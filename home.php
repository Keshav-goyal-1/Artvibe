<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtVibe Studio | Contemporary Art Gallery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
        }
        
        .hero-title {
            font-family: 'Playfair Display', serif;
        }
        
        .modal {
            transition: opacity 0.3s ease, transform 0.3s ease;
            transform: translateY(-20px);
            opacity: 0;
            visibility: hidden;
        }
        
        .modal.active {
            opacity: 1;
            transform: translateY(0);
            visibility: visible;
        }
        
        .artwork-card {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            overflow: hidden;
        }
        
        .artwork-card:hover {
            transform: translateY(-8px);
        }
        
        .artwork-card:hover .artwork-image {
            transform: scale(1.05);
        }
        
        .artwork-image {
            transition: transform 0.5s ease;
        }
        
        .nav-link {
            position: relative;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background-color: currentColor;
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::after {
            width: 100%;
        }
        
        .gradient-overlay {
            background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.7) 100%);
        }
        
        input:focus, textarea:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2);
        }
    </style>
</head>
<body class="bg-gray-50 antialiased">
    <!-- Navigation -->
    <nav class="fixed w-full bg-white/90 backdrop-blur-md border-b border-gray-100 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <a href="#" class="text-2xl font-bold text-gray-900 flex items-center">
                    <span class="text-purple-600">Art</span>Vibe
                </a>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#" class="nav-link text-gray-600 hover:text-purple-600">Home</a>
                    <a href="#gallery" class="nav-link text-gray-600 hover:text-purple-600">Gallery</a>
                    <a href="#artists" class="nav-link text-gray-600 hover:text-purple-600">Artists</a>
                    <a href="#about" class="nav-link text-gray-600 hover:text-purple-600">About</a>
                    
                    <div class="flex space-x-4">
                        <button onclick="showModal('loginModal')" class="text-gray-700 hover:text-purple-600 font-medium px-4 py-2 rounded-lg transition">Sign In</button>
                        <button onclick="showModal('registerModal')" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-medium px-4 py-2 rounded-lg hover:opacity-90 transition">Join Now</button>
                    </div>
                </div>
                
                <button class="md:hidden text-gray-600 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 md:pt-40 md:pb-32">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1579547945413-497e1b99dac0?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80')] bg-cover bg-center opacity-20"></div>
        
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="max-w-3xl">
                <h1 class="hero-title text-5xl md:text-6xl font-bold mb-6 leading-tight text-gray-900">
                    Discover <span class="text-purple-600">Timeless</span> Artistry
                </h1>
                <p class="text-xl text-gray-600 mb-8">
                    Explore contemporary masterpieces from emerging and established artists worldwide. 
                    Curated collections that inspire and transform spaces.
                </p>
                <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                    <button onclick="scrollToGallery()" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-lg font-medium hover:opacity-90 transition">
                        Browse Collection
                    </button>
                    <button class="bg-white text-gray-800 border border-gray-300 px-6 py-3 rounded-lg font-medium hover:bg-gray-50 transition">
                        Meet Our Artists
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Portfolio Gallery -->
    <section id="gallery" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="hero-title text-4xl font-bold mb-4 text-gray-900">Featured <span class="text-purple-600">Artworks</span></h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Curated selection of contemporary pieces from our talented artists</p>
            </div>
            
            <div id="portfolio-gallery" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Artworks will be loaded here via JavaScript -->
                <div class="artwork-card bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="relative h-64 overflow-hidden">
                        <div class="absolute inset-0 bg-gray-200 animate-pulse"></div>
                    </div>
                    <div class="p-6">
                        <div class="h-6 bg-gray-200 rounded w-3/4 mb-2 animate-pulse"></div>
                        <div class="h-4 bg-gray-200 rounded w-full animate-pulse"></div>
                    </div>
                </div>
                <div class="artwork-card bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="relative h-64 overflow-hidden">
                        <div class="absolute inset-0 bg-gray-200 animate-pulse"></div>
                    </div>
                    <div class="p-6">
                        <div class="h-6 bg-gray-200 rounded w-3/4 mb-2 animate-pulse"></div>
                        <div class="h-4 bg-gray-200 rounded w-full animate-pulse"></div>
                    </div>
                </div>
                <div class="artwork-card bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="relative h-64 overflow-hidden">
                        <div class="absolute inset-0 bg-gray-200 animate-pulse"></div>
                    </div>
                    <div class="p-6">
                        <div class="h-6 bg-gray-200 rounded w-3/4 mb-2 animate-pulse"></div>
                        <div class="h-4 bg-gray-200 rounded w-full animate-pulse"></div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-12">
                <button class="bg-white text-purple-600 border border-purple-600 px-6 py-3 rounded-lg font-medium hover:bg-purple-50 transition">
                    View Full Collection
                </button>
            </div>
        </div>
    </section>

    <!-- Artists Section -->
    <section id="artists" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="hero-title text-4xl font-bold mb-4 text-gray-900">Featured <span class="text-purple-600">Artists</span></h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Discover the creative minds behind our exceptional collection</p>
        </div>
        
        <div id="artists-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Loading placeholders (will be replaced by JavaScript) -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden text-center p-6">
                <div class="w-32 h-32 mx-auto rounded-full bg-gray-200 mb-4 overflow-hidden">
                    <div class="w-full h-full bg-gray-300 animate-pulse"></div>
                </div>
                <div class="h-6 bg-gray-200 rounded w-3/4 mx-auto mb-2 animate-pulse"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2 mx-auto mb-4 animate-pulse"></div>
                <div class="h-8 bg-gray-200 rounded w-24 mx-auto animate-pulse"></div>
            </div>
            <!-- Repeat 3 more identical placeholders -->
        </div>
    </div>
