<?php session_start()?>
<?php
$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
$sql ="SELECT * FROM admin WHERE admin_id='".$_SESSION['id']."'";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$row){
	$url='permissions.php';
	echo '<META HTTP-EQUIV=REFRESH CONTENT="0; '.$url.'">';
}
else
    echo '<title>College Events</title>';
?>

<!DOCTYPE HTML>
<html>
	<head>
        <title>College Events</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta name="description" content="" />
        <meta name="keywords" content="" />
        <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,900' rel='stylesheet' type='text/css'>
        <!--[if lte IE 8]><script src="js/html5shiv.js"></script><![endif]-->
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
        <script src="js/skel.min.js"></script>
        <script src="js/init.js"></script>
        <noscript>
            <link rel="stylesheet" href="css/skel-noscript.css" />
            <link rel="stylesheet" href="css/style.css" />
        </noscript>
        <!--[if lte IE 8]><link rel="stylesheet" href="css/ie/v8.css" /><![endif]-->
        <!--[if lte IE 9]><link rel="stylesheet" href="css/ie/v9.css" /><![endif]-->
        <style>
            html, body {
                height: 100%;
                margin: 0;
                padding: 0;
            }
            #map {
                height: 360px;
                width:700px;
            }
            .controls {
                margin-top: 10px;
                border: 1px solid transparent;
                border-radius: 2px 0 0 2px;
                box-sizing: border-box;
                -moz-box-sizing: border-box;
                height: 32px;
                outline: none;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
            }

            #pac-input {
                background-color: #fff;
                font-family: Roboto;
                font-size: 15px;
                font-weight: 300;
                margin-left: 12px;
                padding: 0 11px 0 13px;
                text-overflow: ellipsis;
                width: 300px;
            }

            #pac-input:focus {
                border-color: #4d90fe;
            }

            .pac-container {
                font-family: Roboto;
            }

            #type-selector {
                color: #fff;
                background-color: #4d90fe;
                padding: 5px 11px 0px 11px;
            }

            #type-selector label {
                font-family: Roboto;
                font-size: 13px;
                font-weight: 300;
            }
            #target {
                width: 345px;
            }
        </style>

    <script>
        // This example adds a search box to a map, using the Google Place Autocomplete
        // feature. People can enter geographical searches. The search box will return a
        // pick list containing a mix of places and predicted search terms.

        // This example requires the Places library. Include the libraries=places
        // parameter when you first load the API. For example:

        function initAutocomplete() {
            geocoder = new google.maps.Geocoder();
            var map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: 28.6024, lng: -81.2001},
                zoom: 13,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });


            // Create the search box and link it to the UI element.
            var input = document.getElementById('pac-input');
            var searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

            // Bias the SearchBox results towards current map's viewport.
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
                    var lat = place.geometry.location.lat();
                    var lng = place.geometry.location.lng();
                    document.getElementById("latitude").value = lat;
                    document.getElementById("longitude").value = lng;
                    document.getElementById("location").value = place.name;

                    console.log(place.name);
                    console.log(lat);
                    console.log(lng);
                    var icon = {
                        url: place.icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25)
                    };

                    // Create a marker for each place.
                    markers.push(new google.maps.Marker({
                        map: map,
                        icon: icon,
                        title: place.name,
                        position: place.geometry.location,
                }));

                    if (place.geometry.viewport) {
                        // Only geocodes have viewport
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
                map.fitBounds(bounds);

            });
        }

    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBhju0H-YndJzexINhO5u4JU5o5G-0jtgg&libraries=places&callback=initAutocomplete"
            async defer></script>


	</head>
	<body>
		<div id="wrapper">

			<!-- Header -->
			<div id="header">
				<div class="container">

					<!-- Logo -->
					<div id="logo">
						<h1><a href="#">College Events</a></h1>
					</div>

					<!-- Nav -->
					<nav id="nav">
						<ul>
							<li><a href="index.php">Homepage</a></li>
							<li class="active"><a href="create.php">Create</a></li>
							<li><a href="search.php">Search</a></li>
							<li><a href="universities.php">Universities</a></li>
						</ul>
					</nav>
				</div>
			</div>
			<!-- /Header -->

			<div id="page">
				<div class="container">
					<div class="row">
						<div class="9u skel-cell-important">
							<section id="content">
								<header>
									<h2>Event Guidelines</h2>
								</header>
                                <input id="pac-input" class="controls" type="text" placeholder="Search Box">
                                <div id="map"></div><br><br>
								</p>
							</section>
						</div>
						<div class="3u">
							<section id="sidebar2">
								<header style="text-align:center;">
									<h2>Create Event</h2>
								</header>
									<form class="pure-form centered" action="insert_event.php" method="POST">
										<fieldset>
											<legend>Request to create an event</legend>
											<input style="width:220px;" type="text" name="name" placeholder="Event Name" required>
											<br><br>
											<input style="width:220px;" type="date" name="date" placeholder="date" required>
											<br><br>
											<input style="width:220px;" type="time" name="start_time"  required>
											<br><br>
                                            <input style="width:220px;" type="time" name="end_time"  required>
                                            <br><br>
											<input type="text" style="width:220px;" id="location" name="location" placeholder="Location Name" required><br><br>
											<select style="width:220px;" name="university">
                                                <?php
                                                $dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
                                                $sql ="SELECT university from student WHERE sid='".$_SESSION['id']."'";
                                                $stmt = $dbh->prepare($sql);
                                                $stmt->execute();
                                                $unames=$stmt->fetchAll();
                                                ?>
                                                <option value="" disabled selected>University</option>
                                                <?php foreach($unames as $uname):?>
                                                    <option value="<?php print $uname['university']?>"><?php print $uname['university']; ?></option>
                                                <?php endforeach; ?>
											</select>
											<br><br>
											<select style="width:220px;" name="category" placeholder="Event Category" required>
												<option value="" disabled selected>Event Category</option>
												<option value="concert">Concert</option>
												<option value="tech-talk">Tech Talk</option>
												<option value="hackathon">Hack-A-Thon</option>
												<option value="sports">Sporting Event</option>
                                                <option value="homecoming">Homecoming</option>

                                            </select>
											<br><br>
											<select style="width:220px;" name="privacy" placeholder="Event Type" required>
												<option value="" disabled selected>Event Type</option>
												<option value="public">Public Event</option>
												<option value="private">Private Event</option>
												<option value="RSO">RSO Event</option>
											</select><br><br>
                                            <select style="width:220px;" name="rso_name" id="rso_name">
                                                <?php
                                                $dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
                                                $sql ="SELECT rso_name from creates_rso WHERE sid='".$_SESSION['id']."'";
                                                $stmt = $dbh->prepare($sql);
                                                $stmt->execute();
                                                $unames=$stmt->fetchAll();
                                                ?>
                                                    <option value="" disabled selected>RSO Name, if RSO Event</option>
                                                    <?php foreach($unames as $uname):?>
                                                        <option value="<?php print $uname['rso_name']?>"><?php print $uname['rso_name']; ?></option>
                                                    <?php endforeach; ?>
                                            </select>
											<br><br>
											<input type="hidden" id="latitude" name="latitude" value="">
											<input type="hidden" id="longitude" name="longitude" value="">
											<input style="width:220px;" type="tel" name="contact_phone" placeholder="Contact Phone" required><br><br>
											<input style="width:220px;" type="email" name="contact_email" placeholder="Contact E-mail" required><br><br>
											<textarea style="width:220px;" rows="8" placeholder="Enter your event description here." name="description" required></textarea><br><br>
											<button type="submit" class="small-button">Submit</button>
										</fieldset>
									</form>
							</section>
						</div>
					</div>

				</div>
			</div>



			<!-- Copyright -->
			<div id="copyright">
				<div class="container">
				</div>
			</div>

		</div>
	</body>

</html>
