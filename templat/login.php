<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Log In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <link rel="stylesheet" href="./css/log_in.css" />
</head>

<body>
    <!-- <h1 class="con">HANDMADE</h1> -->
    <div class="container">
        <h1>LOG IN</h1>
        <hr />
        <form id="loginForm">
            <div class="col-md-12">
                <label for="validationCustomUsername" class="form-label">Email</label>
                <div class="input-group has-validation">
                    <span class="input-group-text" id="inputGroupPrepend">@</span>
                    <input type="email" class="form-control" id="validationCustomUsername" aria-describedby="inputGroupPrepend" required />
                    <div class="invalid-feedback">Please enter a valid email.</div>
                </div>
            </div>

            <div class="col-md-12 mt-3">
                <label for="validationCustom03" class="form-label">Password</label>
                <input type="password" class="form-control" id="validationCustom03" required />
                <div class="invalid-feedback">
                    The password that you've entered is incorrect.
                </div>
            </div>

            <div class="col-9 mt-4 md-3">
                <button class="btn btn-success submit-btn-contact rounded-pill" type="submit">
            Log in
          </button>
            </div>

            <!-- <div class="link col-12 mt-2 mb-1">
          <a href="./forget_password.html" style="text-decoration: none"
            >Forgot Password</a
          >
        </div> -->
            <hr width="50%" style="margin-left: 25%" />
            <div class="link col-12 mt-2">
                Don't Have an Account?
                <a href="sign_up.html" style="text-decoration: none">Sign Up</a>
            </div>
        </form>
    </div>

    <!--wooork-->
    <script>
        document.getElementById("loginForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Prevent default form submission

            // Collect form data
            const formData = {
                email: document.getElementById("validationCustomUsername").value,
                password: document.getElementById("validationCustom03").value,
            };

            // Password validation regex
            const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;

            // Email validation regex
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            // Check if password meets criteria
            if (!passwordRegex.test(formData.password)) {
                document.getElementById("validationCustom03").classList.add("is-invalid");
                return;
            }
            /*else {
                           document.getElementById("validationCustom03").classList.remove("is-invalid");
                       }*/

            // Check if email is valid
            if (!emailRegex.test(formData.email)) {
                document.getElementById("validationCustomUsername").classList.add("is-invalid");
                return;
            } else {
                //  document.getElementById("validationCustomUsername").classList.remove("is-invalid");
                window.location.href = "index.php";

            }


        });
    </script>



</body>

</html>