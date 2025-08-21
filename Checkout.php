<?php
session_start();
if (!isset($_SESSION['isMember'])) {
    header("Location: Login.php");
    exit;
}

$customerId = $_SESSION['CustomerID'];

$conn = new mysqli("localhost", "root", "", "projdb", 3306);


$stmt = $conn->prepare("
    SELECT i.ItemID, i.ItemName, i.Price, i.ImageURL, ci.Quantity
    FROM carts c
    JOIN cartitems ci ON c.CartID = ci.CartID
    JOIN items i ON ci.ItemID = i.ItemID
    WHERE c.CustomerID = ?
");

$stmt->bind_param("i", $customerId);
$stmt->execute();
$result = $stmt->get_result();

$count = 0;
$TotalPrice = 0;
?>








<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Restaurant Menu</title>
    <link rel="stylesheet" href="Styles/MenuCSS.css">
    <link rel="stylesheet" href="Styles/Checkout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Caveat&display=swap" rel="stylesheet">
    <script src="java/Checkout.js"></script>
</head>


<body>
<div class="main">

    <div class="bar">
        <div class="logo">
            <a href="Home.php"><img src="Images/logo.png" alt="Logo"></a>
        </div>

        <div class="menu">
            <a href="Home.php">Home</a>
            <a href="Menu.php">Menu</a>
            <a href="AboutUs.php">About us</a>
            <a href="News.php">News</a>
            <a href="ContactUs.php">Contact us</a>
        </div>

        <div class="icons">
            <a class="fas fa-shopping-cart" onclick="showCartTab()">
                <span id="cartNumber"><?php // Span value from count belo ?></span>
            </a>
            <a class="fa-solid fa-bell" onclick="showBellTab()">
                <span id="bellNumber">0</span>
            </a>
            <a class="fas fa-user" onclick="showProfileTab()"></a>
        </div>
    </div>






    <div class="cartTab" id="cartTab" style="right: 0;">
        <h1>Shopping Cart</h1>
        <div class="cartContent">
            <div class="listCart">
                <?php while ($row = $result->fetch_assoc()):
                    $count += (int)$row['Quantity'];
                    $TotalPrice += (int)$row['Price'] * (int)$row['Quantity'];?>
                    <div class="item" data-name="<?= htmlspecialchars($row['ItemName']) ?>" data-id="<?= (int)$row['ItemID'] ?>">
                        <div class="item-img">
                            <img src="<?= htmlspecialchars($row['ImageURL']) ?>" alt="">
                        </div>
                        <div class="name"><?= htmlspecialchars($row['ItemName']) ?></div>
                        <div class="price" data-unit-price="<?= htmlspecialchars($row['Price']) ?>">
                            <?= number_format($row['Price'] * $row['Quantity'], 2) ?>
                        </div>
                        <div class="quantity">
                            <span class="minus" onclick="minusItem(this)"><</span>
                            <span id="quantity"><?= (int)$row['Quantity'] ?></span>
                            <span class="plus" onclick="plusItem(this)">></span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        <div class="btn">
            <button class="close" onclick="showCartTab()">Close</button>
            <button class="checkOut"><a href="Checkout.php">Checkout</a></button>
        </div>
    </div>

</div>


<script>
    document.getElementById('cartNumber').innerText = <?= $count ?>;
    document.getElementById('totalPrice').innerText = <?= $TotalPrice ?>;
</script>
<?php
$stmt->close();
$conn->close();
?>




<div class="bellTab" id="bellTab">
    <h1>Notifications</h1>
    <div class="bellContent">
        <div class="listBell"></div>
    </div>
    <div class="btn">
        <button class="close" onclick="showBellTab()">Close</button>
    </div>
</div>

<script>

    function timeAgo(timestamp) {
        const now = new Date();
        const then = new Date(timestamp);
        const seconds = Math.floor((now - then) / 1000);

        if (seconds < 60) return `${seconds}s ago`;
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) return `${minutes}m ago`;
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return `${hours}h ago`;
        const days = Math.floor(hours / 24);
        return `${days}d ago`;
    }

    function loadNotifications() {
        fetch("fetch_notifications.php")
            .then(res => res.json())
            .then(data => {
                const list = document.querySelector("#bellTab .listBell");
                const badge = document.getElementById("bellNumber");
                list.innerHTML = "";

                let unreadCount = 0;

                data.forEach(n => {
                    const div = document.createElement("div");
                    div.className = "notification";

                    const msg = document.createElement("div");
                    msg.className = "msg";
                    msg.innerText = n.Message;

                    const time = document.createElement("div");
                    time.className = "time";
                    time.innerText = timeAgo(n.created_at);

                    div.appendChild(msg);
                    div.appendChild(time);

                    if (n.Status === "Pending") {
                        div.classList.add("pending");
                        unreadCount++;
                    } else if (n.Status === "Ready") {
                        div.classList.add("ready");
                        unreadCount++;
                    }

                    list.appendChild(div);
                });


                if (unreadCount > 0) {
                    badge.style.display = "inline-block";
                    badge.innerText = unreadCount;
                } else {
                    badge.style.display = "none";
                }
            });
    }


    // Refresh notifications every 3s
    setInterval(() => {
        // Update DB statuses (Pending → Ready, auto-delete old ones)
        fetch("update_notifications.php").then(() => {
            loadNotifications();
        });
    }, 3000);

    // Initial load
    loadNotifications();
