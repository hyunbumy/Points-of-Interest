<?php
    require 'config/config.php';

    // See if the user is logged in
    if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
        header('Location: login/login.php');
    }

    // DB Connection
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ( $mysqli->connect_errno ) {
    	echo $mysqli->connect_error;
    	exit();
    }

    $mysqli->set_charset('utf8');

    // Types:
    $sql_types = "SELECT * FROM types;";
    $results_types = $mysqli->query($sql_types);
    if ( $results_types == false ) {
    	echo $mysqli->error;
    	exit();
    }

    // Close DB Connection
    $mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
    <?php include 'config/head.php'; ?>
    <title>Add POI</title>
    <link rel="stylesheet" type="text/css" href="config/main-style.css" />
</head>
<body>
    <?php include 'config/nav.php'; ?>
    <div class="blank"></div>
    <div class="container mt-4">
        <div id="error"></div>
        <form action="" onsubmit="return false">
            <div class="row mb-2 justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <input id="pac-input" class="form-control" type="text" placeholder="Search query here" />
                </div>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <div id="location-map"></div>
                </div>
            </div>

            <div class="row">
                <label for="title" class="col-3 col-lg-1 mb-3 col-form-label">Title:</label>
                <div class="col-9 col-lg-6 mb-3">
                    <input type="text" class="form-control" id="title" placeholder="POI Title" />
                </div>

                <label for="type" class="col-3 col-lg-1 mb-3 col-form-label">Type:</label>
                <div class="col-9 col-lg-4 mb-3">
                    <select id="type" class="form-control">
                        <option value="" selected disabled>-- Select One --</option>
                        <option value=""></option>

                        <?php while( $row = $results_types->fetch_assoc() ): ?>
                            <option value="<?php echo $row['type_id']; ?>">
                                <?php echo $row['type_name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <label for="address" class="col-3 col-lg-1 mb-3 col-form-label">Address:</label>
                <div class="col-9 col-lg-6 mb-3">
                    <input type="text" class="form-control" id="address" placeholder="Address" />
                </div>

                <label for="city" class="col-3 col-lg-1 mb-3 col-form-label">City:</label>
                <div class="col-9 col-lg-4 mb-3">
                    <input type="text" class="form-control" id="city" placeholder="City" />
                </div>

                <label for="image" class="col-3 col-lg-1 mb-3 col-form-label">Img:</label>
                <div class="col-9 col-lg-11 mb-3">
                    <input type="text" class="form-control" id="image" placeholder="http://adsfajsdf.com" />
                </div>

                <label for="desc" class="col col-form-label">Description:</label>
                <div class="w-100"></div>
                <div class="col mb-3">
                    <textarea style="margin:0;" class="form-control" id="desc" rows="3" placeholder="Short Description"></textarea>
                </div>
            </div>

            <div class="form-row row justify-content-end mb-3">
                <div class="col-auto">
                    <button id="add-btn" type="submit" class="btn btn-outline-primary">Add</button>
                </div>
            </div>

            <input type="hidden" id="lat" name="lat">
            <input type="hidden" id="lng" name="lng">

        </form>
    </div>

    <script>
        var map;
        var service;
        var infowindow;
        var customMarker;
        var usc;
        var geocoder;

        function initialize() {
            usc = new google.maps.LatLng(34.0220613,-118.2858344);

            infowindow = new google.maps.InfoWindow();
            map = new google.maps.Map(document.getElementById('location-map'), {
                center: usc,
                zoom: 15
            });

            // Link Search box
            var input = document.getElementById('pac-input');
            var searchBox = new google.maps.places.SearchBox(input);

            // Bias the SearchBox results based on map's viewport
            map.addListener('bounds_changed', function() {
                searchBox.setBounds(map.getBounds());
            });

            var markers = [];
            // Listen for the event fired when the user selects a prediction and retrieve
            // more details for that place.
            searchBox.addListener('places_changed', function() {
                var places = searchBox.getPlaces();

                if (places.length == 0) {
                    return;
                }

                // Clear out the old markers.
                markers.forEach(function(marker) {
                    marker.setMap(null);
                });
                markers = [];

                // For each place, get the icon, name and location.
                var bounds = new google.maps.LatLngBounds();
                places.forEach(function(place) {
                    if (!place.geometry) {
                        console.log("Returned place contains no geometry");
                        return;
                    }

                    // Create a marker for each place.
                    markers.push(createMarker(place));

                    if (place.geometry.viewport) {
                        // Only geocodes have viewport.
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
                map.fitBounds(bounds);
            });


            customMarker = new google.maps.Marker({
                map: map,
                visible: false
            });

            service = new google.maps.places.PlacesService(map);
            geocoder = new google.maps.Geocoder;

            // Map Listener
            google.maps.event.addListener(map, 'click', function(event) {
                console.log(event);

                customMarker.setVisible(false);

                // if clicked place on the map has a place id
                if (event.hasOwnProperty('placeId')) {
                    console.log('it has a place id!');
                    fetchPlaceDetail(event['placeId']);
                }
                else {
                    customMarker.setPosition(event['latLng']);
                    customMarker.setAnimation(google.maps.Animation.BOUNCE);
                    customMarker.setVisible(true);

                    reverseGeocode(event['latLng']);
                }
            });

        }

        function searchCallback(results, status) {
            if (status == google.maps.places.PlacesServiceStatus.OK) {
                for (var i = 0; i < results.length; i++) {
                    var place = results[i];
                    createMarker(results[i]);
                }
            }
        }

        function detailCallback(place, status) {
            if (status == google.maps.places.PlacesServiceStatus.OK) {
                var title = place.name;
                var addr = place.formatted_address;
                var type = place.types[0];
                var city = extractCityInfo(place);
                var lat = place.geometry.location.lat();
                var lng = place.geometry.location.lng();
                var img;
                if (place.photos != null && place.photos.length > 0) {
                    img = place.photos[0].getUrl({maxWidth: 900});
                }

                populateFields(title, type, addr, city, lat, lng, img);
            }
        }

        function createMarker(place) {
            var placeLoc = place.geometry.location;
            var marker = new google.maps.Marker({
                map: map,
                position: placeLoc
            });

            google.maps.event.addListener(marker, 'click', function() {
                customMarker.setVisible(false);

                infowindow.setContent('<strong>'+place.name+'</strong>' + '<br />' + place.types[0] + '<br />' + place.formatted_address);
                infowindow.open(map, this);

                fetchPlaceDetail(place.place_id);
            });

            return marker;
        }

        function populateFields(name, type, address, city, lat, lng, img) {
            $('#title').val(name);
            $('#address').val(address);
            $('#lat').val(lat);
            $('#lng').val(lng);
            $('#image').val(img);
            $('#city').val(city);

            console.log(type);
            $('#type option').each(function() {
                if ($.trim($(this).text()) == $.trim(type)) {
                    $('#type').val($(this).val());
                }
            });
        }

        function fetchPlaceDetail(placeID) {
            var request = {
                placeId: placeID
            };
            service.getDetails(request, detailCallback);
        }

        function fetchPlaceSearch(query) {

            var request = {
                location: usc,
                radius: '500',
                query: 'restaurant'
            };
            service.textSearch(request, searchCallback);
        }

        function extractCityInfo(place) {
            var addrComp = place.address_components;
            for(var i = 0; i<addrComp.length; i++) {
                var curr = addrComp[i];
                var currTypes = curr.types;
                if (currTypes.includes('locality')) {
                    return curr.long_name;
                }
            }
        }

        function reverseGeocode(latLng) {
            var lat = latLng.lat();
            var lng = latLng.lng();
            var city = "";
            var addr = "";
            var type = "";

            geocoder.geocode({location: latLng}, function(results, status) {
                if (status === 'OK') {
                    if (results[0]) {
                        console.log(results[0]);
                        city = extractCityInfo(results[0]);
                        addr = results[0].formatted_address;
                        type = results[0].types[0];
                        populateFields("", type, addr, city, lat, lng, "");
                    }
                }
            });
        }

        $('document').ready(function() {
            $('#add-btn').click(function() {
                //console.log('here');
                var valid = true;
                if ($.trim($('#title').val()).length == 0) {
                    $('#title').addClass('errormsg');
                    $('#error').html("Please specify the title");
                    valid = false;
                }
                else {
                    $('#title').removeClass('errormsg');
                }
                if (!$('#lat').val() || !$('#lng').val()) {
                    $('#error').html("Please specify the location on the map");
                    valid = false;
                }
                if (valid) {
                    $(this).prop('disabled', true);
                    $(this).text("Adding...");

                    $.post('backend/add_back.php',
                    {
                        username: '<?php echo $_SESSION['username']; ?>',
                        title: $('#title').val(),
                        type: $('#type').val(),
                        addr: $('#address').val(),
                        city: $('#city').val(),
                        img: $('#image').val(),
                        desc: $('#desc').val(),
                        lat: $('#lat').val(),
                        lng: $('#lng').val()
                    },
                    function(data, status) {
                        if (data === "success")
                            window.location.replace("mypoi.php");
                        else
                            alert(data);
                        $('#add-btn').prop('disabled', false);
                        $('#add-btn').text("Add");
                    });
                }
            });
        });

    </script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB4Jkq6sZlGQdK6_9qqW0LJ5aFIElBImjg&libraries=places&callback=initialize" async defer></script>
</body>
</html>
