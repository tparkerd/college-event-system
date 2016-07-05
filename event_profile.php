<?php $event_id=intval($_GET['eid']);
$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=test', 'root', 'S#8roN*PJTMQWJ4m');
$sql="SELECT * FROM e WHERE eid='".$event_id."'";
$sth=$dbh->prepare($sql);
$dbh->query($sql);
foreach ($dbh->query($sql) as $row) {
	$event_name = $row['event_name'];
	$event_time = $row['event_time'];
	$event_date = $row['event_date'];
	$event_desc = $row['description'];
	$event_category = $row['event_category'];
	$contact_email = $row['contact_email'];
	$contact_phone = $row['contact_phone'];
	$event_rating = $row['rating'];
}

$dbh=null;?>

<!DOCTYPE HTML>
<!--
	Synchronous by TEMPLATED
    templated.co @templatedco
    Released for free under the Creative Commons Attribution 3.0 license (templated.co/license)
-->
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
		<script src="js/skel-panels.min.js"></script>
		<script src="js/init.js"></script>
		<noscript>
			<link rel="stylesheet" href="css/skel-noscript.css" />
			<link rel="stylesheet" href="css/style.css" />
		</noscript>
		<!--[if lte IE 8]><link rel="stylesheet" href="css/ie/v8.css" /><![endif]-->
		<!--[if lte IE 9]><link rel="stylesheet" href="css/ie/v9.css" /><![endif]-->
	</head>
	<body>
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.6";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>

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
									<h2><?php print $event_name;?></h2>
								</header>
								<p><b>Date: </b><?php print $event_date;?></p>
								<p>Time: <?php print $event_time;?></p>
								<p>Location: (not in db yet, relationship) </p>
								<p>Contact Information: <br> E-mail: <?php print $contact_email;?>
								<br> Phone: <?php print $contact_phone;?></p>
								<p><?php print $event_desc;?></p>
								<br>
								<div class="fb-share-button" data-href="http://localhost:63342/college-event-website/event_profile.html?_ijt=uaupj23v5ohnmh2o24o8e9g2dr" data-layout="button" data-size="large" data-mobile-iframe="true"><a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Flocalhost%3A63342%2Fcollege-event-website%2Fevent_profile.html%3F_ijt%3Duaupj23v5ohnmh2o24o8e9g2dr&amp;src=sdkpreparse">Share</a></div>
								<br><br>
								<a href="https://twitter.com/share" class="twitter-share-button" data-text="Check out this event:" data-size="large">Tweet</a> <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
								</section>
							<br><br>
							<section id="add_comment">
								<header>
									<h2>Comments</h2>
								</header>
								<form class="pure-form">
									<fieldset>
										<legend>Add a comment</legend>
										<br>
										<div id="rating">
											Rating:
											<label style="display:inline;margin-right:10px;margin-left:10px;" for="one_star" class="pure-radio">
												<input id="one_star" type="radio" name="rating" value="one_star" checked>
													1
												</label>
											<label style="display:inline;margin-right:10px;" for="two_star" class="pure-radio">
												<input id="two_star" type="radio" name="rating" value="two_star" checked>
												2
											</label>
											<label style="display:inline;margin-right:10px;" for="three_star" class="pure-radio label-inline">
												<input id="three_star" type="radio" name="rating" value="three_star" checked>
												3
											</label>
											<label style="display:inline;margin-right:10px;" for="four_star" class="pure-radio">
												<input id="four_star" type="radio" name="rating" value="four_star" checked>
												4
											</label>
											<label style="display:inline;margin-right:10px;" for="five_star" class="pure-radio">
												<input id="five_star" type="radio" name="rating" value="five_star" checked>
												5
											</label>
										</div>
										<br><br>
										<textarea rows="8" cols="50" placeholder="Enter your thoughts and suggestions here" name="event_description"></textarea><br>
										<button type="submit" class="small-button">Submit</button>
									</fieldset>
								</form>
							</section>
							<section id="list_comments">
								<p>Aliquam erat volutpat. Pellentesque tristique ante ut risus. Quisque dictum. Integer nisl risus, sagittis convallis, rutrum id, elementum congue, nibh. Suspendisse dictum porta lectus. Donec placerat odio vel elit. Nullam ante orci, pellentesque eget, tempus quis, ultrices in, est. Curabitur sit amet nulla. Nam in massa. Sed vel tellus. Curabitur sem urna, consequat vel, suscipit in, mattis placerat, nulla. Sed ac leo. Donec leo. Vivamus fermentum nibh in augue. Nulla enim eros, porttitor eu, tempus id, varius non, nibh. Duis enim nulla, luctus eu, dapibus lacinia, venenatis id, quam. Vestibulum imperdiet, magna nec eleifend rutrum, nunc lectus vestibulum velit, euismod lacinia quam nisl id lorem. Quisque erat. Vestibulum pellentesque, justo mollis pretium suscipit, justo nulla blandit libero, in blandit augue justo quis nisl. Fusce mattis viverra elit. Fusce quis tortor.</p>
								<p>Aliquam erat volutpat. Pellentesque tristique ante ut risus. Quisque dictum. Integer nisl risus, sagittis convallis, rutrum id, elementum congue, nibh. Suspendisse dictum porta lectus. Donec placerat odio vel elit. Nullam ante orci, pellentesque eget, tempus quis, ultrices in, est. Curabitur sit amet nulla. Nam in massa. Sed vel tellus. Curabitur sem urna, consequat vel, suscipit in, mattis placerat, nulla. Sed ac leo. Donec leo. Vivamus fermentum nibh in augue. Nulla enim eros, porttitor eu, tempus id, varius non, nibh. Duis enim nulla, luctus eu, dapibus lacinia, venenatis id, quam. Vestibulum imperdiet, magna nec eleifend rutrum, nunc lectus vestibulum velit, euismod lacinia quam nisl id lorem. Quisque erat. Vestibulum pellentesque, justo mollis pretium suscipit, justo nulla blandit libero, in blandit augue justo quis nisl. Fusce mattis viverra elit. Fusce quis tortor.<br>
								</p>
							</section>
						</div>
						<div class="3u">
							<section id="sidebar2">

								<header style="text-align:left; margin-top:20px">
										<h2>Location Info</h2>
									</header>
								<head>
									<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBhju0H-YndJzexINhO5u4JU5o5G-0jtgg"></script>
									<script>
										function initialize() {
											var myLatLng = {lat: 28.6024, lng: -81.2001};
											var mapProp = {
												center:new google.maps.LatLng(28.6024,-81.2001),
												zoom:15,
												mapTypeId:google.maps.MapTypeId.ROADMAP
											};
											var map=new google.maps.Map(document.getElementById("googleMap"),mapProp);
											var marker = new google.maps.Marker({
												position: myLatLng,
												map: map,
												title: 'Event location'
											});
										}
										google.maps.event.addDomListener(window, 'load', initialize);
									</script>
								</head>

								<body>
								<div id="googleMap" style="width:450px;height:380px;"></div>
								</body>

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