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
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="../config/login-style.css" />
</head>
<body>
    <div id="wrapper">
        <div id="header">
            <h1 id="logo" style="cursor:pointer;">POI</h1>
            <h3 style="color:black;">Sign in</h3>
        </div>

        <div id="container">
            <div id="error" class="errormsg"></div>
            <form id="join-form" action="" onsubmit="return false">
                <fieldset id="form-set">
                    <div class="form-group">
                        <input type="text" class="form-control" id="username" placeholder="Username" name="username">
                        <div id="username-error" class="errormsg"></div>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" id="pw" placeholder="Password" name="pw">
                        <div id="pw-error" class="errormsg"></div>
                    </div>
                </fieldset>
                <button type="submit" class="btn btn-primary" id="submit-btn">Sign in</button>
            </form>

            <p class="create-account-callout">
                <a href="register.php">Create an account</a>
            </p>
        </div>

    </div>

    <script>
        $(document).ready(function() {
            $("#submit-btn").click(function() {
                var isValid = true;
                if ($("#username").val().length == 0) {
                    $("#username-error").text("Please enter a username");
                    isValid = false;
                }
                else {
                    $("#username-error").text("");
                }
                if ($("#pw").val().length == 0) {
                    $("#pw-error").text("Please enter a password");
                    isValid = false;
                }
                else {
                    $("#pw-error").text("");
                }
                if (isValid) {
                    $(this).prop('disabled', true);
                    $(this).text("Signing in...");

                    // AJAX to sign in
                    $.post("login_confirmation.php",
                    {
                        username: $("#username").val(),
                        pw: $("#pw").val()
                    },
                    function(data, status) {
                        if (data === "success")
                            window.location.replace("../index.php");
                        else
                            $("#error").text(data);
                        $("#submit-btn").prop('disabled', false);
                        $("#submit-btn").text("Sign in");
                    });
                }
            });
            $('#logo').click(function() {
                window.location.replace("../index.php");
            });
        });
    </script>

</body>
</html>
