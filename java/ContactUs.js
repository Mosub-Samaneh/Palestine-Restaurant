function addToCart(name, price, imgSrc, e) {
    e.stopPropagation(); // prevent flip card click

    const cartList = document.querySelector('.listCart');
    let existingItem = [...cartList.querySelectorAll('.item')].find(item => item.dataset.name === name);

    if (existingItem) {
        // Increase quantity if already exists
        let quantityElem = existingItem.querySelector('#quantity');
        let currentQuantity = parseInt(quantityElem.innerText);
        quantityElem.innerText = currentQuantity + 1;

        // Update price
        let priceElem = existingItem.querySelector('.price');
        let unitPrice = parseFloat(price); // Get the original price from parameters
        priceElem.innerText = (unitPrice * (currentQuantity + 1)).toFixed(2);

        // Update cart counter
        let cartNumberElem = document.getElementById("cartNumber");
        cartNumberElem.innerText = parseInt(cartNumberElem.innerText) + 1;
    } else {
        // Create new cart item
        let item = document.createElement('div');
        item.classList.add('item');
        item.dataset.name = name;

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

        // Increase cart number
        let cartNumberElem = document.getElementById("cartNumber");
        cartNumberElem.innerText = parseInt(cartNumberElem.innerText) + 1;
    }
}


function plusItem(btn) {
    const item = btn.closest('.item');
    const quantityElem = item.querySelector('#quantity');
    const priceElem = item.querySelector('.price');
    const unitPrice = parseFloat(priceElem.dataset.unitPrice); // stored unit price

    let quantity = parseInt(quantityElem.innerText) + 1;
    quantityElem.innerText = quantity;

    priceElem.innerText = (unitPrice * quantity).toFixed(2);

    let cartNumberElem = document.getElementById("cartNumber");
    cartNumberElem.innerText = parseInt(cartNumberElem.innerText) + 1;
}

function minusItem(btn) {
    const item = btn.closest('.item');
    const quantityElem = item.querySelector('#quantity');
    const priceElem = item.querySelector('.price');
    const unitPrice = parseFloat(priceElem.dataset.unitPrice);

    let quantity = parseInt(quantityElem.innerText) - 1;

    if (quantity <= 0) {
        item.remove();
    } else {
        quantityElem.innerText = quantity;
        priceElem.innerText = (unitPrice * quantity).toFixed(2);
    }

    let cartNumberElem = document.getElementById("cartNumber");
    cartNumberElem.innerText = Math.max(0, parseInt(cartNumberElem.innerText) - 1);
}




let shown = false;
function showCartTab() {
    let cart = document.getElementById("cartTab");

    if (!shown) {
        shown = true;
        cart.style.transform = "translateX(500px)";
    } else {
        shown = false;
        cart.style.transform = "translateX(-500px)";
    }
}