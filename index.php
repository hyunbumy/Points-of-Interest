<?php
    require 'config/config.php';

    // DB Connection
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ( $mysqli->connect_errno ) {
        $error = $mysqli->connect_error;
    }
    else {
        $mysqli->set_charset('utf8');

        $sql_countries = "SELECT * FROM countries";
        $results_countries = $mysqli->query($sql_countries);
        if ( $results_countries == false ) {
            $error = $mysqli->error;
        }

        $sql_types = "SELECT * FROM types";
        $results_types = $mysqli->query($sql_types);
        if ( $results_types == false ) {
            $error = $mysqli->error;
        }

        $mysqli->close();
    }
?>
<!DOCTYPE html>
<html>
<head>
    <?php include 'config/head.php'; ?>
    <title>Welcome</title>
    <link rel="stylesheet" type="text/css" href="config/main-style.css" />
</head>
<body>
    <?php include 'config/nav.php'; ?>
    <div class="blank"></div>
    <div class="row align-items-center banner">
        <div class="col">
            <div class="row justify-content-center">
                <div class="col welcome">
                    <h3>Find Points of Interest!</h3>
                </div>
            </div>
            <form class="search-form" action="" method="GET">
                <div class="row justify-content-center">
                    <div class="col-lg col-item">
                        <div class="input-group h-100">
                            <div class="input-group-prepend">
                                <div class="input-group-text" style="border-radius:0;">
                                    <input id="bmonly" type="checkbox">
                                </div>
                            </div>
                            <input type="text" class="form-control h-100" id="title" placeholder="Enter the name of POI"/>
                        </div>
                    </div>

                    <div class="col-lg col-item">
                        <select class="form-control h-100" id="country">
                            <option value="">-- All Countries --</option>

                            <?php while( $row = $results_countries->fetch_assoc() ): ?>
                                <option value="<?php echo $row['country_id']; ?>">
                                    <?php echo $row['country_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-lg col-item">
                        <select class="form-control h-100" id="type">
                            <option value="">-- All Types --</option>

                            <?php while( $row = $results_types->fetch_assoc() ): ?>
                                <option value="<?php echo $row['type_id']; ?>">
                                    <?php echo $row['type_name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-lg-auto h-100 col-item">
                        <button type="submit" class="btn btn-primary" id="filter">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="container main-container">

        <div class="row poi-row">

        </div>

    </div>

    <script>
        var start = 0;
        var limit = 8;
        var reachedMax = false;

        var bookmarked;
        var title;
        var type;
        var country;

        $(window).scroll(function() {
            if ($(window).scrollTop() == $(document).height() - $(window).height())
                getData();
        });

        $(document).ready(function() {
            getData();

            $('#filter').click(function() {
                bookmarked = $('#bmonly').prop("checked") ? true : false;
                title = $.trim($('#title').val());
                type = $('#type').val();
                country = $('#country').val();

                // Clear the start counter
                start = 0;
                // Clear the existing data
                $('.poi-row').empty();
                // Clear reachMax
                reachedMax = false;

                getData();
                return false;
            });
        });

        // Load 8 items (4 items = a row) at a time
        // and then format the row

        // Depending on filter or not, do different query to the db

        function getData() {
            if (reachedMax)
                return;

            $.post('backend/fetchdata_back.php',
            {
                bookmarked: bookmarked,
                title: title,
                type: type,
                country: country,
                start: start,
                limit: limit
            },
            function(data, status) {
                if (status == "success") {
                    console.log(data);
                    var response = data;

                    if (response['error'].length > 0) {
                        alert(response['error']);
                    }
                    else if (response['maxReached']) {
                        reachedMax = true;
                    }
                    else {
                        start += limit;
                        var results = response['results'];
                        console.log(results);

                        for(var i=0; i<results.length; i++) {
                            var curr = results[i];
                            createDOM(curr['id'], curr['title'], curr['img'], curr['desc']);
                        }

                        // Redirect on poi entry click
                        $('.poi-item').click(function () {
                            window.location.replace("details.php?id="+this.id);
                        });
                    }
                }
                return;
            }, 'json');
        }

        function createDOM(id, title, img, desc) {
            if (desc != null) {
                if (desc.length > 25) {
                    desc = desc.substr(0,25) + "...";
                }
            }
            else {
                desc = "";
            }
            if (title.length > 20) {
                title = title.substr(0,20) +"...";
            }

            if (img == null)
                img = "";
            var dom =
            `<div class='col-sm col-md-6 col-lg-3'>
                <div class='card w-100 poi-item' id='${id}' style="height:278px">
                  <img onerror='this.src="img/default.jpg";' class='card-img-top' src='${img}' alt='Card image cap' style='height:60%'>
                  <div class='card-body'>
                    <h5 class='card-title'>${title}</h5>
                    <p class='card-text'>${desc}</p>
                  </div>
                </div>
            </div>`
            console.log(dom);
            $('.poi-row').append(dom);
        }
    </script>
</body>
</html>
