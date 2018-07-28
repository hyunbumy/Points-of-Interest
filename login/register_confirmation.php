<?php
    require '../config/config.php';

    // Validate all inputs (username unique, passwords match, etc)
    if (!isset($_POST['username']) || empty($_POST['username'])) {
        $error = "Username must contain at least 1 alphanumeric character";
    }
    else if (!isset($_POST['email']) || empty($_POST['email'])) {
        $error = "Please provide a valid email";
    }
    else if (!isset($_POST['pw']) || empty($_POST['pw']) || (strlen($_POST['pw']) < 5)) {
        $error = "Password must be at least 5 characters";
    }
    else if (!isset($_POST['confirm']) || empty($_POST['confirm']) || ($_POST['pw'] != $_POST['confirm'])) {
        $error = "Passwords do not match";
    }
    else {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$mysqli->connect_errno) {
            $mysqli->set_charset('utf8');

            $sql_registered = "SELECT * FROM users WHERE username = '" . $_POST['username'] . "' OR email = '" . $_POST['email'] . "';";
            $results_registered = $mysqli->query($sql_registered);
            if ($results_registered) {
                if ($results_registered->num_rows > 0) {
                    $error = "Username or email already exists";
                }
                else {
                    // Add new user record
                    $password = hash('sha256', $_POST['pw']);
                    $sql = "INSERT INTO users(username, email, password) VALUES('"
            			. $_POST['username'] . "', '" .$_POST['email'] . "', '" . $password . "');";
                    $results = $mysqli->query($sql);
                    if (!$results) {
                        $error = $mysqli->error;
                    }
                }
            }
            else {
                $error = $mysqli->error;
            }

            $mysqli->close();
        }
        else {
            $error = $mysqli->connect_error;
        }
    }

    // Respond with the appropriate response
    if (isset($error) && !empty($error)) {
        echo $error;
    }
    else {
        echo "success";
    }
?>
