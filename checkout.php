<?php
session_start();
$conn = new mysqli("localhost", "root", "", "picknpack");

$user_name = $_POST['name'];
$user_email = $_POST['email'];

$sql = "INSERT INTO orders (user_name, user_email, total_price) 
        SELECT '$user_name', '$user_email', SUM(products.price * cart.quantity) 
        FROM cart JOIN products ON cart.product_id = products.id";

$conn->query($sql);
$conn->query("DELETE FROM cart"); // Clear cart after checkout

echo json_encode(["message" => "Order placed successfully!"]);
?>
