<?php
session_start();
$conn = new mysqli("localhost", "root", "", "picknpack");

$product_id = $_POST['product_id'];
$sql = "INSERT INTO cart (product_id) VALUES ('$product_id')";
$conn->query($sql);

echo json_encode(["message" => "Added to cart!"]);
?>
