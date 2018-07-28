<?php
    require 'config/config.php';

    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        header('Location: login/login.php');
    }
    else if (!isset($_GET['id']) || empty($_GET['id'])) {
        $error = "Must provide a valid id";
    }
    else {
        // DB Connection
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ( $mysqli->connect_errno ) {
        	$error = $mysqli->connect_error;
        }
        else {
            $mysqli->set_charset('utf8');

            $sql = "SELECT * FROM pois WHERE poi_id=".$_GET['id'].";";
            $results = $mysqli->query($sql);
            if ( $results == false ) {
            	$error = $mysqli->error;
            }
            else {
                $row = $results->fetch_assoc();
            }

            $sql_types = "SELECT * FROM types";
            $results_types = $mysqli->query($sql_types);
            if ( $results_types == false ) {
                $error = $mysqli->error;
            }
            $mysqli->close();
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <?php include 'config/head.php'; ?>
    <title>Update</title>
    <link rel="stylesheet" type="text/css" href="config/main-style.css" />
</head>
<body>
    <?php include 'config/nav.php'; ?>
    <div class="blank"></div>
    <div class="container mt-3">
        <div id="error" class="mb-3">
            <?php if(isset($error) && !empty($error)) echo $error; ?>
        </div>
        <?php if(!isset($error) || empty($error)): ?>
        <h1>Edit <i><?php echo $row['poi_title']; ?></i></h1>
        <hr />
        <form>
            <div class="form-row row mb-3">
                <label for="title" class="col-2 col-form-label">Title:</label>
                <div class="col-10">
                    <input type="text" class="form-control" id="title" placeholder="POI Title" value="<?php echo $row['poi_title']; ?>"/>
                </div>
            </div>
            <div class="form-row row mb-3">
                <label for="image" class="col-2 col-form-label">Img:</label>
                <div class="col-10">
                    <input type="text" class="form-control" id="image" placeholder="http://adsfajsdf.com" value="<?php echo $row['image_url']; ?>"/>
                </div>
            </div>
            <div class="form-row row mb-3">
                <label for="type" class="col-2 col-form-label">Type:</label>
                <div class="col-10">
                    <select id="type" class="form-control">
                        <option value="" selected>-- Select a Type --</option>
                        <?php while( $row_type = $results_types->fetch_assoc() ): ?>

							<!-- If the media type is this specific track's media type, then select it by default -->
							<?php if($row_type['type_id'] == $row['type_id']) : ?>
							<option value="<?php echo $row_type['type_id']; ?>" selected>
								<?php echo $row_type['type_name']; ?>
							</option>

							<?php else: ?>
							<option value="<?php echo $row['type_id']; ?>">
								<?php echo $row_type['type_name']; ?>
							</option>

							<?php endif; ?>

						<?php endwhile; ?>
                    </select>
                </div>
            </div>
            <div class="form-row row mb-3">
                <label for="desc" class="col col-form-label">Description:</label>
                <div class="w-100"></div>
                <div class="col">
                    <textarea style="margin:0;" class="form-control" id="desc" rows="3" placeholder="Short Description"><?php echo $row['description']; ?></textarea>
                </div>
            </div>
            <div class="form-row row justify-content-end mb-2">
                <div class="col-auto">
                    <a href="#" id="update-btn" role="button" class="btn btn-outline-primary">Confirm</a>
                </div>
                <div class="col-auto">
                    <a href="details.php?id=<?php echo $_GET['id'];?>" role="button" class="btn btn-outline-dark">Back</a>
                </div>
            </div>
            <?php endif; ?>
        </form>
    </div>

    <script>
    $(document).ready(function() {
        $('#update-btn').click(function() {
            if(($.trim($('#title').val())).length > 0) {
                $('#title').removeClass('errormsg');
                // Ajax
                $(this).prop('disabled', true);
                $(this).text("Processing...");

                $.post('backend/update_back.php',
                {
                    username: '<?php echo $_SESSION['username']; ?>',
                    id: <?php echo $_GET['id']; ?>,
                    title: $('#title').val(),
                    img: $('#image').val(),
                    type: $('#type').val(),
                    desc: $('#desc').val()
                },
                function(data, status) {
                    if (data === "success")
                        window.location.replace("mypoi.php");
                    else
                        alert(data);
                    $('#update-btn').prop('disabled', false);
                    $('#update-btn').text("Confirm");
                });
            }
            else {
                console.log("emtpy");
                $('#title').addClass('errormsg');
            }
            return false;
        });
    });
    </script>
</body>
</html>
