<?php
    require '../config/config.php';

    if (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
        header('Location: ../index.php');
    }
?>
<!DOCTYPE html>
<html>
<head>
    <?php include '../config/head.php'; ?>
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="../config/login-style.css" />
</head>
<body>
    <div id="wrapper">

        <div id="header">
            <h1 id="logo" style="cursor:pointer;">POI</h1>
            <h3 style="color:black;">Registration</h1>
        </div>

        <div id="container">
            <div id="error" class="errormsg"></div>
            <form id="join-form" method="POST" action="register_confirmation.php">
                <fieldset id="form-set">
                    <div class="form-group">
                        <input type="text" class="form-control" id="username" placeholder="Username" name="username">
                        <div id="username-error" class="errormsg"></div>
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" id="email" placeholder="Email" name="email">
                        <div id="email-error" class="errormsg"></div>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" id="pw" placeholder="Password" name="pw">
                        <div id="pw-error" class="errormsg"></div>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" id="pw-confirm" placeholder="Password Confirm" name="confirm">
                        <div id="confirm-error" class="errormsg"></div>
                    </div>
                </fieldset>
                <button type="submit" class="btn btn-primary" id="submit-btn">Register</button>
            </form>

            <p class="login-callout">
                <a href="login.php">Sign in</a>
            </p>
        </div>

    </div>

    <script>

        var uValid = false;
        var eValid = false;
        var pValid = false;
        var cValid = false;

        // Username check: only allow at least one alphanumeric character
        document.querySelector("#username").onblur = function() {
            var username = this.value;
            var regExObj = new RegExp("^[a-z0-9]+$");

            if (!regExObj.test(username)) {
                document.querySelector("#username-error").innerHTML = "Username must consist of at least 1 lowercase alphanumeric character";
                uValid = false;
            }
            else {
                document.querySelector("#username-error").innerHTML = "";
                uValid = true;
            }
        }

        // Email check: must contain 1 @ sign
        document.querySelector("#email").onblur = function() {
            var email = this.value;
            var regExObj = new RegExp("@{1}");

            if (!regExObj.test(email)) {
                document.querySelector("#email-error").innerHTML = "Email must contain 1 @ character";
                eValid = false;
            }
            else {
                document.querySelector("#email-error").innerHTML = "";
                eValid = true;
            }
        }

        // Password check: must be at least 5 characters
        document.querySelector("#pw").onblur = function() {
            var password = this.value;
            var regExObj = new RegExp("^.{5,}$");

            if (!regExObj.test(password)) {
                document.querySelector("#pw-error").innerHTML = "Password must be at least 5 characters";
                pValid = false;
            }
            else {
                document.querySelector("#pw-error").innerHTML = "";
                pValid = true;
            }
        }

        // Confirmation check: must match the password
        document.querySelector("#pw-confirm").onblur = function() {
            var confirm = this.value;
            var password = document.querySelector("#pw").value;

            if (confirm !== password) {
                document.querySelector("#confirm-error").innerHTML = "It does not match the password";
                cValid = false;
            }
            else {
                document.querySelector("#confirm-error").innerHTML = "";
                cValid = true;
            }
        }

        function checkValid() {
            document.querySelector("#username").focus();
            document.querySelector("#username").blur();
            document.querySelector("#email").focus();
            document.querySelector("#email").blur();
            document.querySelector("#pw").focus();
            document.querySelector("#pw").blur();
            document.querySelector("#pw-confirm").focus();
            document.querySelector("#pw-confirm").blur();
            return (uValid && eValid && pValid && cValid);
        }

        // jQuery AJAX for server side validation and user addition
        $(document).ready(function() {
            $("#submit-btn").click(function() {
                if (checkValid()) {
                    $(this).prop('disabled', true);
                    $(this).text("Please wait...");
                    $.post("register_confirmation.php",
                    {
                        username: $("#username").val(),
                        email: $("#email").val(),
                        pw: $("#pw").val(),
                        confirm: $("#pw-confirm").val()
                    },
                    function(data, status) {
                        if (data === "success")
                            // Redirect to main logged in
                            window.location.replace("login.php");
                        else
                            $("#error").text(data);
                        $("#submit-btn").prop('disabled', false);
                        $("#submit-btn").text("Register");
                    });
                }
            });

            $("#join-form").submit(function() {
                return false;
            });

            $('#logo').click(function() {
                window.location.replace("../index.php");
            });
        });

    </script>

</body>
</html>
