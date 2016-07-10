<?php
session_start();?>
// Check if a user is not logged in
// If so, redirect them to the permissions page
<?php 
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
if(count($_POST) > 0) {
	// If the form was submitted (posted) and the connection was successful
	// and if the form was submitted
	// attempt to create a new university
	if (!isset($error_type)) {
		try {
			// Create a new univerity
			$sql = "INSERT INTO university(university_name, address, description, picture_one, picture_two)
			VALUES(:name, :address, :description, :picture_one, :picture_two)";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':name', $_POST["university_name"], PDO::PARAM_STR);
			$stmt->bindParam(':address', $_POST["university_address"], PDO::PARAM_STR);
			$stmt->bindParam(':description', $_POST["university_desc"], PDO::PARAM_STR);
			$stmt->bindParam(':picture_one', $_POST["image1"], PDO::PARAM_STR);
			$stmt->bindParam(':picture_two', $_POST["image2"], PDO::PARAM_STR);
			$stmt->execute();
		} catch (PDOException $e) {
			// Get the error code
			$error_type = $e->errorInfo[1];
		}
	}
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
								<p>Aliquam erat volutpat. Pellentesque tristique ante ut risus. Quisque dictum. Integer nisl risus, sagittis convallis, rutrum id, elementum congue, nibh. Suspendisse dictum porta lectus. Donec placerat odio vel elit. Nullam ante orci, pellentesque eget, tempus quis, ultrices in, est. Curabitur sit amet nulla. Nam in massa. Sed vel tellus. Curabitur sem urna, consequat vel, suscipit in, mattis placerat, nulla. Sed ac leo. Donec leo. Vivamus fermentum nibh in augue. Nulla enim eros, porttitor eu, tempus id, varius non, nibh. Duis enim nulla, luctus eu, dapibus lacinia, venenatis id, quam. Vestibulum imperdiet, magna nec eleifend rutrum, nunc lectus vestibulum velit, euismod lacinia quam nisl id lorem. Quisque erat. Vestibulum pellentesque, justo mollis pretium suscipit, justo nulla blandit libero, in blandit augue justo quis nisl. Fusce mattis viverra elit. Fusce quis tortor.</p>
								<p>Aliquam erat volutpat. Pellentesque tristique ante ut risus. Quisque dictum. Integer nisl risus, sagittis convallis, rutrum id, elementum congue, nibh. Suspendisse dictum porta lectus. Donec placerat odio vel elit. Nullam ante orci, pellentesque eget, tempus quis, ultrices in, est. Curabitur sit amet nulla. Nam in massa. Sed vel tellus. Curabitur sem urna, consequat vel, suscipit in, mattis placerat, nulla. Sed ac leo. Donec leo. Vivamus fermentum nibh in augue. Nulla enim eros, porttitor eu, tempus id, varius non, nibh. Duis enim nulla, luctus eu, dapibus lacinia, venenatis id, quam. Vestibulum imperdiet, magna nec eleifend rutrum, nunc lectus vestibulum velit, euismod lacinia quam nisl id lorem. Quisque erat. Vestibulum pellentesque, justo mollis pretium suscipit, justo nulla blandit libero, in blandit augue justo quis nisl. Fusce mattis viverra elit. Fusce quis tortor.<br>
								</p>
								<p>Aliquam erat volutpat. Pellentesque tristique ante ut risus. Quisque dictum. Integer nisl risus, sagittis convallis, rutrum id, elementum congue, nibh. Suspendisse dictum porta lectus. Donec placerat odio vel elit. Nullam ante orci, pellentesque eget, tempus quis, ultrices in, est. Curabitur sit amet nulla. Nam in massa. Sed vel tellus. Curabitur sem urna, consequat vel, suscipit in, mattis placerat, nulla. Sed ac leo. Donec leo. Vivamus fermentum nibh in augue. Nulla enim eros, porttitor eu, tempus id, varius non, nibh. Duis enim nulla, luctus eu, dapibus lacinia, venenatis id, quam. Vestibulum imperdiet, magna nec eleifend rutrum, nunc lectus vestibulum velit, euismod lacinia quam nisl id lorem. Quisque erat. Vestibulum pellentesque, justo mollis pretium suscipit, justo nulla blandit libero, in blandit augue justo quis nisl. Fusce mattis viverra elit. Fusce quis tortor.<br>
								</p>
EOD;

								// Start the checks for content section
								if (count($_POST)) {
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
											echo 'Univerity already exists. TODO(timp): INSERT LINK TO PROFILE OR ask if they would like to join';
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
									<?php
									$default_content_sidebar = <<<EOD
									<header style="text-align:center;">
										<h2 class="centered">Create University Profile</h2>
									</header>

								<form action="" method="POST" class="pure-form centered">
									<fieldset>
										<legend>Request to create a profile for your university</legend>
										<input type="text" name="university_name" placeholder="University Name" required>
										<br><br>
										<input type="text" name="university_address" placeholder="Address" required><br><br>
										<input type="text" name="image1" placeholder="Image URL"><br><br>
										<input type="text" name="image2" placeholder="Image URL"><br><br>
										<textarea rows="8" required placeholder="Enter your university description here." name="university_desc"></textarea><br><br>
										<button type="submit" class="small-button">Submit</button>
									</fieldset>
								</form>
EOD;
								// If nothing has been posted, display the form
								if (count($_POST) == 0) {
									echo $default_content_sidebar;
								}
								?>
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
