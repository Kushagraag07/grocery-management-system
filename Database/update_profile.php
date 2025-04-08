<?php
// Initialize the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.html");
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

// Process form data when submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = htmlspecialchars($_POST["name"]);
    $phone = htmlspecialchars($_POST["phone"]);
    $user_id = $_SESSION["id"];
    
    // Update user name in users table
    $update_user_sql = "UPDATE users SET name = ? WHERE id = ?";
    $update_user_stmt = $conn->prepare($update_user_sql);
    $update_user_stmt->bind_param("si", $name, $user_id);
    $update_user_stmt->execute();
    $update_user_stmt->close();
    
    // Update session name
    $_SESSION["name"] = $name;
    
    // Check if user profile exists
    $check_profile_sql = "SELECT id FROM user_profiles WHERE user_id = ?";
    $check_profile_stmt = $conn->prepare($check_profile_sql);
    $check_profile_stmt->bind_param("i", $user_id);
    $check_profile_stmt->execute();
    $check_profile_result = $check_profile_stmt->get_result();
    
    if ($check_profile_result->num_rows > 0) {
        // Update existing profile
        $update_profile_sql = "UPDATE user_profiles SET phone = ? WHERE user_id = ?";
        $update_profile_stmt = $conn->prepare($update_profile_sql);
        $update_profile_stmt->bind_param("si", $phone, $user_id);
        $update_profile_stmt->execute();
        $update_profile_stmt->close();
    } else {
        // Create new profile
        $insert_profile_sql = "INSERT INTO user_profiles (user_id, phone) VALUES (?, ?)";
        $insert_profile_stmt = $conn->prepare($insert_profile_sql);
        $insert_profile_stmt->bind_param("is", $user_id, $phone);
        $insert_profile_stmt->execute();
        $insert_profile_stmt->close();
    }
    
    $check_profile_stmt->close();
    
    // Redirect back to dashboard with success message
    header("Location: dashboard.html?success=profile_updated");
    exit;
}

$conn->close();
?>