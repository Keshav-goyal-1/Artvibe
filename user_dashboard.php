<?php
session_start();

// Debug session
error_log("Session data: " . print_r($_SESSION, true));

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_id'])) {
    error_log("Redirecting to login.php: user_id not set");
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "portfolio");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$error = "";
$success = "";

// Predefined portfolio templates
$templates = [
    'modern' => ['name' => 'Modern', 'description' => 'Sleek, minimalist design with bold visuals.'],
    'creative' => ['name' => 'Creative', 'description' => 'Vibrant, colorful layout for artistic portfolios.'],
    'professional' => ['name' => 'Professional', 'description' => 'Clean, structured design for formal portfolios.']
];

// Handle skill addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_skill'])) {
    $skill_name = trim($_POST['skill_name']);
    if (!empty($skill_name)) {
        $sql = "INSERT INTO skills (user_id, skill_name) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $skill_name);
        if ($stmt->execute()) {
            $success = "Skill added successfully!";
        } else {
            $error = "Error adding skill.";
        }
        $stmt->close();
    } else {
        $error = "Skill name is required.";
    }
}

// Handle template selection
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['select_template'])) {
    $template_name = $_POST['template_name'];
    if (array_key_exists($template_name, $templates)) {
        $sql = "SELECT id FROM portfolios WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $sql = "UPDATE portfolios SET template_name = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $template_name, $user_id);
        } else {
            $sql = "INSERT INTO portfolios (user_id, template_name) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $user_id, $template_name);
        }
        if ($stmt->execute()) {
            $success = "Template selected successfully!";
        } else {
            $error = "Error selecting template.";
        }
        $stmt->close();
    } else {
        $error = "Invalid template selected.";
    }
}

