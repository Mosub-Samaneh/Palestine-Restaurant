<?php
$conn = new mysqli("localhost", "root", "", "projdb", 3306);

$conn->query("UPDATE notifications 
              SET Status='Ready', Message=CONCAT('Order #', OrderID, ' is ready!') 
              WHERE Status='Pending' AND TIMESTAMPDIFF(SECOND, CreatedAt, NOW()) >= 30");

$conn->query("DELETE FROM notifications 
              WHERE TIMESTAMPDIFF(SECOND, CreatedAt, NOW()) >= 300");
?>
