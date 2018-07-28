<?php
    require '../config/config.php';

    // Validate all inputs (username unique, passwords match, etc)
    if (!isset($_POST['username']) || empty($_POST['username'])) {
        header("Location: ../login/login.php");
    }
    else if (!isset($_POST['id']) || empty($_POST['id'])) {
        $error = "Must provide a valid POI ID";
    }
    else if (!isset($_POST['title']) || empty($_POST['title'])) {
        $error = "Title cannot be empty";
    }
    else {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$mysqli->connect_errno) {
            $mysqli->set_charset('utf8');

            $img = "null";
            if (isset($_POST['img']) && !empty($_POST['img'])) {
                $img = "'".$_POST['img']."'";
            }
            $type = "null";
            if (isset($_POST['type']) && !empty($_POST['type'])) {
                $type = $_POST['type'];
            }
            $desc = "null";
            if (isset($_POST['desc']) && !empty($_POST['desc'])) {
                $desc = "'".$_POST['desc']."'";
            }
            $sql = "UPDATE pois
                    SET poi_title='".$_POST['title']."',
                        image_url=".$img.",
                        type_id=".$type.",
                        description=".$desc."
                    WHERE poi_id = ".$_POST['id'].";";

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
