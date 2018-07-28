<?php
    require '../config/config.php';

    $error = "";
    // DB Connection
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ( $mysqli->connect_errno ) {
        $error = $mysqli->connect_error;
    }
    else {
        $mysqli->set_charset('utf8');

        $sql = "SELECT *
                FROM pois p
                LEFT JOIN cities c ON c.city_id = p.city_id
                LEFT JOIN countries ct ON ct.country_id = c.country_id
                LEFT JOIN types t ON p.type_id = t.type_id\n";

        if (isset($_POST['bookmarked']) && ($_POST['bookmarked'] == 'true')) {
            if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
                $sql .= "INNER JOIN
                        (SELECT poi_id FROM bookmarks WHERE user_id=".$_SESSION['user_id'].") book ON book.poi_id = p.poi_id ";
            }
        }
        $sql .= "WHERE 1=1";
        if (isset($_POST['title']) && !empty($_POST['title'])) {
            $sql .= " AND p.poi_title LIKE '%".$_POST['title']."%'";
        }
        if (isset($_POST['type']) && !empty($_POST['type'])) {
            $sql .= " AND p.type_id=".$_POST['type'];
        }
        if (isset($_POST['country']) && !empty($_POST['country'])) {
            $sql .= " AND c.country_id='".$_POST['country']."'";
        }

        $sql .= " LIMIT ".$_POST['start'].", ".$_POST['limit'].";";

        // echo $sql;
        // exit();

        $results = $mysqli->query($sql);
        if ( $results == false ) {
            $error = $mysqli->error;
        }
        else {
            $rows = [];
            $maxReached = false;
            if ($results->num_rows == 0) {
                $maxReached = true;
            }
            while($row = $results->fetch_assoc()) {
                $temp = [
                    "id" => $row['poi_id'],
                    "title" => $row['poi_title'],
                    "img" => $row['image_url'],
                    "desc" => $row['description']
                ];
                array_push($rows, $temp);
            }
            $response = [
                "results" => $rows,
                "error" => $error,
                "maxReached" => $maxReached
            ];

            echo json_encode($response);
        }
        $mysqli->close();
    }
?>
