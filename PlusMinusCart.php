<?php
// PlusMinusCart.php
session_start();
header('Content-Type: application/json');

$customerId = $_SESSION['CustomerID'];


$data = json_decode(file_get_contents('php://input'), true);
$itemId = isset($data['id']) ? intval($data['id']) : 0;
$action = isset($data['action']) ? $data['action'] : '';

$conn = new mysqli("localhost", "root", "", "projdb", "3306");



$stmt = $conn->prepare("SELECT CartID FROM Carts WHERE CustomerID = ? LIMIT 1");
$stmt->bind_param("i", $customerId);
$stmt->execute();
$res = $stmt->get_result();

if($row = $res->fetch_assoc()) {
    $cartId = $row['CartID'];
} else {
    $insCart = $conn->prepare("INSERT INTO Carts (CustomerID) VALUES (?)");
    $insCart->bind_param("i", $customerId);
    $insCart->execute();
    $cartId = $insCart->insert_id;
}


$check = $conn->prepare("SELECT CartItemID, Quantity FROM CartItems WHERE CartID = ? AND ItemID = ?");
$check->bind_param("ii", $cartId, $itemId);
$check->execute();
$res = $check->get_result();

$quantity = 0;

if($row = $res->fetch_assoc()) {
    $cartItemId = $row['CartItemID'];
    $quantity = $row['Quantity'];

    if($action === "plus") {
        $quantity++;
    } elseif($action === "minus") {
        $quantity--;
    }

    if($quantity <= 0) {
        $del = $conn->prepare("DELETE FROM CartItems WHERE CartItemID = ?");
        $del->bind_param("i", $cartItemId);
        $del->execute();
        $quantity = 0;
    } else {
        $upd = $conn->prepare("UPDATE CartItems SET Quantity = ? WHERE CartItemID = ?");
        $upd->bind_param("ii", $quantity, $cartItemId);
        $upd->execute();
    }
} elseif($action === "plus") {
    $ins = $conn->prepare("INSERT INTO CartItems (CartID, ItemID, Quantity) VALUES (?, ?, 1)");
    $ins->bind_param("ii", $cartId, $itemId);
    $ins->execute();
    $quantity = 1;
}


$total = 0;
$totalQ = $conn->prepare("
    SELECT SUM(ci.Quantity * i.Price) AS Total 
    FROM CartItems ci 
    JOIN Items i ON ci.ItemID = i.ItemID 
    WHERE ci.CartID = ?
");
$totalQ->bind_param("i", $cartId);
$totalQ->execute();
$totalRes = $totalQ->get_result();
if ($trow = $totalRes->fetch_assoc()) {
    $total = $trow['Total'] ?? 0;
}

$conn->close();

exit;
?>
