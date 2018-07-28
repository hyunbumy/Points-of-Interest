<?php
    require '../config/config.php';

    // Validate all inputs (username unique, passwords match, etc)
    if (!isset($_POST['username']) || empty($_POST['username'])) {
        header("Location: ../login/login.php");
    }
    else if (!isset($_POST['id']) || empty($_POST['id'])) {
        $error = "POI ID must be valid";
    }
    else {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$mysqli->connect_errno) {
            $mysqli->set_charset('utf8');

            $sql = "SELECT *
                    FROM pois p
                    INNER JOIN bookmarks b ON p.poi_id = b.poi_id
                    WHERE p.poi_id =" . $_POST['id'] . ";";

            $results = $mysqli->query($sql);
            if (!$results) {
                $error = $mysqli->error;
            }
            else {
                $success = true;
                while($row = $results->fetch_assoc()) {
                    $sql = "DELETE FROM bookmarks WHERE bookmark_id =".$row['bookmark_id'].";";
                    $results_t = $mysqli->query($sql);
                    if (!$results_t) {
                        $error = $mysqli->error;
                        $success = false;
                        break;
                    }
                }
                if ($success) {
                    $sql_del = "DELETE FROM pois WHERE poi_id =".$_POST['id'].";";
                    $results_del = $mysqli->query($sql_del);
                    if (!$results_del) {
                        $error = $mysqli->error;
                    }
                }
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
