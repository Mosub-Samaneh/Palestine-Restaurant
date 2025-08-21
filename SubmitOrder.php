<?php
session_start();

// Must be logged in
if (!isset($_SESSION['CustomerID'])) {
    header("Location: Login.php");
    exit;
}

$customerId   = $_SESSION['CustomerID'];
$orderType    = $_POST['orderType'];
$orderName    = $_POST['customerName'];
$orderPhone   = $_POST['customerPhone'];
$orderAddress = $_POST['customerAddress'];
$orderComment = $_POST['orderComments'];

// DB connection
$conn = new mysqli("localhost", "root", "", "projdb", 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. Get customer’s active cart
$cartSql = "SELECT CartID FROM carts WHERE CustomerID = ? ORDER BY CartID DESC LIMIT 1";
$stmt = $conn->prepare($cartSql);
$stmt->bind_param("i", $customerId);
$stmt->execute();
$result = $stmt->get_result();
if (!$row = $result->fetch_assoc()) {
    die("No active cart found.");
}
$cartId = $row['CartID'];
$stmt->close();

// 2. Get all items from this cart
$itemsSql = "SELECT ci.ItemID, ci.Quantity, i.Price 
             FROM cartitems ci 
             JOIN items i ON ci.ItemID = i.ItemID 
             WHERE ci.CartID = ?";
$stmt = $conn->prepare($itemsSql);
$stmt->bind_param("i", $cartId);
$stmt->execute();
$res = $stmt->get_result();

$items = [];
$totalPrice = 0;
while ($r = $res->fetch_assoc()) {
    $items[] = $r;
    $totalPrice += $r['Price'] * $r['Quantity'];
}
$stmt->close();

if (count($items) == 0) {
    echo "<script>alert('Cart is empty.'); window.location='Menu.php';</script>";
    exit;
}

// 3. Insert into `order` table
$orderSql = "INSERT INTO `order` 
(OrderDate, Status, OrderPrice, CustomerID, Comment, OrderName, OrderType) 
VALUES (NOW(), 'Pending', ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($orderSql);
$stmt->bind_param("disss", $totalPrice, $customerId, $orderComment, $orderName, $orderType);
$stmt->execute();
$orderId = $stmt->insert_id;
$stmt->close();

// 4. Clear cart after order
$clearStmt = $conn->prepare("DELETE FROM cartitems WHERE CartID = ?");
$clearStmt->bind_param("i", $cartId);
$clearStmt->execute();
$clearStmt->close();

// 5. Insert notification
$notifSql = "INSERT INTO notifications (OrderID, CustomerID, Message, Status) 
             VALUES (?, ?, ?, 'Pending')";
$notifMsg = "Your order #$orderId has been placed.";
$stmt = $conn->prepare($notifSql);
$stmt->bind_param("iis", $orderId, $customerId, $notifMsg);
$stmt->execute();
$stmt->close();

$conn->close();

// Redirect with alert
echo "<script>alert('Thank you for your order. Your order ID is " . $orderId . "'); window.location='Home.php';</script>";
exit;
?>
