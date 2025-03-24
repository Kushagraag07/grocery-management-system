<?php
$conn = new mysqli("localhost", "root", "", "picknpack");

$sql = "SELECT * FROM orders";
$result = $conn->query($sql);

$orders = [];
while($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

header('Content-Type: application/json');
echo json_encode($orders);
?>