</script>





<div class="profileTab" id="profileTab">
    <h1>Profile</h1>
    <div class="profileContent">
        <div class="listProfile">
            <?php
            if (isset($_SESSION['isMember']) && $_SESSION['isMember'] == 1) {
                echo "<p><strong>First Name:</strong> " . htmlspecialchars($_SESSION['FName']) . "</p>";
                echo "<p><strong>Last Name:</strong> " . htmlspecialchars($_SESSION['LName']) . "</p>";
                echo "<p><strong>Email:</strong> " . htmlspecialchars($_SESSION['Gmail']) . "</p>";
            } else {
                echo "<p>You are not logged in.</p>";
            }
            ?>
        </div>
    </div>
    <div class="btn">
        <button class="close" onclick="showProfileTab()">Close</button>
        <button class="logout" onclick="logout()">Sign Out</button>
    </div>
</div>





<form id="checkoutForm" class="checkout-container" action="SubmitOrder.php" method="POST">
    <h1>Checkout</h1>

    <div class="order-type">
        <label>
            <input type="radio" name="orderType" value="Dine In" checked>
            Dine In
        </label>
        <label>
            <input type="radio" name="orderType" value="Delivery">
            Delivery
        </label>
    </div>

    <label for="customerName">Order Name</label>
    <input
            type="text"
            id="customerName"
            name="customerName"
            placeholder="Your Name"
            required
    >

    <label for="customerPhone">Phone Number</label>
    <input
            type="tel"
            id="customerPhone"
            name="customerPhone"
            placeholder="Phone Number"
            required
    >

    <label for="customerAddress">Address</label>
    <input
            type="text"
            id="customerAddress"
            name="customerAddress"
            placeholder="Delivery Address"
            required
    >

    <label for="orderComments">Comments</label>
    <textarea
            id="orderComments"
            name="orderComments"
            placeholder="Any special requests?"
            rows="3"
            style="width:100%; height:120px; resize:none;"
    ></textarea>

    <div class="total-price">
        <span>Total:</span>
        <span id="totalPrice">
      <?= number_format($TotalPrice, 2) ?>
    </span>
    </div>

    <button type="submit" class="confirm-btn" onclick="confirmOrder(event)">
        Confirm Order
    </button>
</form>



<pre>






</pre>







<footer class="extra-footer">
    <div class="footer-container">
        <div class="footer-item">
            <i class="fas fa-phone-alt"></i>
            <h4>Call Us</h4>
            <p>0598911096</p>
        </div>
        <div class="footer-item">
            <i class="fas fa-envelope"></i>
            <h4>Email</h4>
            <p>contact@yourrestaurant.com</p>
        </div>
        <div class="footer-item">
            <i class="fas fa-clock"></i>
            <h4>Opening Hours</h4>
            <p>Mon-Sat: 10:00 AM - 10:00 PM</p>
        </div>
        <div class="footer-item social">
            <h4>Follow Us</h4>
            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
        </div>
    </div>
</footer>


</body>
</html>