</section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="lg:w-1/2">
                    <div class="relative rounded-2xl overflow-hidden shadow-xl h-96">
                        <div class="absolute inset-0 bg-gray-200 animate-pulse">
                            <img src="https://plus.unsplash.com/premium_photo-1667520104627-fd1b3c515bb0?q=80&w=1931&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="">
                        </div>
                    </div>
                </div>
                <div class="lg:w-1/2">
                    <h2 class="hero-title text-4xl font-bold mb-6 text-gray-900">About <span class="text-purple-600">ArtVibe</span></h2>
                    <p class="text-gray-600 mb-6">
                        ArtVibe Studio is a premier online gallery dedicated to showcasing exceptional contemporary art from around the world. 
                        We connect art lovers with talented artists, creating a vibrant community that celebrates creativity.
                    </p>
                    <p class="text-gray-600 mb-8">
                        Founded in 2015, our mission has been to make art accessible to everyone while providing artists with a platform 
                        to showcase their work to a global audience.
                    </p>
                    <button class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-lg font-medium hover:opacity-90 transition">
                        Learn More About Us
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Login Modal -->
    <div id="loginModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Welcome Back</h2>
                    <button onclick="closeModal('loginModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form action="login.php" method="post" class="space-y-6">
                    <div>
                        <label for="login-username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" name="username" id="login-username" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" required>
                    </div>
                    <div>
                        <label for="login-password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" id="login-password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" required>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" id="remember" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                        </div>
                        <a href="#" class="text-sm text-purple-600 hover:text-purple-800">Forgot password?</a>
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-4 py-3 rounded-lg font-medium hover:opacity-90 transition">
                        Sign In
                    </button>
                </form>
                
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Or continue with</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <button class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center justify-center">
                            <i class="fab fa-google text-red-500 mr-2"></i> Google
                        </button>
                        <button class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center justify-center">
                            <i class="fab fa-facebook text-blue-600 mr-2"></i> Facebook
                        </button>
                    </div>
                </div>
                
                <p class="mt-6 text-center text-sm text-gray-600">
                    Don't have an account? 
                    <button onclick="switchModal('loginModal', 'registerModal')" class="text-purple-600 font-medium hover:text-purple-800">Sign up</button>
                </p>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 modal">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Create Account</h2>
                    <button onclick="closeModal('registerModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form action="register.php" method="post" class="space-y-6">
                    <div>
                        <label for="register-username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" name="username" id="register-username" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" required>
                    </div>
                    <div>
                        <label for="register-email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" id="register-email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" required>
                    </div>
                    <div>
                        <label for="register-password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" id="register-password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" required>
                    </div>
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500" required>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="terms" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded" required>
                        <label for="terms" class="ml-2 block text-sm text-gray-700">
                            I agree to the <a href="#" class="text-purple-600 hover:text-purple-800">Terms of Service</a> and <a href="#" class="text-purple-600 hover:text-purple-800">Privacy Policy</a>
                        </label>
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-4 py-3 rounded-lg font-medium hover:opacity-90 transition">
                        Create Account
                    </button>
                </form>
                
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">Or sign up with</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <button class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center justify-center">
                            <i class="fab fa-google text-red-500 mr-2"></i> Google
                        </button>
                        <button class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center justify-center">
                            <i class="fab fa-facebook text-blue-600 mr-2"></i> Facebook
                        </button>
                    </div>
                </div>
                
                <p class="mt-6 text-center text-sm text-gray-600">
                    Already have an account? 
                    <button onclick="switchModal('registerModal', 'loginModal')" class="text-purple-600 font-medium hover:text-purple-800">Sign in</button>
                </p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                <div>
                    <h3 class="text-xl font-bold mb-4">ArtVibe Studio</h3>
                    <p class="text-gray-400">Connecting art lovers with exceptional contemporary artists from around the world.</p>
                    <div class="flex space-x-4 mt-6">
                        <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Explore</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Featured Artists</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">New Arrivals</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Collections</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Exhibitions</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Resources</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Art Buying Guide</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Artist Submissions</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Careers</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Press</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact</h4>
                    <ul class="space-y-2">
                        <li class="text-gray-400">123 Art Street, Creative City</li>
                        <li class="text-gray-400">hello@artvibe.com</li>
                        <li class="text-gray-400">+1 (555) 123-4567</li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm mb-4 md:mb-0">© 2025 ArtVibe Studio. All rights reserved.</p>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition">Privacy Policy</a>
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition">Terms of Service</a>
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition">Cookies</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Modal handling
        function showModal(modalId) {
            document.body.style.overflow = 'hidden';
            document.getElementById(modalId).classList.add('active');
        }

        function closeModal(modalId) {
            document.body.style.overflow = '';
            document.getElementById(modalId).classList.remove('active');
        }

        function switchModal(fromModal, toModal) {
            closeModal(fromModal);
            setTimeout(() => showModal(toModal), 300);
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                document.body.style.overflow = '';
                document.querySelectorAll('.modal').forEach(modal => {
                    modal.classList.remove('active');
                });
            }
        }

        // Scroll to gallery
        function scrollToGallery() {
            document.getElementById('gallery').scrollIntoView({ behavior: 'smooth' });
        }

        // Fetch artworks
        document.addEventListener("DOMContentLoaded", function() {
            fetch("get_artworks.php")
                .then(response => response.json())
                .then(data => {
                    const gallery = document.getElementById("portfolio-gallery");
                    gallery.innerHTML = ''; // Clear loading placeholders
                    
                    data.forEach(artwork => {
                        const card = document.createElement("div");
                        card.className = "artwork-card bg-white rounded-xl shadow-md overflow-hidden";
                        card.innerHTML = `
                            <div class="relative h-80 overflow-hidden">
                                <img src="${artwork.image_path}" alt="${artwork.title}" class="artwork-image w-full h-full object-cover">
                                <div class="absolute inset-0 gradient-overlay flex items-end p-6">
                                    <h3 class="text-xl font-bold text-white">${artwork.title}</h3>
                                </div>
                            </div>
                            <div class="p-6">
                                <p class="text-gray-600 mb-4">${artwork.description}</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">${new Date(artwork.created_at).toLocaleDateString()}</span>
                                    <button class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                        View Details
                                    </button>
                                </div>
                            </div>
                        `;
                        gallery.appendChild(card);
                    });
                })
                .catch(error => console.error("Error fetching artworks:", error));
        });
        document.addEventListener("DOMContentLoaded", function() {
    fetchArtists();
});

