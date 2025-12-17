//this script handels: adding items to the cart, changing quantites, calculate the total price, send the order data to php
//imple cart: stores like this { "item name": {price: number, qty: number} }
var cart = {};
//get the importanr html elements
var orderList = document.getElementById('orderList'); //order lkist container
var cartTotal = document.getElementById('cartTotal');//total price text
var tableNoEl = document.getElementById('tableNo');//table number uinput
var btnClear  = document.getElementById('btnClear');//clear cart button
var btnOrder  = document.getElementById('placeOrder');//place order button
//number as euro cuurency
function euro(v) {
  return '€' + (Math.round(v*100)/ 100);
}
//adds an item to cart; if item alreadt exist increases quantity
function addToCart(name,price, qty) {
  //if item doesnt exist in cart, create it
  if (!cart[name]) {
    cart[name] = {price: price, qty: 0};
  }
  //increase item quantity
  cart[name].qty = cart[name].qty + qty;
  renderCart();
}
//change quantity of item in the cart; if quantity becomes 0, items is removedx
function changeQty(name, change) {
  if (!cart[name]) {
    return
  };
  cart[name].qty = cart[name].qty + change;
  if(cart[name].qty <= 0) {
    delete cart[name];
  }
  renderCart();
}
//display cart item: items on the page - also calculate and update total price
function renderCart(){
  if (!orderList){
    return
  };

  orderList.innerHTML = '';
  var total = 0;

  for(var name in cart) {
    var price = cart[name].price;
    var qty = cart[name].qty;
    var line = price *qty;
    total += line;
    //create html for each cart item
    var row = document.createElement('div');
    row.className = 'order-row';

    //basic HTML 
    row.innerHTML=
      '<div class="left">' +
        '<div class="name">' + name + '</div>' +
        '<div class="controls">' +
          '<button class="mini-btn" data-act="dec" data-name="' + name + '">-</button>' +
          '<span class="pcs">' + qty + '</span>' +
          '<button class="mini-btn" data-act="inc" data-name="' + name + '">+</button>' +
          '<button class="mini-btn" data-act="rm" data-name="' + name + '" title="remove">×</button>' +
        '</div>' +
      '</div>' +
      '<div class="sum">' + euro(line) + '</div>';
    orderList.appendChild(row);
  }
  if (cartTotal) {
    cartTotal.textContent = euro(total);
  }
}
//handles all clock events on the page; quantity buttons, add to cart buttons, cart controls like: -/+/remove
document.onclick = function(e) {
  var t = e.target;

  //handle cart control buttons: 
  if (t.tagName === 'BUTTON' && t.parentNode && t.parentNode.className.indexOf('qty') !== -1) {
    var span = t.parentNode.getElementsByTagName('span')[0];
    var n = parseInt(span.innerHTML, 10);
    if(isNaN(n)) {
      n = 1;
    }
    if(t.innerHTML.trim()=== '+') {
      n++;
    }
    if(t.innerHTML.trim() === '-'){
      n = Math.max(1,  n-1);
    }
    span.innerHTML =n;
    return;
  }

  //add button on cards
  if (t.className && t.className.indexOf('add') !== -1) {
    //find parent .card
    var card = t;
    while (card && (!card.className || card.className.indexOf('card') === -1)) {
      card = card.parentNode;
    }
    if (!card) {
      return;
    }

    var name = card.getAttribute('data-name');
    var price = parseFloat(card.getAttribute('data-price'));

    // qty from card (.qty span)
    var qty = 1;
    var spans = card.getElementsByTagName('span');
    for (var i = 0; i < spans.length; i++) {
      if (spans[i].parentNode && spans[i].parentNode.className.indexOf('qty') !== -1) {
        qty = parseInt(spans[i].innerHTML, 10);
        if (isNaN(qty)) {
          qty = 1;//if quantyty isnt a number, set default value
        }
        spans[i].innerHTML = '1'; //resetto 1
        break;
      }
    }
    if (!name || isNaN(price)) {
      return;
    }
    addToCart(name, price, qty);
  }
  //cart controls buttons
  if (t.tagName === 'BUTTON' && t.getAttribute('data-act')) {
    var act = t.getAttribute('data-act');
    var name2 = t.getAttribute('data-name');

    if (act === 'dec') {
      changeQty(name2, -1);
    }
    else if (act === 'inc'){
      changeQty(name2, +1);
    }
    else if (act === 'rm'){ 
      delete cart[name2]; 
      renderCart(); 
    }
    return;
  }
};

//clear button
if (btnClear) {
  btnClear.onclick = function() {
    for (var k in cart) {
      delete cart[k];
    }
    renderCart();
  };
}
//place order: send order data to place_order.php
if (btnOrder) {
  btnOrder.onclick = function() {
    var tableNo = "";
    if(tableNoEl){
      tableNo= tableNoEl.value;
    }
    tableNo = tableNo.trim();
    //chck if the table number is entered
    if (!tableNo){ 
      alert('Please enter a table number!'); 
      return; 
    }
    //check if cart is empty
    var empty = true;
    for (var k in cart){ 
      empty = false; 
      break; 
    }
    if (empty){ 
      alert('Your order is empty.'); 
      return; 
    }
    //prepare form data
    var fd = new FormData();
    fd.append('table_no', tableNo);

    for (var name in cart) {
      fd.append('dish_name[]', name);
      fd.append('qty[]', cart[name].qty);
      fd.append('unit_price[]', cart[name].price);
    }
    //send data to php
    fetch('place_order.php', { 
      method: 'POST', 
      body: fd 
    })
      .then(function(r){ 
        return r.text(); 
      })
      .then(function(txt){
        if (txt.trim().toLowerCase().indexOf('ok') === 0) {
          alert(txt.trim());
          //clear cart after success order
          for (var k in cart) {
            delete cart[k];
          }
          renderCart();

          if (tableNoEl) {
            tableNoEl.value = '';
          }
        }else {
          alert(txt);
        }
      })
      .catch(function(){ 
        alert('Server error'); 
      });
  };
}
//first render
renderCart();