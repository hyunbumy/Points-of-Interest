<?php
    require 'config/config.php';

    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        header('Location: login/login.php');
    }
    else {
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
                    WHERE p.user_id = ".$_SESSION['user_id'].";";
            $results = $mysqli->query($sql);
            if ( $results == false ) {
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
    <title>MyPOI</title>
    <link rel="stylesheet" type="text/css" href="config/main-style.css" />
</head>
<body>
    <?php include 'config/nav.php'; ?>
    <div class="blank mb-4"></div>
    <div class="container">
		<h1 class="col-12 mb-4">MyPOIs</h1>
        <div id="error">
            <?php if (isset($error) && !empty($error)) echo $error; ?>
        </div>

        <?php if (!isset($error) || empty($error)): ?>
        <div class="row mb-4">
            <div class="col">
                <table class="table table-hover">
                    <thead>
						<tr class="d-flex">
							<th class="col-2"></th>
							<th class="col-sm-10 col-md-6 col-lg-4 table-row">Title</th>
                            <th class="d-none d-md-block col-md-4 col-lg-3 table-row">City</th>
                            <th class="d-none d-lg-block col-md-4 col-lg-3 table-row">Country</th>
						</tr>
					</thead>
                    <tbody>
                        <?php while($row = $results->fetch_assoc()): ?>
                        <tr class="d-flex">
                            <td class="col-2">
                                <a href="#" class="btn btn-outline-danger delete" data-value="<?php echo $row['poi_id']; ?>">
                    				Delete
                    			</a>
                            </td>
                            <td class="col-sm-10 col-md-6 col-lg-4 table-row">
                                <a href="details.php?id=<?php echo $row['poi_id']; ?>">
                                    <?php echo $row['poi_title']; ?>
                                </a>
                            </td>
                            <td class="d-none d-md-block col-md-4 col-lg-3 table-row"><?php echo $row['city_name']; ?></td>
                            <td class="d-none d-lg-block col-md-4 col-lg-3 table-row"><?php echo $row['country_name']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
	</div> <!-- .container-fluid -->

    <script>
        $(document).ready(function() {
            $('.delete').click(function() {
                var curr = $(this);
                if(confirm("You are about to delete this entry")) {
                    // Ajax
                    $(this).prop('disabled', true);
                    $(this).text("Deleting...");

                    $.post('backend/delete_back.php',
                    {
                        username: '<?php echo $_SESSION['username']; ?>',
                        id: $(this).data("value")
                    },
                    function(data, status) {
                        if (data === "success")
                            window.location.replace("mypoi.php");
                        else
                            alert(data);
                        curr.prop('disabled', false);
                        curr.text("Delete");
                    });
                }
                return false;
            });
        });
    </script>
</body>
</html>
