<?php session_start();?>
<?php
if(isset($_POST['submit']))
	$keywords = $_POST['rso_name'];
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
					<li><a href="create.php">Create</a></li>
					<li  class="active"><a href="search.php">Search</a></li>
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
						<header>
							<h2>RSO Search Results</h2>
						</header>
						<ul class="style3">
							<?php $dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
							if(isset($_POST['submit'])) {
								$sth = "SELECT * FROM belongs_to_university WHERE rso_name LIKE '%".$keywords."' AND university_name=(SELECT university FROM student WHERE sid='".$_SESSION['id']."'))";
								$sth->execute();
								while ($row = $sth_public->fetch(PDO::FETCH_ASSOC)) {
									echo "<li>";
									echo '<p style="font-size:24pt;"><u><a href="#">';
									print $row['rso_name'];
									echo "</a></u></p>";
									echo "<p><b>";
									print $row['university_name'] . "\t";
									echo "</b></p><br><br></li>";
								}
							}
							?>
						</ul>
						</p>
					</section>
				</div>
				<div class="3u">
					<section id="sidebar2">
						<header style="text-align:center;">
							<h2 class="centered">Find Organizations to Join</h2>
						</header>

						<form class="pure-form centered" method="POST">
							<fieldset>
								<legend>Search for groups that you relate to</legend>
								<br><br>
								<input type="text" id="rso_name" name="rso_name" placeholder="Organization Name">
								<br><br>
								<button type="submit" class="small-button">Search</button>
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