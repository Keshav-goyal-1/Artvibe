<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_id'])) {
    error_log("update_profile.php: Invalid session, redirecting to login.php");
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

// Fetch current user data
$current_bio = "";
$current_profile_pic = "";
$sql = "SELECT bio, profile_pic FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $current_bio = $row['bio'] ?? "";
    $current_profile_pic = $row['profile_pic'] ?? "";
}
$stmt->close();

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $bio = trim($_POST['bio']);
    $profile_pic = $_FILES['profile_pic'];
    $profile_pic_path = $current_profile_pic;

    // Validate bio
    if (strlen($bio) > 1000) {
        $error = "Bio cannot exceed 1000 characters.";
    } else {
        // Handle profile picture upload
        if ($profile_pic['error'] == UPLOAD_ERR_OK) {
            $target_dir = "Uploads/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $imageFileType = strtolower(pathinfo($profile_pic["name"], PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            $max_file_size = 5 * 1024 * 1024; // 5MB
            $new_file_name = $target_dir . "profile_" . $user_id . "_" . uniqid() . "." . $imageFileType;

            if (!in_array($imageFileType, $allowed_types)) {
                $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
            } elseif ($profile_pic['size'] > $max_file_size) {
                $error = "Profile picture is too large. Maximum size is 5MB.";
            } elseif (!move_uploaded_file($profile_pic['tmp_name'], $new_file_name)) {
                $error = "Failed to upload profile picture. Check folder permissions.";
            } else {
                $profile_pic_path = $new_file_name;
                // Delete old profile picture if it exists
                if ($current_profile_pic && file_exists($current_profile_pic)) {
                    unlink($current_profile_pic);
                }
            }
        } elseif ($profile_pic['error'] != UPLOAD_ERR_NO_FILE) {
            $error = "An error occurred while uploading the profile picture.";
        }

        // Update database if no errors
        if (empty($error)) {
            $sql = "UPDATE users SET bio = ?, profile_pic = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $bio, $profile_pic_path, $user_id);
            if ($stmt->execute()) {
                $success = "Profile updated successfully!";
                $current_bio = $bio;
                $current_profile_pic = $profile_pic_path;
            } else {
                $error = "Error updating profile.";
                if ($profile_pic_path !== $current_profile_pic && file_exists($profile_pic_path)) {
                    unlink($profile_pic_path);
                }
            }
            $stmt->close();
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
    <title>Update Profile</title>
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
    <!-- Navbar -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Update Profile</h1>
            <div class="space-x-4">
                <a href="index.php" class="text-gray-700 hover:text-purple-600 font-medium px-4 py-2 rounded-full border border-gray-300 hover:border-purple-600 transition">Home</a>
                <a href="user_dashboard.php" class="text-gray-700 hover:text-purple-600 font-medium px-4 py-2 rounded-full border border-gray-300 hover:border-purple-600 transition">Dashboard</a>
                <a href="portfolio.php" class="text-gray-700 hover:text-purple-600 font-medium px-4 py-2 rounded-full border border-gray-300 hover:border-purple-600 transition">Portfolio</a>
                <a href="logout.php" class="bg-purple-600 text-white hover:bg-purple-700 font-medium px-4 py-2 rounded-full transition">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Profile Update Section -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-gray-900 mb-12 text-center">Update Your Profile</h2>

            <!-- Notifications -->
            <?php if ($error): ?>
                <p class="bg-red-100 text-red-700 p-4 rounded-lg mb-6"><?php echo $error; ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p class="bg-green-100 text-green-700 p-4 rounded-lg mb-6"><?php echo $success; ?></p>
            <?php endif; ?>

            <!-- Profile Form -->
            <div class="bg-white p-6 rounded-xl shadow-lg max-w-lg mx-auto">
                <form action="update_profile.php" method="post" enctype="multipart/form-data" class="space-y-6">
                    <div>
                        <label for="bio" class="block text-gray-700 font-semibold mb-2">Bio</label>
                        <textarea name="bio" id="bio" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" rows="5" maxlength="1000"><?php echo htmlspecialchars($current_bio); ?></textarea>
                        <p class="text-sm text-gray-500 mt-1">Maximum 1000 characters</p>
                    </div>
                    <div>
                        <label for="profile_pic" class="block text-gray-700 font-semibold mb-2">Profile Picture</label>
                        <?php if ($current_profile_pic): ?>
                            <img src="<?php echo htmlspecialchars($current_profile_pic); ?>" alt="Profile Picture" class="w-24 h-24 rounded-full object-cover mb-2">
                        <?php endif; ?>
                        <input type="file" name="profile_pic" id="profile_pic" accept="image/*" class="w-full">
                        <p class="text-sm text-gray-500 mt-1">JPG, JPEG, PNG, GIF (max 5MB)</p>
                    </div>
                    <button type="submit" name="update_profile" class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">Update Profile</button>
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