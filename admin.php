<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "portfolio");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch users
$users = [];
$user_sql = "SELECT id, username, created_at FROM users";
$user_result = $conn->query($user_sql);
if ($user_result->num_rows > 0) {
    while ($row = $user_result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Fetch artworks
$artworks = [];
$artwork_sql = "SELECT id, title, description, image_path FROM artworks";
$artwork_result = $conn->query($artwork_sql);
if ($artwork_result->num_rows > 0) {
    while ($row = $artwork_result->fetch_assoc()) {
        $artworks[] = $row;
    }
}

// Fetch user registration data for chart (users per month)
$chart_data = [];
$chart_sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS user_count FROM users GROUP BY month ORDER BY month";
$chart_result = $conn->query($chart_sql);
if ($chart_result->num_rows > 0) {
    while ($row = $chart_result->fetch_assoc()) {
        $chart_data[] = $row;
    }
}

// Handle user deletion
if (isset($_GET['delete_user'])) {
    $user_id = intval($_GET['delete_user']);
    $delete_sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php");
    exit;
}

// Handle artwork deletion
if (isset($_GET['delete_artwork'])) {
    $artwork_id = intval($_GET['delete_artwork']);
    // Get image path to delete file
    $sql = "SELECT image_path FROM artworks WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $artwork_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        unlink($row['image_path']); // Delete image file
    }
    $stmt->close();
    // Delete from database
    $delete_sql = "DELETE FROM artworks WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $artwork_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php");
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Admin Portal</h1>
            <div class="space-x-4">
                <a href="index.html" class="text-gray-700 hover:text-purple-600 font-medium px-4 py-2 rounded-full border border-gray-300 hover:border-purple-600 transition">Home</a>
                <a href="logout.php" class="bg-purple-600 text-white hover:bg-purple-700 font-medium px-4 py-2 rounded-full transition">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Dashboard -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-gray-900 mb-12 text-center">Admin Dashboard</h2>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                <div class="bg-white p-6 rounded-xl shadow-lg card-hover">
                    <h3 class="text-xl font-semibold text-gray-900">Total Users</h3>
                    <p class="text-3xl font-bold text-purple-600 mt-2"><?php echo count($users); ?></p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg card-hover">
                    <h3 class="text-xl font-semibold text-gray-900">Total Artworks</h3>
                    <p class="text-3xl font-bold text-purple-600 mt-2"><?php echo count($artworks); ?></p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg card-hover">
                    <h3 class="text-xl font-semibold text-gray-900">Last Login</h3>
                    <p class="text-lg text-gray-600 mt-2"><?php echo date('Y-m-d H:i:s'); ?></p>
                </div>
            </div>

            <!-- User Registration Chart -->
            <div class="bg-white p-6 rounded-xl shadow-lg mb-12">
                <h3 class="text-2xl font-semibold text-gray-900 mb-6">User Registrations Over Time</h3>
                <canvas id="userChart" class="w-full h-64"></canvas>
            </div>

            <!-- User Management -->
            <div class="bg-white p-6 rounded-xl shadow-lg mb-12">
                <h3 class="text-2xl font-semibold text-gray-900 mb-6">Manage Users</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $user['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?php echo $user['created_at']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="admin.php?delete_user=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');" class="text-red-600 hover:text-red-800">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Artwork Management -->
            <div class="bg-white p-6 rounded-xl shadow-lg mb-12">
                <h3 class="text-2xl font-semibold text-gray-900 mb-6">Manage Artworks</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($artworks as $artwork): ?>
                        <div class="bg-gray-50 rounded-lg shadow-md card-hover">
                            <img src="<?php echo htmlspecialchars($artwork['image_path']); ?>" alt="<?php echo htmlspecialchars($artwork['title']); ?>" class="w-full h-48 object-cover rounded-t-lg">
                            <div class="p-4">
                                <h4 class="text-lg font-bold text-gray-900"><?php echo htmlspecialchars($artwork['title']); ?></h4>
                                <p class="text-gray-600"><?php echo htmlspecialchars($artwork['description']); ?></p>
                                <a href="admin.php?delete_artwork=<?php echo $artwork['id']; ?>" onclick="return confirm('Are you sure you want to delete this artwork?');" class="mt-2 inline-block text-red-600 hover:text-red-800">Delete</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Chart.js for user registration over time
        document.addEventListener("DOMContentLoaded", function() {
            const chartData = <?php echo json_encode($chart_data); ?>;
            const labels = chartData.map(item => item.month);
            const data = chartData.map(item => item.user_count);

            const ctx = document.getElementById('userChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Users Registered',
                        data: data,
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.2)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Users'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Month'
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>