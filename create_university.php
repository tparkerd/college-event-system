<?php
session_start();?>
<?php
// Check if a user is not logged in
// If so, redirect them to the permissions page
if(!isset($_SESSION['id']))
{
	$url='permissions.php';
	echo '<META HTTP-EQUIV=REFRESH CONTENT="0; '.$url.'">';
}

// Connect to database
$host = 'sdickerson.ddns.net';
$port = '3306';
$db   = 'ces';
$user = 'root';
$pass = 'S#8roN*PJTMQWJ4m';
$charset = 'utf8';

try {
	$pdo = new PDO('mysql:host='.$host.';dbname='.$db.';port=3306', $user, $pass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
	$error_type = $e->errorInfo[0];
}
if(!empty($_POST)) {
	$response = array();
	$response['Form data'] = $_POST;

	// If the form was submitted (posted) and the connection was successful
	// and if the form was submitted
	// attempt to create a new university
	if (!isset($error_type)) {
		try {
			// Create a new univerity
			$sql = "INSERT INTO university(university_name, address, description, picture_one, picture_two)
			VALUES(:name, :address, :description, :picture_one, :picture_two)";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':name', $_POST["name"], PDO::PARAM_STR);
			$stmt->bindParam(':address', $_POST["address"], PDO::PARAM_STR);
			$stmt->bindParam(':description', $_POST["description"], PDO::PARAM_STR);
			$stmt->bindParam(':picture_one', $_POST["picture_one"], PDO::PARAM_STR);
			$stmt->bindParam(':picture_two', $_POST["picture_two"], PDO::PARAM_STR);
			$stmt->execute();

			// Establish the user as the prospective superadmin
			$sql = "INSERT INTO creates_university(university_name, superadmin_id) VALUES(:name, :superadmin_id)";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
			$stmt->bindParam(':superadmin_id', $_SESSION['id'], PDO::PARAM_STR);
			$stmt->execute();
		} catch (PDOException $e) {
			// Get the error code
			$response['error'] = json_encode($e);
		}
	}
	echo json_encode($response);
	exit;
}
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
	<script src="js/skel-panels.min.js"></script>
	<script src="js/init.js"></script>
	<noscript>
		<link rel="stylesheet" href="css/skel-noscript.css" />
		<link rel="stylesheet" href="css/style.css" />
	</noscript>
	<script type="text/javascript">
	$(function() {
		// Whenever a univerity is created...
		$('#creates_university').submit(function(e) {
			e.preventDefault();

			formData = {
				name 					: $('input[name=university_name]').val(),
				address 			: $('input[name=university_address]').val(),
				description 	: $('textarea[name=university_desc]').val(),
				picture_one		: $('input[name=image1]').val(),
				picture_two		: $('input[name=image2]').val()
			}

			$.ajax({
				type			: 'POST',
				url				: '',
				data      : formData,
				dataType	: 'json',
				encode		: true
			})
			.done(function(data) {
				// TODO(timp): Replace the sidebar with a response if the request was successfully submitted
				$(e.target).parent().parent().remove();
				console.log(data)
			})
			.fail(function(data) {
				// alert the user that it failed
				console.log('Failed ajax')
				console.log(data)
			})
		})
	})
	</script>
	<!--[if lte IE 8]><link rel="stylesheet" href="css/ie/v8.css" /><![endif]-->
	<!--[if lte IE 9]><link rel="stylesheet" href="css/ie/v9.css" /><![endif]-->
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
					<?php if(isset($_SESSION['id'])){
						$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
						$superadmin_sql = "SELECT * FROM superadmin WHERE superadmin_id ='".$_SESSION['id']."'";
						$prep_sql = $dbh->prepare($superadmin_sql);
						$prep_sql->execute();
						$result = $prep_sql->fetch();
						if($result){
							echo '<li><a href="superadmin_dashboard.php">Dashboard</a></li>';
						}
						$webmaster_sql = "SELECT * FROM webmaster WHERE wid ='".$_SESSION['id']."'";
						$prep_webmaster_sql = $dbh->prepare($webmaster_sql);
						$prep_webmaster_sql->execute();
						$result2 = $prep_webmaster_sql->fetch();
						if($result2){
							echo '<li><a href="webmaster_dashboard.php">Webmaster Dashboard</a></li>';
						}}
					?>
				</ul>
			</nav>
		</div>
	</div>
	<!-- /Header -->

	<div id="page">
		<div class="container">
			<div class="row">
				<div class="9u skel-cell-important">
					<section id="content" >
						<?php

						// Define content
						$default_content = <<<EOD
								<header>
									<h2>University Profile Guidelines</h2>
								</header>
								<p>
									<ul style="list-style-type:circle" class="style3">
									<li class="first">All fields in this form except image URLs are required. However, we prefer at least one image URL to show off on your university's profile page!</li class="first">
									<li>Your university creation request must be approved by the webmaster before it will appear on the site at all. </li>
									<li>You will become the university's superadministrator once your creation request is approved. All registered student organization creation requests and event creation requests are your responsibility to approve and monitor.</li>
										<br><br>
									</ul>
								</p>
EOD;

						// Start the checks for content section
						if (!empty($_POST)) {
							if (!isset($error_type)) {
								echo 'Your request to create <em>' . $_POST['university_name'] . '</em> has been accepted. Please wait for approval.<br>';
							} else {
								echo 'Error code: ' . $error_type . '<br>';
								switch ($error_type) {
									// Column cannot be null
									case 1048:
										echo 'Forgot to enter value';
										break;
									// Duplicate key
									case 1062:
										echo 'University already exists. TODO(timp): INSERT LINK TO PROFILE OR ask if they would like to join';
										break;
									default:
										echo 'Unknown error code: ' . $error_type;
										break;
								}
							}
						} else {
							echo $default_content;
						}
						?>
					</section>
				</div>
				<div class="3u">
					<section id="sidebar2">
									<header style="text-align:center;">
										<h2 class="centered">Create University Profile</h2>
									</header>

								<form id="creates_university" class="pure-form centered">
									<fieldset>
										<legend>Request to create a profile for your university</legend>
										<input type="text" name="university_name" placeholder="University Name" required>
										<br><br>
										<input type="text" name="university_address" placeholder="Address" required><br><br>
										<input type="text" name="image1" placeholder="Image URL"><br><br>
										<input type="text" name="image2" placeholder="Image URL"><br><br>
										<textarea rows="8" required placeholder="Enter your university description here." name="university_desc"></textarea><br><br>
										<input type="submit" class="small-button" value="Submit" />
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
