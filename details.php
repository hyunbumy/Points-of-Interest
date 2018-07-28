<?php
    require 'config/config.php';

    if (!isset($_GET['id']) || empty($_GET['id'])) {
        $error = "No POI ID given";
    }
    else {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$mysqli->connect_errno) {
            $mysqli->set_charset('utf8');

            $sql = "SELECT *
                    FROM pois p
                    INNER JOIN users u ON u.user_id = p.user_id
                    LEFT JOIN types t ON t.type_id = p.type_id
                    WHERE p.poi_id =" . $_GET['id'] . ";";

            $results = $mysqli->query($sql);
            if (!$results) {
                $error = $mysqli->error;
            }
            else {
                $marked = false;
                $same_user = false;

                $row = $results->fetch_assoc();
                $img = "img/default.jpg";
                if ($row['image_url'] != null)
                    $img = $row['image_url'];
                $desc = "";
                if ($row['description'] != null)
                    $desc = $row['description'];

                if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {

                    if ($_SESSION['username'] == $row['username'])
                        $same_user = true;

                    $sql_book = "SELECT * FROM bookmarks WHERE user_id=".$_SESSION['user_id']." AND poi_id=".$_GET['id'].";";

                    $result = $mysqli->query($sql_book);
                    if(!$result) {
                        $error = $mysqli->error;
                    }
                    else if($result->num_rows > 0) {
                        $marked = true;
                    }
                }
            }
            $mysqli->close();
        }
        else {
            $error = $mysqli->connect_error;
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <?php include 'config/head.php'; ?>
    <title>Detail</title>
    <link rel="stylesheet" type="text/css" href="config/main-style.css" />
</head>
<body>
    <?php include 'config/nav.php'; ?>
    <div class="blank"></div>
    <div class="container">
        <div id="error">
            <?php if(isset($error) && !empty($error)) echo $error; ?>
        </div>

        <?php if (!isset($error) || empty($error)): ?>
        <div class="row">
            <div class="col-md" id="detail-info">
                <div class="row">
                    <div class="col-md-auto" id="title">
                        <h2><?php echo $row['poi_title']; ?></h2>
                    </div>
                    <div class="col-md-auto" id="user">
                        <span style="font-style:italic;"><?php echo $row['username']; ?></span>
                    </div>
                </div>
                <div class="row" id="secondary-info">
                    <div class="col-md-auto" id="address">
                        <span><?php echo $row['address']; ?></span>
                    </div>
                    <div class="col-md-auto" id="type">
                        <span><?php echo $row['type_name']; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row body-row">
            <div class="col-md-8" id="detail-img">
                <img onerror="this.src='img/default.jpg';" src="<?php echo $img; ?>" />
            </div>
            <div class="col-md-4">
                <div class="row h-100">
                    <div class="col h-100" id="desc">
                        <h4 style="text-decoration:underline;">About</h4>
                        <p style="word-break:break-all;">
                            <?php echo $desc; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row body-row">
            <div class="col">
                <div id="location-map"></div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row justify-content-end mb-2">
            <?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])): ?>
                <?php if(!$marked): ?>
                <div class="col-auto">
                    <a id="bookmark-btn" role="button" class="btn btn-outline-primary">Bookmark</a>
                </div>
                <?php else: ?>
                <div class="col-auto">
                    <a id="bookmark-btn" role="button" class="btn btn-outline-primary">Un-Bookmark</a>
                </div>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($same_user): ?>
            <div class="col-auto">
                <a href="update.php?id=<?php echo $_GET['id']; ?>" id="update-btn" role="button" class="btn btn-outline-warning">Update</a>
            </div>
            <?php endif; ?>
            <div class="col-auto">
                <a href="index.php" role="button" class="btn btn-outline-dark">Back</a>
            </div>
        </div>

    </div>
    <?php if (!isset($error) || empty($error)): ?>
    <script>
        function initMap() {
            var r_lat = <?php echo $row['poi_lat']; ?>;
            var r_lng = <?php echo $row['poi_lng']; ?>;
            var pos = {lat: r_lat, lng: r_lng};
            var map = new google.maps.Map(document.getElementById('location-map'), {
                zoom:15,
                center: pos
            });
            var marker = new google.maps.Marker({
                position: pos,
                map: map
            });
        }

        $('document').ready(function() {
            $('#bookmark-btn').click(function() {
                // Make an AJAX call to bookmark.php to add to bookmarks for the user
                $(this).prop('disabled', true);
                var text = $(this).text();
                $(this).text("Processing...");

                var marked = <?php if($marked) echo 'true'; else echo 'false'?>;
                $.post('backend/bookmark_back.php',
                {
                    user_id: '<?php if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) echo $_SESSION['user_id']; ?>',
                    id: <?php echo $_GET['id']; ?>,
                    mode: marked
                },
                function(data, status) {
                    if (data === "success")
                        window.location.replace("details.php?id=<?php echo $_GET['id']; ?>");
                    else
                        alert(data);
                    $('#bookmark-btn').prop('disabled', false);
                    $('#bookmark-btn').text(text);
                });
            });
        });
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB4Jkq6sZlGQdK6_9qqW0LJ5aFIElBImjg&callback=initMap"></script>
    <?php endif; ?>
</body>
</html>
