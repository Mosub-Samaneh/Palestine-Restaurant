document.addEventListener("DOMContentLoaded", () => {
    const testimonials = document.querySelectorAll(".testimonial");
    const dots = document.querySelectorAll(".dot");

    let current = 0;

    function showTestimonial(index) {
        testimonials.forEach((testimonial, i) => {
            testimonial.classList.toggle("active", i === index);
            dots[i].classList.toggle("active", i === index);
        });
    }

    dots.forEach((dot, i) => {
        dot.addEventListener("click", () => {
            current = i;
            showTestimonial(current);
        });
    });

    setInterval(() => {
        current = (current + 1) % testimonials.length;
        showTestimonial(current);
    }, 5000);
});




document.addEventListener("DOMContentLoaded", () => {
    const chefCard = document.getElementById("chefCard");
    chefCard.addEventListener("click", () => {
        chefCard.classList.toggle("flip");
    });
});



document.addEventListener("DOMContentLoaded", () => {
    // Optional: Add smooth scroll or other effects here
});

function flipCard(card) {
    card.classList.toggle("flip");
}






function addToCart(name, price, imgSrc, e, id) {
    e.stopPropagation();

    const cartList = document.querySelector('.listCart');
    let existingItem = [...cartList.querySelectorAll('.item')].find(item => item.dataset.name === name);

    if (existingItem) {

        let quantityElem = existingItem.querySelector('#quantity');
        let currentQuantity = parseInt(quantityElem.innerText);
        quantityElem.innerText = currentQuantity + 1;


        let priceElem = existingItem.querySelector('.price');
        let unitPrice = parseFloat(price);
        priceElem.innerText = (unitPrice * (currentQuantity + 1)).toFixed(2);


        let cartNumberElem = document.getElementById("cartNumber");
        cartNumberElem.innerText = parseInt(cartNumberElem.innerText) + 1;
    } else {

        let item = document.createElement('div');
        item.classList.add('item');
        item.dataset.name = name;
        item.dataset.id = id;

        item.innerHTML = `
            <div class="item-img">
                <img src="${imgSrc}" alt="">
            </div>
            <div class="name">${name}</div>
            <div class="price" data-unit-price="${price}">${parseFloat(price).toFixed(2)}</div>
            <div class="quantity">
                <span class="minus" onclick="minusItem(this)"><</span>
                <span id="quantity">1</span>
                <span class="plus" onclick="plusItem(this)">></span>
            </div>
        `;
        cartList.appendChild(item);


        let cartNumberElem = document.getElementById("cartNumber");
        cartNumberElem.innerText = parseInt(cartNumberElem.innerText) + 1;
    }

    fetch("AddToCart.php?id=" + encodeURIComponent(id));
}


function plusItem(btn) {
    const item = btn.closest('.item');
    const quantityElem = item.querySelector('#quantity');
    const priceElem = item.querySelector('.price');
    const unitPrice = parseFloat(priceElem.dataset.unitPrice);
    const id = item.dataset.id;

    fetch("PlusMinusCart.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        credentials: "include",
        body: JSON.stringify({ id: id, action: "plus" })
    });

    let quantity = parseInt(quantityElem.innerText) + 1;
    quantityElem.innerText = quantity;
    priceElem.innerText = (unitPrice * quantity).toFixed(2);

    let cartNumberElem = document.getElementById("cartNumber");
    cartNumberElem.innerText = parseInt(cartNumberElem.innerText) + 1;

    const totalEl = document.getElementById('totalPrice');
    const total = parseFloat(totalEl.innerText);
    totalEl.innerText = (total + unitPrice).toFixed(2);
}


function minusItem(btn) {
    const item = btn.closest('.item');
    const quantityElem = item.querySelector('#quantity');
    const priceElem = item.querySelector('.price');
    const unitPrice = parseFloat(priceElem.dataset.unitPrice);
    const id = item.dataset.id;

    fetch("PlusMinusCart.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        credentials: "include",
        body: JSON.stringify({ id: id, action: "minus" })
    });

    let quantity = parseInt(quantityElem.innerText) - 1;

    if (quantity <= 0) {
        item.remove();
    } else {
        quantityElem.innerText = quantity;
        priceElem.innerText = (unitPrice * quantity).toFixed(2);
    }

    let cartNumberElem = document.getElementById("cartNumber");
    cartNumberElem.innerText = Math.max(0, parseInt(cartNumberElem.innerText) - 1);

    const totalEl = document.getElementById('totalPrice');
    const total = parseFloat(totalEl.innerText);
    totalEl.innerText = (total - unitPrice).toFixed(2);
}







let cartShown = false;
let profileShown = false;
let bellShown = false;

function showCartTab() {
    const cart = document.getElementById("cartTab");
    const profile = document.getElementById("profileTab");
    const bell = document.getElementById("bellTab");

    // Always close others
    profile.style.right = "-370px";
    profileShown = false;
    bell.style.right = "-400px";
    bellShown = false;

    if (!cartShown) {
        cart.style.right = "0px";
        cartShown = true;
    } else {
        cart.style.right = "-400px";
        cartShown = false;
    }
}

function showBellTab() {
    const cart = document.getElementById("cartTab");
    const bell = document.getElementById("bellTab");
    const profile = document.getElementById("profileTab");

    // Always close others
    profile.style.right = "-370px";
    profileShown = false;
    cart.style.right = "-400px";
    cartShown = false;

    if (!bellShown) {
        bell.style.right = "0px";
        bellShown = true;
    } else {
        bell.style.right = "-400px";
        bellShown = false;
    }
}

function showProfileTab() {
    const profile = document.getElementById("profileTab");
    const cart = document.getElementById("cartTab");
    const bell = document.getElementById("bellTab");

    // Always close others
    cart.style.right = "-400px";
    cartShown = false;
    bell.style.right = "-400px";
    bellShown = false;

    if (!profileShown) {
        profile.style.right = "0px";
        profileShown = true;
    } else {
        profile.style.right = "-370px";
        profileShown = false;
    }
}

function logout() {
    window.location.href = "logout.php";
}




