<?php
session_start();
if (!isset($_SESSION['CustomerID'])) {
    echo json_encode([]);
    exit;
}

$customerId = $_SESSION['CustomerID'];
$conn = new mysqli("localhost", "root", "", "projdb", 3306);

$sql = "SELECT NotificationID, OrderID, Message, Status,
        CreatedAt AS created_at
        FROM notifications 
        WHERE CustomerID = ? 
        ORDER BY CreatedAt DESC LIMIT 10";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customerId);
$stmt->execute();
$res = $stmt->get_result();

$notifications = [];
while ($row = $res->fetch_assoc()) {
    $notifications[] = $row;
}
echo json_encode($notifications);
?>
