<?php
// Initialize the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // Return empty array if not logged in
    echo json_encode([]);
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

// Get orders for this user
$sql = "SELECT id, total_amount, status, DATE_FORMAT(created_at, '%M %d, %Y') as created_at 
        FROM orders 
        WHERE user_id = ? 
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();

$orders = [];

// Fetch orders
while ($order = $orders_result->fetch_assoc()) {
    // Get order items for this order
    $items_sql = "SELECT p.name, oi.quantity, oi.price 
                 FROM order_items oi
                 JOIN products p ON oi.product_id = p.id
                 WHERE oi.order_id = ?";
                 
    $items_stmt = $conn->prepare($items_sql);
    $items_stmt->bind_param("i", $order["id"]);
    $items_stmt->execute();
    $items_result = $items_stmt->get_result();
    
    $items = [];
    while ($item = $items_result->fetch_assoc()) {
        $items[] = $item;
    }
    
    // Add items to order
    $order["items"] = $items;
    $orders[] = $order;
    
    $items_stmt->close();
}

// Return orders as JSON
echo json_encode($orders);

$stmt->close();
$conn->close();
?>