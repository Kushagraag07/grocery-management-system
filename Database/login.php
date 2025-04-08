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
    // Collect form data
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format";
        exit();
    }
    
    // Prepare SQL statement
    $sql = "SELECT id, name, email, password FROM users WHERE email = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("s", $email);
        
        // Execute statement
        if ($stmt->execute()) {
            // Store result
            $stmt->store_result();
            
            // Check if email exists
            if ($stmt->num_rows == 1) {
                // Bind result variables
                $stmt->bind_result($id, $name, $db_email, $hashed_password);
                if ($stmt->fetch()) {
                    // Verify password
                    if (password_verify($password, $hashed_password)) {
                        // Password is correct, start a new session
                        session_start();
                        
                        // Store data in session variables
                        $_SESSION["loggedin"] = true;
                        $_SESSION["id"] = $id;
                        $_SESSION["name"] = $name;
                        $_SESSION["email"] = $db_email;
                        
                        // Redirect user to dashboard page
                        header("Location: dashboard.html");
                    } else {
                        // Password is not valid
                        echo "<script>
                                alert('Invalid password');
                                window.location.href = 'login.html';
                              </script>";
                    }
                }
            } else {
                // Email doesn't exist
                echo "<script>
                        alert('No account found with that email');
                        window.location.href = 'login.html';
                      </script>";
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        
        // Close statement
        $stmt->close();
    }
}

// Close connection
$conn->close();
?>