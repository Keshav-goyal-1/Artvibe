<?php
session_start();
$conn = new mysqli("localhost", "root", "", "portfolio");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_id'])) {
    error_log("portfolio.php: Invalid session, redirecting to login.php");
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "portfolio");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$user_id = $_SESSION['user_id'];

// Fetch user details
// $user = null;
// $sql = "SELECT username, bio, profile_pic FROM users WHERE id = ?";
// // $sql = "SELECT id, title, description, image_path, created_at FROM artworks WHERE user_id = ?";
// $stmt = $conn->prepare($sql);
// $stmt->bind_param("i", $user_id);
// $stmt->execute();
// $result = $stmt->get_result();
// if ($row = $result->fetch_assoc()) {
//     $user = $row;
// }
// $stmt->close();
// if (!$user) {
//     die("User not found.");
// }




// In your PHP code (portfolio.php):

// Fetch user details including profile picture
$user = null;
$sql = "SELECT username, bio, profile_pic FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $user = $row;
}
$stmt->close();

// Fetch artworks (projects) with their images
$artworks = [];
$sql = "SELECT id, title, description, image_path FROM artworks WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $artworks[] = $row;
}
$stmt->close();




// Fetch portfolio template
$template = 'modern'; // Default
$sql = "SELECT template_name FROM portfolios WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $template = $row['template_name'];
}
$stmt->close();

// Fetch skills
$skills = [];
$sql = "SELECT skill_name FROM skills WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $skills[] = $row;
}
$stmt->close();

// Fetch artworks
$artworks = [];
$sql = "SELECT title, description, image_path FROM artworks WHERE user_id = ? ";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $artworks[] = $row;
}
$stmt->close();

$conn->close();

