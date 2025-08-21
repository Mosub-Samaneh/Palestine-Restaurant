<?php
session_start();

if (!isset($_SESSION['CustomerID'])) {
    header("Location: Login.php");
}

$customerId = $_SESSION['CustomerID'];
$itemId = intval($_POST['id']);
$action = $_POST['action'];

$conn = new mysqli("localhost", "root", "", "projdb", 3306);
if ($conn->connect_error) {
    http_response_code(500);
    exit("DB connection failed");
}

$cartSql = "SELECT CartID FROM Carts WHERE CustomerID = ? LIMIT 1";
$stmt = $conn->prepare($cartSql);
$stmt->bind_param("i", $customerId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$cartId = $row['CartID'];


$stmt = $conn->prepare("SELECT CartItemID, Quantity FROM CartItems WHERE CartID = ? AND ItemID = ?");
$stmt->bind_param("ii", $cartId, $itemId);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

if ($row) {
    $newQty = $row['Quantity'] + ($action === 'plus' ? 1 : -1);
    if ($newQty <= 0) {
        $delete = $conn->prepare("DELETE FROM CartItems WHERE CartItemID = ?");
        $delete->bind_param("i", $row['CartItemID']);
        $delete->execute();
    } else {
        $update = $conn->prepare("UPDATE CartItems SET Quantity = ? WHERE CartItemID = ?");
        $update->bind_param("ii", $newQty, $row['CartItemID']);
        $update->execute();
    }
}

$conn->close();