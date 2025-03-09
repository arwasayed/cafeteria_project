<?php include_once 'header2.php';?>

<h1 class="text-center mb-4">Add User</h1>

<form action="index.php" method="post" class="form1 text-center row g-3 needs-validation" id="signupForm" novalidate>
    <table class="product custom-table text-center">
        <tr>
            <th><label for="name">User Name</label></th>
            <td>
                <input type="text" class="form-control" id="name" name="name" required placeholder="Enter user name">
                <div class="invalid-feedback">Please enter a username.</div>
            </td>
        </tr>

        <tr>
            <th><label for="email">Email</label></th>
            <td>
                <div class="input-group has-validation">
                    <span class="input-group-text">@</span>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="Enter email">
                    <div class="invalid-feedback">Please enter a valid email.</div>
                </div>
            </td>
        </tr>

        <tr>
            <th><label for="pass">Password</label></th>
            <td>
                <input type="password" id="pass" name="password" class="form-control" required placeholder="Enter password">
                <div class="invalid-feedback">Must be at least 8 characters including numbers, 2 uppercase, and 2 lowercase letters.</div>
            </td>
        </tr>

        <tr>
            <th><label for="cnPass">Confirm Password</label></th>
            <td>
                <input type="password" id="cnPass" name="confirm_password" class="form-control" required placeholder="Confirm password">
                <div class="invalid-feedback">Confirm password does not match.</div>
            </td>
        </tr>

        <tr>
            <th><label for="Rno">Room No</label></th>
            <td>
                <input type="number" id="Rno" name="Room" class="form-control" required placeholder="Enter room number">
                <div class="invalid-feedback">Please enter a room number.</div>
            </td>
        </tr>

        <tr>
            <th><label for="Ext">Ext</label></th>
            <td>
                <input type="number" id="Ext" name="Ext" class="form-control" required placeholder="Enter extension">
                <div class="invalid-feedback">Please enter an extension number.</div>
            </td>
        </tr>

        <tr>
            <th><label for="img">Profile Picture</label></th>
            <td>
                <input type="file" id="img" name="img" class="form-control" accept="image/*" required>
                <div class="invalid-feedback">Please upload a profile picture.</div>
            </td>
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

<script>
document.getElementById('signupForm').addEventListener('submit', function(event) {
    let valid = true; 
    event.preventDefault(); 

    // Get input values
    const name = document.getElementById('name');
    const email = document.getElementById('email');
    const password = document.getElementById('pass');
    const confirmPassword = document.getElementById('cnPass');
    const room = document.getElementById('Rno');
    const ext = document.getElementById('Ext');
    const img = document.getElementById('img');

    // Regex patterns
    const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Validate required fields
    function checkField(field) {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            valid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    }

    checkField(name);
    checkField(email);
    checkField(password);
    checkField(confirmPassword);
    checkField(room);
    checkField(ext);
    checkField(img);

    // Validate email
    if (!emailRegex.test(email.value)) {
        email.classList.add('is-invalid');
        valid = false;
    } else {
        email.classList.remove('is-invalid');
    }

    // Validate password
    if (!passwordRegex.test(password.value)) {
        password.classList.add('is-invalid');
        valid = false;
    } else {
        password.classList.remove('is-invalid');
    }

    // Validate confirm password
    if (password.value !== confirmPassword.value) {
        confirmPassword.classList.add('is-invalid');
        valid = false;
    } else {
        confirmPassword.classList.remove('is-invalid');
    }

    // Submit the form if valid
    if (valid) {
        this.submit();
    }
});
</script>

<?php include_once 'footer.php';?>
