<?php
$conn = new mysqli("localhost", "root", "", "picknpack");

$sql = "SELECT products.name, products.price, cart.quantity 
        FROM cart JOIN products ON cart.product_id = products.id";

$result = $conn->query($sql);

$cart = [];
while($row = $result->fetch_assoc()) {
    $cart[] = $row;
}

header('Content-Type: application/json');
echo json_encode($cart);
?>
