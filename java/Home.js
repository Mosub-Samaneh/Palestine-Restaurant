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