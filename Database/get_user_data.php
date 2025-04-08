<?php
// Initialize the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // Not logged in
    echo json_encode(["loggedin" => false]);
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "Kushagra@123";
$dbname = "picknpack_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID from session
$user_id = $_SESSION["id"];

// Get user profile data
$sql = "SELECT u.name, u.email, p.phone, p.address, p.city, p.state, p.postal_code 
        FROM users u 
        LEFT JOIN user_profiles p ON u.id = p.user_id 
        WHERE u.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Add loggedin status to the response
    $row["loggedin"] = true;
    
    // Return data as JSON
    echo json_encode($row);
} else {
    // User not found (unlikely if session is set)
    echo json_encode([
        "loggedin" => true,
        "name" => $_SESSION["name"],
        "email" => $_SESSION["email"]
    ]);
}

$stmt->close();
$conn->close();
?>