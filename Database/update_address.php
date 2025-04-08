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
    $address = htmlspecialchars($_POST["address"]);
    $city = htmlspecialchars($_POST["city"]);
    $state = htmlspecialchars($_POST["state"]);
    $postal_code = htmlspecialchars($_POST["postal_code"]);
    $user_id = $_SESSION["id"];
    
    // Check if user profile exists
    $check_profile_sql = "SELECT id FROM user_profiles WHERE user_id = ?";
    $check_profile_stmt = $conn->prepare($check_profile_sql);
    $check_profile_stmt->bind_param("i", $user_id);
    $check_profile_stmt->execute();
    $check_