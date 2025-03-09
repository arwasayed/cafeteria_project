<?php include_once 'userheader2.php';?>

<!-- Add Order Section -->
<section class="add_order_section layout_padding">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h2 class="section_heading">Add Order</h2>
      </div>
    </div>

    <!-- Order Form -->
    <form id="orderForm" method="POST">
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="order_items">
            <div class="item">
              <label>Tea</label>
              <input type="number" class="quantity" name="teaQuantity" value="0" min="0">
              <span class="price">EGP 5</span>
              <button class="add_item" data-item="tea">+</button>
              <button class="remove_item" data-item="tea">-</button>
            </div>
            <div class="item">
              <label>Cola</label>
              <input type="number" class="quantity" name="colaQuantity" value="0" min="0">
              <span class="price">EGP 10</span>
              <button class="add_item" data-item="cola">+</button>
              <button class="remove_item" data-item="cola">-</button>
            </div>
            <!-- Add more items as needed -->
          </div>
          <div class="notes">
            <textarea placeholder="Notes"></textarea>
          </div>
          <div class="room">
            <label>Room</label>
            <select>
              <option value="">Select Room</option>
              <option value="1">Room 1</option>
              <option value="2">Room 2</option>
              <!-- Add more room options as needed -->
            </select>
          </div>
          <div class="total">
            <span>Total: EGP <span id="totalAmount">0</span></span>
          </div>
          <button type="button" class="btn btn-primary" id="confirmOrder">Confirm</button>
        </div>
        <div class="col-md-6">
          <div class="latest_order">
            <h4>Latest Order</h4>
            <div class="items" id="latestOrderItems">
              <!-- Items will be dynamically added here -->
            <img src="images/tea.png" alt="Tea">
            <div class="item_info">
              <h5>Tea</h5>
              <p>5 LE</p>
              <p>Quantity: 1</p>
            </div>
              <!-- Items will be dynamically added here -->
            <img src="images/tea.png" alt="Tea">
            <div class="item_info">
              <h5>Tea</h5>
              <p>5 LE</p>
              <p>Quantity: 1</p>
            </div>
            
          </div>
        </div>
      </div>
    </form>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const orderItems = document.querySelectorAll('.order_items .item');
  const latestOrderItems = document.getElementById('latestOrderItems');
  const totalAmountDisplay = document.getElementById('totalAmount');

  let totalAmount = 0;

  orderItems.forEach(function(item) {
    const addItemButton = item.querySelector('.add_item');
    const removeItemButton = item.querySelector('.remove_item');
    const quantityInput = item.querySelector('.quantity');
    const priceSpan = item.querySelector('.price');
    const price = parseFloat(priceSpan.textContent.replace('EGP ', ''));

    if (!addItemButton || !removeItemButton || !quantityInput || !priceSpan) {
      console.error("One or more elements not found in the item:", item);
      return;
    }

    addItemButton.addEventListener('click', function(event) {
      event.preventDefault(); // Prevent default button action
      console.log("Add button clicked");
      quantityInput.value = parseInt(quantityInput.value) + 1;
      updateTotal();
    });

    removeItemButton.addEventListener('click', function(event) {
      event.preventDefault(); // Prevent default button action
      console.log("Remove button clicked");
      if (parseInt(quantityInput.value) > 0) {
        quantityInput.value = parseInt(quantityInput.value) - 1;
        updateTotal();
      }
    });

    quantityInput.addEventListener('input', function() {
      console.log("Quantity input changed");
      updateTotal();
    });
  });


  document.getElementById('confirmOrder').addEventListener('click', function() {
    // Handle order confirmation logic here
    alert('Order confirmed!');
  });
});
</script>

<?php include_once 'footer.php';?>