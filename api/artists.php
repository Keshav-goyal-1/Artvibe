<?php
// api/artists.php
// Prevent PHP from displaying errors directly
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Always set content type to JSON
header('Content-Type: application/json');

// Mock data as fallback
$mockArtists = [
    [
        'id' => 1,
        'name' => 'Michael',
        'specialty' => 'Abstract Expressionism',
        'image_url' => 'https://plus.unsplash.com/premium_photo-1671656349322-41de944d259b?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
    ],
    [
        'id' => 2,
        'name' => 'Jane Smith',
        'specialty' => 'Contemporary Art',
        'image_url' => 'https://images.unsplash.com/photo-1480429370139-e0132c086e2a?q=80&w=1976&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
    ],
    [
        'id' => 3,
        'name' => 'John doe',
        'specialty' => 'Digital Art & Photography',
        'image_url' => 'https://plus.unsplash.com/premium_photo-1664533227571-cb18551cac82?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
    ],
    [
        'id' => 4,
        'name' => 'Emily Johnson',
        'specialty' => 'Sculpture & Installation',
        'image_url' => 'https://images.unsplash.com/photo-1612643502853-ac8808042d1b?q=80&w=2067&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
    ]
    // Add more mock data as needed
];

$response = [];

try {
    // Try to include the config file
    if (!file_exists('../config.php')) {
        throw new Exception("Config file not found");
    }
    
    require_once '../config.php';
    
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception("Database connection failed");
    }
    
    $query = "SELECT * FROM artists WHERE is_featured = 1 ORDER BY name ASC";
    $result = $conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        $artists = [];
        while ($row = $result->fetch_assoc()) {
            $artists[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'specialty' => $row['specialty'],
                'image_url' => $row['image_url'] ?: 'images/default-artist.jpg',
                'bio' => $row['bio']
            ];
        }
        $response['success'] = true;
        $response['artists'] = $artists;
    } else {
        // No artists found - use mock data
        $response['success'] = true;
        $response['artists'] = $mockArtists;
        $response['note'] = "Using sample data (no artists in database)";
    }
    
    if (isset($conn)) {
        $conn->close();
    }
} catch (Exception $e) {
    // Log the error to a file instead of displaying it
    error_log("Artists API error: " . $e->getMessage());
    
    // Return mock data with error note
    $response['success'] = true; // Still return success to avoid breaking the frontend
    $response['artists'] = $mockArtists;
    $response['note'] = "Using sample data (API error occurred)";
}

echo json_encode($response);
exit;
?>