// Handle artwork upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_artwork'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $image = $_FILES["image"];
    $imageFileType = strtolower(pathinfo($image["name"], PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    $max_file_size = 5 * 1024 * 1024; // 5MB
    $new_file_name = $target_dir . uniqid() . "." . $imageFileType;

    if (empty($title) || empty($description)) {
        $error = "Title and description are required.";
    } elseif ($image["error"] == UPLOAD_ERR_NO_FILE) {
        $error = "No file was uploaded.";
    } elseif (!in_array($imageFileType, $allowed_types)) {
        $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
    } elseif ($image["size"] > $max_file_size) {
        $error = "File is too large. Maximum size is 5MB.";
    } elseif ($image["error"] != UPLOAD_ERR_OK) {
        $error = "An error occurred while uploading the file.";
    } elseif (move_uploaded_file($image["tmp_name"], $new_file_name)) {
        $sql = "INSERT INTO artworks (title, description, image_path, user_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $title, $description, $new_file_name, $user_id);
        if ($stmt->execute()) {
            $success = "Artwork uploaded successfully!";
        } else {
            $error = "Error saving to database.";
            unlink($new_file_name);
        }
        $stmt->close();
    } else {
        $error = "Failed to upload file. Check folder permissions.";
    }
}

// Fetch user's skills
$skills = [];
$skill_sql = "SELECT id, skill_name FROM skills WHERE user_id = ?";
$stmt = $conn->prepare($skill_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $skills[] = $row;
}
$stmt->close();

// Fetch user's artworks
$artworks = [];
$artwork_sql = "SELECT id, title, description, image_path FROM artworks WHERE user_id = ?";
$stmt = $conn->prepare($artwork_sql);
if ($stmt === false) {
    die("Error preparing artwork query: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $artworks[] = $row;
}
$stmt->close();

// Fetch user's portfolio template
$selected_template = null;
$portfolio_sql = "SELECT template_name FROM portfolios WHERE user_id = ?";
$stmt = $conn->prepare($portfolio_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $selected_template = $row['template_name'];
}
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .template-card {
            cursor: pointer;
            transition: border-color 0.3s ease;
        }
        .template-card.selected {
            border-color: #8b5cf6;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">User Dashboard</h1>
            <!-- <div class="space-x-4">
                <a href="index.html" class="text-gray-700 hover:text-purple-600 font-medium px-4 py-2 rounded-full border border-gray-300 hover:border-purple-600 transition">Home</a>
                <a href="portfolio.php" class="text-gray-700 hover:text-purple-600 font-medium px-4 py-2 rounded-full border border-gray-300 hover:border-purple-600 transition">View Portfolio</a>
                <a href="logout.php" class="bg-purple-600 text-white hover:bg-purple-700 font-medium px-4 py-2 rounded-full transition">Logout</a>
            </div> -->
            <div class="space-x-4">
    <a href="index.php" class="text-gray-700 hover:text-purple-600 font-medium px-4 py-2 rounded-full border border-gray-300 hover:border-purple-600 transition">Home</a>
    <a href="update_profile.php" class="text-gray-700 hover:text-purple-600 font-medium px-4 py-2 rounded-full border border-gray-300 hover:border-purple-600 transition">Update Profile</a>
    <a href="portfolio.php" class="text-gray-700 hover:text-purple-600 font-medium px-4 py-2 rounded-full border border-gray-300 hover:border-purple-600 transition">View Portfolio</a>
    <a href="logout.php" class="bg-purple-600 text-white hover:bg-purple-700 font-medium px-4 py-2 rounded-full transition">Logout</a>
</div>
        </div>
    </nav>

    <!-- Dashboard -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-gray-900 mb-12 text-center">Build Your Portfolio</h2>

            <!-- Notifications -->
            <?php if ($error): ?>
                <p class="bg-red-100 text-red-700 p-4 rounded-lg mb-6"><?php echo $error; ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p class="bg-green-100 text-green-700 p-4 rounded-lg mb-6"><?php echo $success; ?></p>
            <?php endif; ?>

            <!-- Add Skills -->
            <div class="bg-white p-6 rounded-xl shadow-lg mb-12">
                <h3 class="text-2xl font-semibold text-gray-900 mb-6">Add Skills</h3>
                <form action="user_dashboard.php" method="post" class="max-w-lg mx-auto space-y-4">
                    <div>
                        <label for="skill_name" class="block text-gray-700 font-semibold mb-2">Skill Name</label>
                        <input type="text" name="skill_name" id="skill_name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                    </div>
                    <button type="submit" name="add_skill" class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">Add Skill</button>
                </form>
                <div class="mt-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Your Skills</h4>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($skills as $skill): ?>
                            <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm"><?php echo htmlspecialchars($skill['skill_name']); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Select Portfolio Template -->
            <div class="bg-white p-6 rounded-xl shadow-lg mb-12">
                <h3 class="text-2xl font-semibold text-gray-900 mb-6">Choose a Portfolio Template</h3>
                <form action="user_dashboard.php" method="post">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <?php foreach ($templates as $key => $template): ?>
                            <div class="template-card bg-gray-50 p-4 rounded-lg border-2 <?php echo $selected_template === $key ? 'border-purple-600' : 'border-gray-200'; ?> card-hover">
                                <h4 class="text-lg font-bold text-gray-900"><?php echo htmlspecialchars($template['name']); ?></h4>
                                <p class="text-gray-600"><?php echo htmlspecialchars($template['description']); ?></p>
                                <input type="radio" name="template_name" value="<?php echo $key; ?>" class="hidden" <?php echo $selected_template === $key ? 'checked' : ''; ?> required>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" name="select_template" class="mt-6 w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">Save Template</button>
                </form>
            </div>

            <!-- Upload Artwork -->
            <div class="bg-white p-6 rounded-xl shadow-lg mb-12">
                <h3 class="text-2xl font-semibold text-gray-900 mb-6">Upload Artwork</h3>
                <form action="user_dashboard.php" method="post" enctype="multipart/form-data" class="max-w-lg mx-auto space-y-4">
                    <div>
                        <label for="title" class="block text-gray-700 font-semibold mb-2">Title</label>
                        <input type="text" name="title" id="title" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                    </div>
                    <div>
                        <label for="description" class="block text-gray-700 font-semibold mb-2">Description</label>
                        <textarea name="description" id="description" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" required></textarea>
                    </div>
                    <div>
                        <label for="image" class="block text-gray-700 font-semibold mb-2">Image</label>
                        <input type="file" name="image" id="image" accept="image/*" class="w-full" required>
                    </div>
                    <button type="submit" name="upload_artwork" class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">Upload</button>
                </form>
            </div>

            <!-- Manage Artworks -->
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h3 class="text-2xl font-semibold text-gray-900 mb-6">Your Artworks</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($artworks as $artwork): ?>
                        <div class="bg-gray-50 rounded-lg shadow-md card-hover">
                            <img src="<?php echo htmlspecialchars($artwork['image_path']); ?>" alt="<?php echo htmlspecialchars($artwork['title']); ?>" class="w-full h-48 object-cover rounded-t-lg">
                            <div class="p-4">
                                <h4 class="text-lg font-bold text-gray-900"><?php echo htmlspecialchars($artwork['title']); ?></h4>
                                <p class="text-gray-600"><?php echo htmlspecialchars($artwork['description']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Handle template selection
        document.querySelectorAll('.template-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.template-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                this.querySelector('input[type="radio"]').checked = true;
            });
        });
    </script>
</body>
</html>