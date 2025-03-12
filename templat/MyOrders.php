<?php include_once 'userheader2.php';?>

<!-- My Orders Section -->
<section class="my_orders_section layout_padding">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h2 class="section_heading">My Orders</h2>
      </div>
    </div>

    <!-- Date Filters -->
    <div class="row mb-4">
      <div class="col-md-6">
        <form>
          <div class="form-group row">
            <label for="dateFrom" class="col-sm-3 col-form-label">Date from</label>
            <div class="col-sm-9">
              <input type="date" class="form-control" id="dateFrom" name="dateFrom">
            </div>
          </div>
          <div class="form-group row">
            <label for="dateTo" class="col-sm-3 col-form-label">Date to</label>
            <div class="col-sm-9">
              <input type="date" class="form-control" id="dateTo" name="dateTo">
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Orders Table -->
    <div class="row">
      <div class="col-md-12">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Order Date</th>
              <th>Status</th>
              <th>Amount</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                2015/02/02 10:30 AM 
                <span class="toggle-details" data-toggle="+">+</span>
                <div class="order-details" style="display:none;">
                <div class="row order_details">
      <div class="col-md-12">
        <div class="order_items">
          <div class="item">
            <img src="images/tea.png" alt="Tea">
            <div class="item_info">
              <h5>Tea</h5>
              <p>5 LE</p>
              <p>Quantity: 1</p>
            </div>
          </div>
          <div class="item">
            <img src="images/coffee.png" alt="Coffee">
            <div class="item_info">
              <h5>Coffee</h5>
              <p>6 LE</p>
              <p>Quantity: 1</p>
            </div>
          </div>
          <div class="item">
            <img src="images/nescafe.png" alt="Nescafe">
            <div class="item_info">
              <h5>Nescafe</h5>
              <p>8 LE</p>
              <p>Quantity: 1</p>
            </div>
          </div>
          <div class="item">
            <img src="images/cola.png" alt="Cola">
            <div class="item_info">
              <h5>Cola</h5>
              <p>10 LE</p>
              <p>Quantity: 1</p>
            </div>
          </div>
        </div>
        <div class="total">
          <h4>Total: EGP 104</h4>
        </div>
      </div>
    </div>

                </div>
              </td>
              <td>Processing</td>
              <td>55 EGP</td>
              <td><button class="btn btn-danger">CANCEL</button></td>
            </tr>
            <tr>
              <td>
                2015/02/01 11:30 AM 
                <span class="toggle-details" data-toggle="+">+</span>
                <div class="order-details" style="display:none;">
                <div class="row order_details">
      <div class="col-md-12">
        <div class="order_items">
          <div class="item">
            <img src="images/tea.png" alt="Tea">
            <div class="item_info">
              <h5>Tea</h5>
              <p>5 LE</p>
              <p>Quantity: 1</p>
            </div>
          </div>
          <div class="item">
            <img src="images/coffee.png" alt="Coffee">
            <div class="item_info">
              <h5>Coffee</h5>
              <p>6 LE</p>
              <p>Quantity: 1</p>
            </div>
          </div>
          <div class="item">
            <img src="images/nescafe.png" alt="Nescafe">
            <div class="item_info">
              <h5>Nescafe</h5>
              <p>8 LE</p>
              <p>Quantity: 1</p>
            </div>
          </div>
          <div class="item">
            <img src="images/cola.png" alt="Cola">
            <div class="item_info">
              <h5>Cola</h5>
              <p>10 LE</p>
              <p>Quantity: 1</p>
            </div>
          </div>
        </div>
        <div class="total">
          <h4>Total: EGP 104</h4>
        </div>
      </div>
    </div>

                </div>
              </td>
              <td>Out for delivery</td>
              <td>20 EGP</td>
              <td><button class="btn btn-danger">CANCEL</button></td>
            </tr>
            <tr>
              <td>
                2015/01/01 11:35 AM 
                <span class="toggle-details" data-toggle="-">-</span>
                <div class="order-details" style="display:block;">
                <div class="row order_details">
      <div class="col-md-12">
        <div class="order_items">
          <div class="item">
            <img src="images/tea.png" alt="Tea">
            <div class="item_info">
              <h5>Tea</h5>
              <p>5 LE</p>
              <p>Quantity: 1</p>
            </div>
          </div>
          <div class="item">
            <img src="images/coffee.png" alt="Coffee">
            <div class="item_info">
              <h5>Coffee</h5>
              <p>6 LE</p>
              <p>Quantity: 1</p>
            </div>
          </div>
          <div class="item">
            <img src="images/nescafe.png" alt="Nescafe">
            <div class="item_info">
              <h5>Nescafe</h5>
              <p>8 LE</p>
              <p>Quantity: 1</p>
            </div>
          </div>
          <div class="item">
            <img src="images/cola.png" alt="Cola">
            <div class="item_info">
              <h5>Cola</h5>
              <p>10 LE</p>
              <p>Quantity: 1</p>
            </div>
          </div>
        </div>
        <div class="total">
          <h4>Total: EGP 104</h4>
        </div>
      </div>
    </div>

                </div>
              </td>
              <td>Done</td>
              <td>29 EGP</td>
              <td><button class="btn btn-danger">CANCEL</button></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const toggleDetails = document.querySelectorAll('.toggle-details');

  toggleDetails.forEach(function(toggle) {
    toggle.addEventListener('click', function() {
      const orderDetails = this.nextElementSibling;
      if (orderDetails.style.display === "none") {
        orderDetails.style.display = "block";
        this.setAttribute('data-toggle', '-');
        this.textContent = '-';
      } else {
        orderDetails.style.display = "none";
        this.setAttribute('data-toggle', '+');
        this.textContent = '+';
      }
    });
  });
});
</script>

<?php include_once 'footer.php';?>