<?php
header('Content-Type: application/json');

// Database connection (adjust credentials accordingly)
$conn = new mysqli('localhost', 'username', 'password', 'flypped_blog');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = $_GET['query'] ?? '';
$tags = [];

if (!empty($query)) {
    // Fetch matching tags from the database
    $stmt = $conn->prepare("SELECT name FROM wp_terms WHERE name LIKE ?");
    $searchTerm = "%" . $query . "%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $tags[] = $row['name'];
    }
    
    $stmt->close();
}

echo json_encode($tags);
$conn->close();
?>
