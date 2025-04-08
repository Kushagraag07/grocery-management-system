<?php
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
    // Collect form data and sanitize inputs
    $name = htmlspecialchars($_POST["name"]);
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format";
        exit();
    }
    
    // Check if email already exists
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        echo "<script>
                alert('Email already registered. Please use a different email or login.');
                window.location.href = 'signup.html';
              </script>";
        $check_stmt->close();
        exit();
    }
    $check_stmt->close();
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Prepare and bind parameters
    $sql = "INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $hashed_password);
    
    // Execute query and check
    if ($stmt->execute()) {
        // Create a session
        session_start();
        
        // Get the user ID of the newly created user
        $user_id = $conn->insert_id;
        
        // Set session variables
        $_SESSION["loggedin"] = true;
        $_SESSION["id"] = $user_id;
        $_SESSION["name"] = $name;
        $_SESSION["email"] = $email;
        
        // Redirect to dashboard
        header("Location: dashboard.html");
    } else {
        echo "Error: " . $stmt->error;
    }
    
    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>