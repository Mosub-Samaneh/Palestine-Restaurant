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
    <link rel="stylesheet" href="Styles/HomeCSS.css">
    <title>Title</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Caveat&display=swap" rel="stylesheet">
    <script src="java/Home.js"></script>

</head>



<body>
<div class="main">

    <div class="bar">
        <div class="logo">
            <a href="Home.php"><img src="Images/logo.png"></a>
        </div>

        <div class="menu">
            <a style="color: #FC4202" href="Home.php">Home</a>
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




    <!-- Image and welcome text container -->
    <div class="welcome-section">
        <div class="welcome-text">
            <h1>Welcome to Our Restaurant</h1>
            <p STYLE="color: black"> <b>Delicious moments  wait you</b></p>
        </div>
        <div class="welcome-image">
            <img src="Images/pizza_cheese.webp" class="slide show" id="slide1" alt="Pizza" width="900" height="500">
            <img src="Images/burger_fries.webp" class="slide" id="slide2" alt="Burger" width="900" height="500">
            <img src="Images/RR.webp" class="slide" id="slide3" alt="chicken" width="900" height="500">

        </div>
    </div>


    <!-- Best Dish Section -->
    <div class="best-dish">
        <img src="Images/BestDish.jpg" alt="Best Dish" class="best-dish-img" width="200" height="300">
        <div class="best-dish-text">
            <h2>Best Dish of the Day</h2>
            <p>Hamburger & Cheese with Pickles</p>
            <button class="add-to-cart">₪35 - Add to Cart</button>
        </div>
    </div>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <h2>Testimonials <br> They Say About Us</h2>
        <div class="testimonial-list">
            <div class="testimonial">
                <div class="avatar male"><i class="fas fa-user"></i></div>
                <h3>Ahmad</h3>
                <p>"The pizza here is amazing — crispy crust and fresh toppings every time!"</p>
            </div>
            <div class="testimonial">
                <div class="avatar male"><i class="fas fa-user"></i></div>
                <h3>Ali</h3>
                <p>"I love the fast service and friendly staff. The burgers are my favorite!"</p>
            </div>
            <div class="testimonial">
                <div class="avatar female"><i class="fas fa-user-circle"></i></div>
                <h3>Rhaf</h3>
                <p>"Great place for quick meals that never compromise on taste."</p>
            </div>
        </div>
    </section>

    <pre>

    </pre>



<pre>

</pre>



    <!-- Why Choose Us Section -->
    <section class="why-choose-us">
        <h2>Why Choose Us?</h2>
        <div class="features">
            <div class="feature">
                <i class="fas fa-star"></i>
                <h3>Top Quality</h3>
                <p>We use the freshest ingredients in every dish.</p>
            </div>
            <div class="feature">
                <i class="fas fa-shipping-fast"></i>
                <h3>Fast Delivery</h3>
                <p>Your meal delivered hot and on time.</p>
            </div>
            <div class="feature">
                <i class="fas fa-heart"></i>
                <h3>Customer Love</h3>
                <p>People keep coming back — and they tell their friends.</p>
            </div>
        </div>
    </section>

    <pre>
    </pre>


<pre>
</pre>

    <!-- About Restaurant Section -->
    <div class="about-restaurant" id="about">
        <div class="about-icon">
            <i class="fas fa-utensils"></i>
        </div>
        <div class="about-content">
            <h2>About Our Restaurant</h2>
            <p><b>We are a fast-food kitchen serving tasty pizzas, burgers, fries, and more. Our chefs use fresh ingredients to make every meal delicious. Whether you eat here or take your food home, we deliver fast, great flavors, and a happy experience every time.</b></p>
        </div>
    </div>
    <!-- Red bottom line -->
    <hr class="about-line">
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
        <img src="Images/TopArrow.png" width="50px" style="color: black">
    </a>

</div>
<script>
    let current = 0;
    const slides = document.querySelectorAll('.slide');

    setInterval(() => {
        slides[current].classList.remove('show');
        current = (current + 1) % slides.length;
        slides[current].classList.add('show');
    }, 3000);
</script>
</body>
</html>