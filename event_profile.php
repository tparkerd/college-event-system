<?php session_start();?>
<?php $event_id=intval($_GET['eid']);
$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
$sql="SELECT * FROM e WHERE eid='".$event_id."'";
$sth=$dbh->prepare($sql);
$sth->execute();
$row = $sth->fetch();

$event_name = $row['event_name'];
$event_start_time = $row['event_start_time'];
$event_date = $row['event_date'];
$event_desc = $row['description'];
$event_category = $row['event_category'];
$contact_email = $row['contact_email'];
$contact_phone = $row['contact_phone'];
$event_rating = $row['rating'];

$location_sql = "SELECT * FROM at WHERE eid='".$event_id."'";
$location_stmt = $dbh->prepare($location_sql);
$location_stmt->execute();
$loc_result = $location_stmt->fetch();
$event_location = $loc_result['location_name'];

$latlong_sql = "SELECT * FROM location WHERE location_name='".$event_location."'";
$latlong_stmt = $dbh->prepare($latlong_sql);
$latlong_stmt->execute();
$latlong_result = $latlong_stmt->fetch();
$lat = $latlong_result['latitude'];
$long = $latlong_result['longitude'];


$dbh=null;
?>

<?php
$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
$text = $rating = "";

if (isset($_POST['submit_comment'])) {
	if (!empty($_POST['comment_text']))
		$text = strval($_POST['comment_text']);
	if (!empty($_POST['rating']))
		$rating = intval($_POST['rating']);
	$student_id = $_SESSION['id'];
	$insert_comment_sql = "INSERT INTO comments(sid, eid, rating, ctimestamp, text) VALUES ('".$student_id."','".$event_id."','".$rating."', default,'".$text."')";
	$comment_stmt = $dbh->prepare($insert_comment_sql);
	$comment_stmt->execute();
}
?>

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
		<script>
				function editComment() {
					var xhttp = new XMLHttpRequest();
					xhttp.onreadystatechange = function() {
						if (xhttp.readyState == 4 && xhttp.status == 200) {
							document.getElementById("single_comment_<?php print $_SESSION['id']?>").innerHTML = xhttp.responseText;
						}
					};
					xhttp.open("GET", "edit_comment.php?eid=<?php print $event_id?>", true);
					xhttp.send();
				}

				function deleteComment() {
					$.ajax({
						url: 'delete_comment.php',
						type: 'post', // performing a POST request
						data : {
							eid : '<?php print $event_id?>', // will be accessible in $_POST['data1']
						},
						dataType: 'json',
						success: function(data)
						{
							// etc...
						}
					});
				}
		</script>
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
				<div style="max-width=500px" class="container">
					<div style="max-width:80%;" class="row" >
						<div class="9u skel-cell-important">
							<section id="content">
								<header>
									<h2><?php print $event_name;?></h2>
								</header>
								<p><b>Date: </b><?php print $event_date;?></p>
								<p>Time: <?php print $event_start_time;?></p>
								<p>Location: <?php print $event_location ?></p>
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
								<form class="pure-form" method="POST">
									<fieldset>
										<legend>Add a comment</legend>
										<br>
										<div id="rating" name="rating">
											Rating:
											<label style="display:inline;margin-right:10px;margin-left:10px;" for="one_star" class="pure-radio">
												<input id="one_star" type="radio" name="rating" value="1" checked>
													1
												</label>
											<label style="display:inline;margin-right:10px;" for="two_star" class="pure-radio">
												<input id="two_star" type="radio" name="rating" value="2" checked>
												2
											</label>
											<label style="display:inline;margin-right:10px;" for="three_star" class="pure-radio label-inline">
												<input id="three_star" type="radio" name="rating" value="3" checked>
												3
											</label>
											<label style="display:inline;margin-right:10px;" for="four_star" class="pure-radio">
												<input id="four_star" type="radio" name="rating" value="4" checked>
												4
											</label>
											<label style="display:inline;margin-right:10px;" for="five_star" class="pure-radio">
												<input id="five_star" type="radio" name="rating" value="5" checked>
												5
											</label>
										</div>
										<br><br>
										<textarea rows="8" cols="50" placeholder="Enter your thoughts and suggestions here" name="comment_text" id="comment_text"></textarea><br>
										<button type="submit" id="submit_comment" name="submit_comment" class="small-button">Submit</button>
									</fieldset>
								</form>
							</section>
							<section id="list_comments">
								<p>
								<ul class="style3">
									<?php
										date_default_timezone_set('America/New_York');
										$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
										$sql="SELECT * FROM comments WHERE eid='".$event_id."'";
										$sth=$dbh->prepare($sql);
										foreach ($dbh->query($sql) as $row) {
											$c_sid = $row['sid'];
											$sql_commenter_name = "SELECT * FROM student WHERE sid='".$c_sid."'";
											$prep_commenter_name = $dbh->prepare($sql_commenter_name);
											$prep_commenter_name->execute();
											$result = $prep_commenter_name->fetch(PDO::FETCH_ASSOC);
											echo '<div id="single_comment_';
											print $_SESSION['id'];
											echo '"><li>';
											echo '<div><p style="font-size:large;"><b>';
											print "Posted on ".date('F j, Y g:i A', strtotime($row['ctimestamp']));
											print " by ".$result['given_name'];
											echo '</b><br>';
											print "Rating: ".$row['rating'];
											echo '<br>';
											print $row['text'];
											echo '</div><br>';
											echo '<div style="float:right">';
											if($row['sid'] == $_SESSION['id']){
												echo '<button style="font-size:smaller;display:inline-block;" class="smallest-button" onclick="editComment()" type="button" id="edit_comment" name="edit_comment">Edit</button>';
												echo '<button style="margin-left:20px;font-size:smaller;display:inline-block;" class="smallest-button" onclick="deleteComment()" type="button" id="delete_comment" name="delete_comment">Delete</button>';
											}
											echo '</div></li></p>';
										}
										?>
									</ul>
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
											var myLatLng = {lat: <?php echo $lat?>, lng: <?php echo $long?>};
											var mapProp = {
												center:new google.maps.LatLng(<?php echo $lat?>,<?php echo $long?>),
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