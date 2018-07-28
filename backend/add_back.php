<?php
    require '../config/config.php';

    // Validate all inputs (username unique, passwords match, etc)
    if (!isset($_POST['username']) || empty($_POST['username'])) {
        header("Location: ../login/login.php");
    }
    else if (!isset($_POST['title']) || empty($_POST['title'])) {
        $error = "Title cannot be empty";
    }
    else if (!isset($_POST['lat']) || empty($_POST['lat']) || (!isset($_POST['lng'])) || empty($_POST['lng'])) {
        $error = "Please specify the location on the map";
    }
    else {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$mysqli->connect_errno) {
            $mysqli->set_charset('utf8');

            $sql_city = "SELECT *,
            111.045 * DEGREES(ACOS(COS(RADIANS(" . $_POST['lat'] . "))
	        * COS(RADIANS(city_lat))
            * COS(RADIANS(city_lng) - RADIANS(" . $_POST['lng'] . "))
            + SIN(RADIANS(" . $_POST['lat'] . "))
            * SIN(RADIANS(city_lat))))
            AS dist
            FROM cities
            WHERE city_name LIKE '%" . $_POST['city'] . "%'
            ORDER BY dist
            LIMIT 0,1;";

            $results_city = $mysqli->query($sql_city);
            if (!$results_city) {
                $error = $mysqli->error;
            }
            else {
                $city = "null";
                if ($row = $results_city->fetch_assoc()) {
                    $city = $row['city_id'];
                }
                $type = "null";
                if (isset($_POST['type']) && !empty($_POST['type'])) {
                    $type = $_POST['type'];
                }
                $addr = "null";
                if (isset($_POST['addr']) && !empty($_POST['addr'])) {
                    $addr = "'".$_POST['addr']."'";
                }
                $img = "null";
                if (isset($_POST['img']) && !empty($_POST['img'])) {
                    $img = "'".$_POST['img']."'";
                }
                $desc = "null";
                if (isset($_POST['desc']) && !empty($_POST['desc'])) {
                    $desc = "'".$_POST['desc']."'";
                }
                $sql =
                "INSERT INTO pois(user_id, poi_title, type_id, address, city_id, image_url, description, poi_lat, poi_lng)
                VALUES(".$_SESSION['user_id'].", '".$_POST['title']."', ".$type.", ".$addr.", ".$city.", ".$img.", ".$desc.", ".$_POST['lat'].", ".$_POST['lng'].");";

                $results = $mysqli->query($sql);
                if (!$results) {
                    $error = $mysqli->error;
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
