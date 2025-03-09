<?php include_once 'header2.php';?>





<h1 class="text-center mb-4">Add New Product</h1>

<form action="" method="post" class="form1 text-center" >
    
    <table class="product custom-table text-center" >
        <tr>
            <th><label for="product">Product Name</label></th>
            <td><input type="text" id="product" name="product" class="form-control" placeholder="Enter product name" required></td>
        </tr>

        <tr>
            <th><label for="price">Price</label></th>
            <td>
                <input type="number" id="price" name="price" class="form-control" step="0.01" required> EGP
            </td>
        </tr>

        <tr>
            <th><label for="dropdown">Category</label></th>
            <td>
                <select id="dropdown" name="category" class="form-select">
                    <option value="Hot Drink">Hot Drink</option>
                    <option value="Cold Drinks">Cold Drinks</option>
                    <option value="Healthy Drinks">Healthy Drinks</option>
                    <option value="Appetizers">Appetizers</option>
                    <option value="Fast Food">Fast Food</option>
                    <option value="Main Dishes">Main Dishes</option>
                    <option value="Seafood">Seafood</option>
                    <option value="Healthy Food">Healthy Food</option>
                </select>
            </td>
        </tr>

        <tr>
            <th><label for="img">Product Image</label></th>
            <td><input type="file" id="img" name="img" class="form-control" accept="image/*"></td>
        </tr>
        <tr class="button-row">
            <td colspan="2">
                <div class="text-center">
                    <input type="submit" class="btn btn-success px-5" value="Save">
                    <input type="reset" class="btn btn-primary px-5" value="Reset">
                </div>
            </td>
        </tr>
    </table>
</form>


<?php include_once 'footer.php';?>
