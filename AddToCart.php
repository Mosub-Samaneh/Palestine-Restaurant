<?php
session_start();

if (!isset($_SESSION['CustomerID'])) {
    header("Location: Login.php");
}

$customerId = $_SESSION['CustomerID'];

$itemId = intval($_GET['id']);


$conn = new mysqli("localhost", "root", "", "projdb", "3306");


$cartSql = "SELECT CartID FROM Carts WHERE CustomerID = ? LIMIT 1";
$stmt = $conn->prepare($cartSql);
$stmt->bind_param("i", $customerId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $cartId = $row['CartID'];
} else {
    // Create new cart
    $insertCart = $conn->prepare("INSERT INTO Carts (CustomerID) VALUES (?)");
    $insertCart->bind_param("i", $customerId);
    $insertCart->execute();
    $cartId = $insertCart->insert_id;
}


$checkSql = "SELECT CartItemID, Quantity FROM CartItems WHERE CartID = ? AND ItemID = ?";
$check = $conn->prepare($checkSql);
$check->bind_param("ii", $cartId, $itemId);
$check->execute();
$res = $check->get_result();

if ($row = $res->fetch_assoc()) {

    $newQty = $row['Quantity'] + 1;
    $update = $conn->prepare("UPDATE CartItems SET Quantity = ? WHERE CartItemID = ?");
    $update->bind_param("ii", $newQty, $row['CartItemID']);
    $update->execute();
} else {

    $insert = $conn->prepare("INSERT INTO CartItems (CartID, ItemID, Quantity) VALUES (?, ?, 1)");
    $insert->bind_param("ii", $cartId, $itemId);
    $insert->execute();
}


$conn->close();
//header("Location: Menu.php");
exit;
?>
