<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_id'])) {
    error_log("upload.php: User not logged in, redirecting to login.php");
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "portfolio");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = intval($_SESSION['user_id']);
$error = "";
$success = "";

// Verify user exists in users table
$sql = "SELECT id FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    error_log("upload.php: User not found for user_id = $user_id");
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}
$stmt->close();

// Handle artwork upload
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image = $_FILES['image'] ?? null;

    $target_dir = "Uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Validate inputs
    if (empty($title)) {
        $error = "Title is required.";
    } elseif (empty($description)) {
        $error = "Description is required.";
    } elseif (!$image || $image['error'] == UPLOAD_ERR_NO_FILE) {
        $error = "No file was uploaded.";
    } else {
        $imageFileType = strtolower(pathinfo($image["name"], PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $max_file_size = 5 * 1024 * 1024; // 5MB
        $new_file_name = $target_dir . "artwork_" . $user_id . "_" . uniqid() . "." . $imageFileType;

        if (!in_array($imageFileType, $allowed_types)) {
            $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        } elseif ($image['size'] > $max_file_size) {
            $error = "File is too large. Maximum size is 5MB.";
        } elseif ($image['error'] != UPLOAD_ERR_OK) {
            $error = "An error occurred while uploading the file.";
        } elseif (!move_uploaded_file($image['tmp_name'], $new_file_name)) {
            $error = "Failed to upload file. Check folder permissions.";
        } else {
            // Insert into artworks table
            $sql = "INSERT INTO artworks (title, description, image_path, user_id) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                $error = "Database error: " . $conn->error;
            } else {
                $stmt->bind_param("sssi", $title, $description, $new_file_name, $user_id);
                if ($stmt->execute()) {
                    $success = "Artwork uploaded successfully!";
                } else {
                    $error = "Error saving artwork to database.";
                    if (file_exists($new_file_name)) {
                        unlink($new_file_name); // Clean up uploaded file on failure
                    }
                }
                $stmt->close();
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Artwork</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Upload Artwork</h1>
            <div class="space-x-4">
                <a href="index.php" class="text-gray-700 hover:text-purple-600 font-medium px-4 py-2 rounded-full border border-gray-300 hover:border-purple-600 transition">Home</a>
                <a href="user_dashboard.php" class="text-gray-700 hover:text-purple-600 font-medium px-4 py-2 rounded-full border border-gray-300 hover:border-purple-600 transition">Dashboard</a>
                <a href="update_profile.php" class="text-gray-700 hover:text-purple-600 font-medium px-4 py-2 rounded-full border border-gray-300 hover:border-purple-600 transition">Update Profile</a>
                <a href="portfolio.php" class="text-gray-700 hover:text-purple-600 font-medium px-4 py-2 rounded-full border border-gray-300 hover:border-purple-600 transition">Portfolio</a>
                <a href="logout.php" class="bg-purple-600 text-white hover:bg-purple-700 font-medium px-4 py-2 rounded-full transition">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Upload Section -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-gray-900 mb-12 text-center">Upload New Artwork</h2>

            <!-- Notifications -->
            <?php if ($error): ?>
                <p class="bg-red-100 text-red-700 p-4 rounded-lg mb-6"><?php echo $error; ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p class="bg-green-100 text-green-700 p-4 rounded-lg mb-6"><?php echo $success; ?></p>
            <?php endif; ?>

            <!-- Upload Form -->
            <div class="bg-white p-6 rounded-xl shadow-lg max-w-lg mx-auto">
                <form action="upload.php" method="post" enctype="multipart/form-data" class="space-y-6">
                    <div>
                        <label for="title" class="block text-gray-700 font-semibold mb-2">Title</label>
                        <input type="text" name="title" id="title" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                    </div>
                    <div>
                        <label for="description" class="block text-gray-700 font-semibold mb-2">Description</label>
                        <textarea name="description" id="description" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" rows="5" required></textarea>
                    </div>
                    <div>
                        <label for="image" class="block text-gray-700 font-semibold mb-2">Image</label>
                        <input type="file" name="image" id="image" accept="image/*" class="w-full" required>
                        <p class="text-sm text-gray-500 mt-1">JPG, JPEG, PNG, GIF (max 5MB)</p>
                    </div>
                    <button type="submit" class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">Upload Artwork</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-lg">© 2025 Artist Portfolio. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>