<?php
session_start();

// Prevent redirect loop by checking if already on login.php
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['user_id'])) {
    // Check if already logged in as admin
    if ($_SESSION['username'] === 'admin') {
        error_log("login.php: Admin already logged in, redirecting to admin.php");
        header("Location: admin.php");
    } else {
        error_log("login.php: User already logged in, redirecting to user_dashboard.php");
        header("Location: user_dashboard.php");
    }
    exit;
}

$conn = new mysqli("localhost", "root", "", "portfolio");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            error_log("login.php: Login successful, user_id = " . $user['id']);
            
            // Redirect based on username
            if ($user['username'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit;
        } else {
            $error = "Invalid password.";
            error_log("login.php: Invalid password for username = $username");
        }
    } else {
        $error = "No user found.";
        error_log("login.php: No user found for username = $username");
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Login</h2>
        <?php if ($error): ?>
            <p class="text-red-500 mb-4"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="login.php" method="post" class="space-y-4">
            <div>
                <label for="username" class="block text-gray-700 font-bold mb-2">Username</label>
                <input type="text" name="username" id="username" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <div>
                <label for="password" class="block text-gray-700 font-bold mb-2">Password</label>
                <input type="password" name="password" id="password" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Login</button>
        </form>
        <p class="mt-4 text-center text-gray-600">Don't have an account? <a href="register.php" class="text-blue-500 hover:underline">Register</a></p>
    </div>
</body>
</html>