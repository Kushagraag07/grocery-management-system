<?php
$conn = new mysqli("localhost", "root", "", "picknpack");

$sql = "SELECT * FROM products";
$result = $conn->query($sql);

$products = array();
while($row = $result->fetch_assoc()) {
    $product[] = $row;
}

header('Content-Type: application/json');
echo json_encode($product);
?>
