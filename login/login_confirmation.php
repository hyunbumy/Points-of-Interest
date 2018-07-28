<?php

    require '../config/config.php';

    if (!isset($_POST['username']) || empty($_POST['username']) || !isset($_POST['pw']) || empty($_POST['pw'])) {
        $error = "Please enter a username and the password";
    }
    else {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$mysqli->connect_errno) {
            $mysqli->set_charset('utf8');

            $password = hash('sha256', $_POST['pw']);
            $sql = "SELECT * FROM users WHERE username ='" . $_POST['username'] . "' AND
                    password = '" . $password . "';";

            $result = $mysqli->query($sql);
            if (!$result) {
                $error = $mysqli->error;
            }
            else {
                if ($result->num_rows == 1) {
                    $_SESSION['username'] = $_POST['username'];
                    $_SESSION['user_id'] = ($result->fetch_assoc())['user_id'];
                }
                else {
                    $error = "User with the given credentials does not exist";
                }
            }

            $mysqli->close();
        }
        else {
            $error = $mysqli->error;
        }
    }

    if (isset($error) && !empty($error))
        echo $error;
    else
        echo "success";
?>