// Template styles
$template_styles = [
    'modern' => [
        'bg' => 'bg-gradient-to-b from-gray-50 to-gray-100',
        'card' => 'bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300',
        'text' => 'text-gray-800',
        'primary' => 'text-indigo-600',
        'button' => 'bg-indigo-600 hover:bg-indigo-700 text-white',
        'border' => 'border-gray-200'
    ],
    'creative' => [
        'bg' => 'bg-gradient-to-br from-purple-50 to-blue-50',
        'card' => 'bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 border-l-4 border-purple-500',
        'text' => 'text-gray-800',
        'primary' => 'text-purple-600',
        'button' => 'bg-purple-600 hover:bg-purple-700 text-white',
        'border' => 'border-purple-100'
    ],
    'professional' => [
        'bg' => 'bg-gray-50',
        'card' => 'bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-300 border border-gray-200',
        'text' => 'text-gray-800',
        'primary' => 'text-blue-600',
        'button' => 'bg-blue-600 hover:bg-blue-700 text-white',
        'border' => 'border-gray-200'
    ]
];
$style = $template_styles[$template];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['username']); ?> | Portfolio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
        }
        
        .nav-link {
            position: relative;
        }
        
        .nav-link:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: currentColor;
            transition: width 0.3s ease;
        }
        
        .nav-link:hover:after {
            width: 100%;
        }
        
        .project-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .skill-pill {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        /* Mobile menu animation */
        .mobile-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        
        .mobile-menu.open {
            max-height: 500px;
            transition: max-height 0.5s ease-in;
        }
    </style>
</head>
<body class="<?php echo $style['bg']; ?> min-h-screen text-gray-800 antialiased">
    <!-- Navigation -->
    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b <?php echo $style['border']; ?>">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <a href="#" class="text-2xl font-bold <?php echo $style['primary']; ?>">
                    <?php echo htmlspecialchars($user['username']); ?>
                </a>
                
                <div class="hidden md:flex items-center space-x-6">
                    <a href="#home" class="nav-link text-gray-600 hover:<?php echo $style['primary']; ?>">Home</a>
                    <a href="#skills" class="nav-link text-gray-600 hover:<?php echo $style['primary']; ?>">Skills</a>
                    <a href="#projects" class="nav-link text-gray-600 hover:<?php echo $style['primary']; ?>">Projects</a>
                    <a href="#contact" class="nav-link text-gray-600 hover:<?php echo $style['primary']; ?>">Contact</a>
                    
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id): ?>
                        <a href="user_dashboard.php" class="<?php echo $style['button']; ?> px-4 py-2 rounded-md text-sm font-medium transition-colors">Dashboard</a>
                        <a href="logout.php" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-md text-sm font-medium transition-colors">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="<?php echo $style['button']; ?> px-4 py-2 rounded-md text-sm font-medium transition-colors">Login</a>
                    <?php endif; ?>
                </div>
                
                <button id="mobile-menu-button" class="md:hidden text-gray-600 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Mobile menu -->
            <div id="mobile-menu" class="mobile-menu md:hidden px-4">
                <div class="flex flex-col space-y-3 pb-4">
                    <a href="#home" class="nav-link text-gray-600 hover:<?php echo $style['primary']; ?> py-2">Home</a>
                    <a href="#skills" class="nav-link text-gray-600 hover:<?php echo $style['primary']; ?> py-2">Skills</a>
                    <a href="#projects" class="nav-link text-gray-600 hover:<?php echo $style['primary']; ?> py-2">Projects</a>
                    <a href="#contact" class="nav-link text-gray-600 hover:<?php echo $style['primary']; ?> py-2">Contact</a>
                    
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id): ?>
                        <a href="user_dashboard.php" class="<?php echo $style['button']; ?> px-4 py-2 rounded-md text-sm font-medium text-center mt-2">Dashboard</a>
                        <a href="logout.php" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-md text-sm font-medium text-center">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="<?php echo $style['button']; ?> px-4 py-2 rounded-md text-sm font-medium text-center mt-2">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center gap-12">
                <div class="md:w-1/2 order-2 md:order-1">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">
                        Hi, I'm <span class="<?php echo $style['primary']; ?>"><?php echo htmlspecialchars($user['username']); ?></span>
                    </h1>
                    <p class="text-lg text-gray-600 mb-8">
                        <?php echo htmlspecialchars($user['bio'] ?? 'Passionate creator dedicated to crafting beautiful digital experiences.'); ?>
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="#projects" class="<?php echo $style['button']; ?> px-6 py-3 rounded-md font-medium transition-colors hover:shadow-md">View My Work</a>
                        <a href="#contact" class="bg-white text-gray-800 border border-gray-300 hover:bg-gray-50 px-6 py-3 rounded-md font-medium transition-colors hover:shadow-md">Contact Me</a>
                    </div>
                </div>
                
                <div class="md:w-1/2 order-1 md:order-2 mb-10 md:mb-0">
                    <div class="relative">
                        <div class="absolute -inset-4 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-2xl opacity-20 blur"></div>
                        <?php if (!empty($user['profile_pic'])): ?>
                            <img src="<?php echo htmlspecialchars($user['profile_pic']); ?>" 
                                 alt="<?php echo htmlspecialchars($user['username']); ?>'s profile picture" 
                                 class="relative w-full h-auto rounded-2xl shadow-xl object-cover aspect-square">
                        <?php else: ?>
                            <div class="relative w-full bg-gray-200 rounded-2xl shadow-xl flex items-center justify-center aspect-square">
                                <span class="text-gray-500">No profile image</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Skills Section -->
    <section id="skills" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">My <span class="<?php echo $style['primary']; ?>">Skills</span></h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Here are the technologies and tools I'm proficient in.</p>
            </div>
            
            <div class="flex flex-wrap justify-center gap-3 max-w-3xl mx-auto">
                <?php foreach ($skills as $skill): ?>
                    <span class="skill-pill <?php echo $style['primary']; ?> bg-opacity-10 hover:bg-opacity-20 <?php echo $style['primary']; ?>">
                        <?php echo htmlspecialchars($skill['skill_name']); ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Projects Section -->
    <section id="projects" class="py-16 <?php echo $style['bg']; ?>">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">Featured <span class="<?php echo $style['primary']; ?>">Projects</span></h2>
                <p class="text-gray-600 max-w-2xl mx-auto">A selection of my recent work and creative projects.</p>
            </div>
            
            <?php if (!empty($artworks)): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($artworks as $artwork): ?>
                        <div class="<?php echo $style['card']; ?> project-card overflow-hidden h-full flex flex-col">
                            <div class="relative overflow-hidden aspect-video">
                                <?php if (!empty($artwork['image_path'])): ?>
                                    <img src="<?php echo htmlspecialchars($artwork['image_path']); ?>" 
                                         alt="<?php echo htmlspecialchars($artwork['title']); ?>" 
                                         class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                <?php else: ?>
                                    <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                        <span class="text-gray-500">Project image</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="p-6 flex-grow">
                                <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($artwork['title']); ?></h3>
                                <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($artwork['description']); ?></p>
                            </div>
                            <div class="px-6 pb-6">
                                <a href="#" class="inline-flex items-center text-sm font-medium <?php echo $style['primary']; ?> hover:underline">
                                    View Project
                                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <p class="text-gray-500">No projects to display yet. Check back later!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold mb-4">Get In <span class="<?php echo $style['primary']; ?>">Touch</span></h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Have a project in mind or want to collaborate? Feel free to reach out.</p>
            </div>
            
            <div class="max-w-3xl mx-auto <?php echo $style['card']; ?> p-6 md:p-8 shadow-sm hover:shadow-md transition-shadow">
                <form class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Your Name</label>
                            <input type="text" id="name" class="w-full px-4 py-2 border <?php echo $style['border']; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" id="email" class="w-full px-4 py-2 border <?php echo $style['border']; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                    </div>
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                        <input type="text" id="subject" class="w-full px-4 py-2 border <?php echo $style['border']; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                        <textarea id="message" rows="4" class="w-full px-4 py-2 border <?php echo $style['border']; ?> rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"></textarea>
                    </div>
                    <button type="submit" class="<?php echo $style['button']; ?> px-6 py-3 rounded-md font-medium w-full hover:shadow-md transition-all">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-6 md:mb-0 text-center md:text-left">
                    <a href="#" class="text-2xl font-bold text-white hover:opacity-80 transition"><?php echo htmlspecialchars($user['username']); ?></a>
                    <p class="text-gray-400 mt-2">Creating digital experiences that matter</p>
                </div>
                
                <div class="flex space-x-6 mb-6 md:mb-0">
                    <a href="#" class="text-gray-400 hover:text-white transition" aria-label="Twitter">
                        <i class="fab fa-twitter text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition" aria-label="GitHub">
                        <i class="fab fa-github text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition" aria-label="LinkedIn">
                        <i class="fab fa-linkedin text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition" aria-label="Instagram">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm mb-4 md:mb-0">© <?php echo date('Y'); ?> <?php echo htmlspecialchars($user['username']); ?>. All rights reserved.</p>
                
                <div class="flex flex-wrap justify-center gap-x-6 gap-y-2">
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition">Privacy Policy</a>
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition">Terms</a>
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition">Cookies</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('open');
        });

        // Close mobile menu when clicking a link
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('open');
            });
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>