document.addEventListener("DOMContentLoaded", function() {
    fetchArtists();
});
function fetchArtists() {
    const container = document.getElementById('artists-container');
    
    // Show loading state
    container.innerHTML = `
        <div class="col-span-full text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-purple-600 mb-4"></div>
            <p class="text-gray-600">Loading artists...</p>
        </div>
    `;

    fetch('api/artists.php')
        .then(response => {
            // First check if the response is OK (status 200-299)
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            // Check content type to ensure it's JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error("Response wasn't JSON");
            }
            
            return response.json();
        })
        .then(data => {
            // Clear the container
            container.innerHTML = '';
            
            // Check if we actually got artists data
            if (!data.artists || data.artists.length === 0) {
                container.innerHTML = `
                    <div class="col-span-full text-center py-8">
                        <p class="text-gray-600">No featured artists at this time</p>
                    </div>
                `;
                return;
            }
            
            // Render artists
            data.artists.forEach(artist => {
                const artistCard = document.createElement('div');
                artistCard.className = 'bg-white rounded-xl shadow-md overflow-hidden text-center p-6 hover:shadow-lg transition-all duration-300';
                artistCard.innerHTML = `
                    <div class="w-32 h-32 mx-auto rounded-full bg-gray-200 mb-4 overflow-hidden">
                        <img src="${artist.image_url || 'images/default-artist.jpg'}" 
                             alt="${artist.name}" 
                             class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                             onerror="this.src='images/default-artist.jpg'">
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">${artist.name}</h3>
                    <p class="text-gray-600 mb-4">${artist.specialty || 'Visual Artist'}</p>
                    <button onclick="viewArtist(${artist.id})" 
                            class="text-purple-600 hover:text-purple-800 text-sm font-medium transition-colors">
                        View Portfolio
                    </button>
                `;
                container.appendChild(artistCard);
            });
        })
        .catch(error => {
            console.error('Error fetching artists:', error);
            container.innerHTML = `
                <div class="col-span-full text-center py-8">
                    <p class="text-red-500">Failed to load artists. Please try again later.</p>
                    <button onclick="fetchArtists()" 
                            class="mt-4 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        Retry
                    </button>
                </div>
            `;
        });
}6+

function viewArtist(artistId) {
    window.location.href = `artist.php?id=${artistId}`;
}
    </script>
</body>
</html>