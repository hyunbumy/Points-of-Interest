<?php
    require '../config/config.php';

    // Validate all inputs (username unique, passwords match, etc)
    if (!isset($_POST['user_id']) || empty($_POST['user_id'])) {
        header("Location: ../login/login.php");
    }
    else if (!isset($_POST['id']) || empty($_POST['id'])) {
        $error = "POI ID must be valid";
    }
    else if (!isset($_POST['mode']) || empty($_POST['mode'])) {
        $error = "Bookmark mode not set";
    }
    else {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$mysqli->connect_errno) {
            $mysqli->set_charset('utf8');

            if ($_POST['mode'] == 'true') {
                $sql = "DELETE FROM bookmarks WHERE user_id=".$_POST['user_id']." AND poi_id=".$_POST['id'].";";
            }
            else {
                $sql = "INSERT INTO bookmarks(user_id, poi_id)
                        VALUES(".$_POST['user_id'].", ".$_POST['id'].");";
            }

            $results = $mysqli->query($sql);
            if (!$results) {
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
