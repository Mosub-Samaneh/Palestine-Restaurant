<?php
session_start();
if(isset($_SESSION['isMember'])){
    if($_SESSION['isMember'] == 0){
        header("Location: Login.php");
    }
}
else{
    header("Location: Login.php");
}
?>

<?php
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
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Restaurant News</title>
    <link rel="stylesheet" href="Styles/CSSforNews.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
</head>
<script src="java/News.js"></script>


<body>

<div class="main">

    <div class="bar">
        <div class="logo">
            <a href="Home.php"><img src="Images/logo.png"></a>
        </div>

        <div class="menu">
            <a href="Home.php">Home</a>
            <a href="Menu.php">Menu</a>
            <a href="AboutUs.php">About us</a>
            <a style="color: #FC4202" href="News.php">News</a>
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






    <div class="cartTab" id="cartTab">
        <h1>Shopping Cart</h1>
        <div class="cartContent">
            <div class="listCart">
                <?php while ($row = $result->fetch_assoc()):
                    $count += (int)$row['Quantity'];?>
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






    <pre>






    </pre>

    <div class="news-header">
        <h1>🍽️ Latest News & Updates</h1>
        <p>Stay informed about new dishes, events, and promotions!</p>
    </div>

    <div class="news-container">
        <div class="news-card">
            <img src="Images/spicy_burger.webp" alt="New Dish">
            <h2>🔥 New Spicy Burger Launched!</h2>
            <p>We just launched our fiery new Spicy Burger—perfect for those who like it hot!</p>
            <button class="read-more-btn">Read More</button>
            <div class="hidden-content">
                <p>This burger is made with our homemade spicy sauce, jalapeños, and premium beef. Available for a limited time only.</p>
            </div>
        </div>

        <div class="news-card">
            <img src="Images/group-people-enjoying-live-music-42054098.webp" alt="Live Music">
            <h2>🎶 Live Music Every Friday</h2>
            <p>Enjoy your dinner with local musicians playing live every Friday evening.</p>
            <button class="read-more-btn">Read More</button>
            <div class="hidden-content">
                <p>Book early to get the best seats and enjoy a fantastic ambiance with great food and live tunes.</p>
            </div>
        </div>

        <div class="news-card">
            <img src="Images/offer-combo-food-based-on-the-season.jpg" alt="Special Offer">
            <h2>💥 Summer Combo Deal</h2>
            <p>Grab our summer combo meal for just ₪36.96 this August!</p>
            <button class="read-more-btn">Read More</button>
            <div class="hidden-content">
                <p>The combo includes a burger, fries, and a cold drink. Perfect for the summer heat!</p>
            </div>
        </div>
    </div>

</div>
<pre>



</pre>




<div class="testimonials-section">
    <h2>💬 What Our Customers Say</h2>
    <div class="testimonial-slider">
        <div class="testimonial active">
            <p>"The spicy burger is a flavor bomb! Definitely coming back!"</p>
            <h4>— Sarah L.</h4>
        </div>
        <div class="testimonial">
            <p>"Amazing atmosphere on Fridays with live music. Loved it!"</p>
            <h4>— Omar K.</h4>
        </div>
        <div class="testimonial">
            <p>"The summer combo was affordable and tasty. Highly recommended!"</p>
            <h4>— Dina M.</h4>
        </div>
    </div>

    <div class="testimonial-nav">
        <span class="dot active"></span>
        <span class="dot"></span>
        <span class="dot"></span>
    </div>
</div>

<div class="chef-spotlight-section">
    <h2>👨‍🍳 Meet Our Chefs</h2>
    <div class="chef-cards-container">

        <div class="chef-card" onclick="flipCard(this)">
            <div class="card-inner">
                <div class="card-front">
                    <img src="Images/Boy.jpg" alt="Chef 1">
                    <h3>Chef Khaled</h3>
                    <p>Expert in Grill & Meats</p>
                </div>
                <div class="card-back">
                    <h3>Signature Dish</h3>
                    <p>Smoky BBQ Ribs served with seasoned fries and coleslaw.</p>
                </div>
            </div>
        </div>

        <div class="chef-card" onclick="flipCard(this)">
            <div class="card-inner">
                <div class="card-front">
                    <img src="Images/gril.jpg" alt="Chef 2">
                    <h3>Chef Layla</h3>
                    <p>Lady of Burgers</p>
                </div>
                <div class="card-back">
                    <h3>Signature Dish</h3>
                    <p>Grilled meat sandwich in American style with melted cheese and special sauce
                        </p>
                </div>
            </div>
        </div>

        <div class="chef-card" onclick="flipCard(this)">
            <div class="card-inner">
                <div class="card-front">
                    <img src="Images/boy2.jpg.webp" alt="Chef 3" class="large-chef-img">
                    <h3>Chef Youssef</h3>
                    <p>Seafood Specialist</p>
                </div>
                <div class="card-back">
                    <h3>Signature Dish</h3>
                    <p>Grilled salmon fillet with lemon butter sauce and vegetables.</p>
                </div>
            </div>
        </div>


    </div>
</div>



<br>
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





<!-- Back to top arrow -->
<a href="#top" class="toparrow">
    <img src="Images/TopArrow.png" width="50px">
</a>




</body>
</html>