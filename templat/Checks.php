<?php include_once 'header2.php';?>

<!-- Checks Section -->
<section class="checks_section layout_padding">
  <div class="container">
    <h2 class="section_heading">Checks</h2>

    <!-- Filters -->
    <form id="filterForm">
      <div class="row mb-4">
        <div class="col-md-6">
          <label for="dateFrom">Date from</label>
          <input type="date" class="form-control" id="dateFrom" name="dateFrom">
        </div>
        <div class="col-md-6">
          <label for="dateTo">Date to</label>
          <input type="date" class="form-control" id="dateTo" name="dateTo">
        </div>
      </div>
      <div class="row mb-4">
        <div class="col-md-6">
          <label for="userSelect">User</label>
          <select class="form-control" id="userSelect" name="userSelect">
            <option value="">All Users</option>
            <option value="user1">User 1</option>
            <option value="user2">User 2</option>
            <!-- Add more users as needed -->
          </select>
        </div>
      </div>
    </form>

    <!-- Checks Table -->
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Name</th>
            <th>Total Amount</th>
          </tr>
        </thead>
        <tbody id="checksTableBody">
          <tr>
            <td>
              <span class="toggle-details" data-toggle="+">+</span> Abdulrahman Hamdy
              <div class="orders" style="display:none;">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Order Date</th>
                      <th>Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>
                        <span class="toggle-order-details" data-toggle="+">+</span> 2015/02/02 10:30 AM
                        <div class="order-details" style="display:none;">
                          <p>Item 1: 5 EGP</p>
                          <p>Item 2: 10 EGP</p>
                          <!-- Add more items as needed -->
                        </div>
                      </td>
                      <td>55 EGP</td>
                    </tr>
                    <tr>
                      <td>
                        <span class="toggle-order-details" data-toggle="+">+</span> 2015/02/01 11:30 AM
                        <div class="order-details" style="display:none;">
                          <p>Item 1: 10 EGP</p>
                          <p>Item 2: 10 EGP</p>
                          <!-- Add more items as needed -->
                        </div>
                      </td>
                      <td>20 EGP</td>
                    </tr>
                    <!-- Add more orders as needed -->
                  </tbody>
                </table>
              </div>
            </td>
            <td>110</td>
          </tr>
          <tr>
            <td>
              <span class="toggle-details" data-toggle="+">+</span> Islam Askar
              <div class="orders" style="display:none;">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Order Date</th>
                      <th>Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>
                        <span class="toggle-order-details" data-toggle="+">+</span> 2015/02/02 10:30 AM
                        <div class="order-details" style="display:none;">
                          <p>Item 1: 5 EGP</p>
                          <p>Item 2: 10 EGP</p>
                          <!-- Add more items as needed -->
                        </div>
                      </td>
                      <td>55 EGP</td>
                    </tr>
                    <!-- Add more orders as needed -->
                  </tbody>
                </table>
              </div>
            </td>
            <td>500</td>
          </tr>
          <!-- Add more users as needed -->
        </tbody>
      </table>
    </div>
  </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const toggleDetails = document.querySelectorAll('.toggle-details');
  const toggleOrderDetails = document.querySelectorAll('.toggle-order-details');

  toggleDetails.forEach(function(toggle) {
    toggle.addEventListener('click', function() {
      const row = this.closest('tr');
      const isExpanded = this.getAttribute('data-toggle') === '+';
      this.setAttribute('data-toggle', isExpanded ? '-' : '+');
      this.textContent = isExpanded ? '-' : '+';

      const orders = row.querySelector('.orders');
      if (isExpanded) {
        orders.style.display = 'block';
      } else {
        orders.style.display = 'none';
      }
    });
  });

  toggleOrderDetails.forEach(function(toggle) {
    toggle.addEventListener('click', function() {
      const row = this.closest('tr');
      const isExpanded = this.getAttribute('data-toggle') === '+';
      this.setAttribute('data-toggle', isExpanded ? '-' : '+');
      this.textContent = isExpanded ? '-' : '+';

      const orderDetails = row.querySelector('.order-details');
      if (isExpanded) {
        orderDetails.style.display = 'block';
      } else {
        orderDetails.style.display = 'none';
      }
    });
  });
});
</script>
<?php include_once 'footer.php';?>
