<?php session_start();?>
<!DOCTYPE HTML>
<html>
<head>
	<title>College Events</title>
	<link rel="stylesheet" href="css/style.css" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,900' rel='stylesheet' type='text/css'>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
	<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
	<script src="js/skel.min.js"></script>
	<script src="js/skel-panels.min.js"></script>
	<script src="js/init.js"></script>
	<!--[if lte IE 8]><script src="js/html5shiv.js"></script><![endif]-->
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
					<li class="active"><a href="index.php">Homepage</a></li>
					<li><a href="create.php">Create</a></li>
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
				<div class="3u">
					<section id="sidebar1">
						<header>
							<h2>Upcoming Events</h2>
						</header>

						<ul class="style3">
							<?php
							$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
							$sql="SELECT * FROM public_event WHERE event_date >= DATE(NOW()) AND eid IN (SELECT eid FROM public_approved_by) ORDER BY event_date LIMIT 3";
							$sth=$dbh->prepare($sql);
							$sth->execute();
							while($row = $sth->fetch(PDO::FETCH_ASSOC))
							{
								echo "<li>";
								echo "<p class=\"date\"><a href=\"#\"><b>";
								print $row['event_date']. "\t";
								echo "</b></a></p>";
								echo '<p><a href="event_profile.php?eid='.$row['eid'].'">';
								print $row['event_name'] . "\t";
								echo "</a>";
								echo "</p></li>";
							}
							?>
						</ul>
					</section>
				</div>
				<div class="6u skel-cell-important">
					<section id="content" >
						<header>
							<h2>Find what excites you</h2>
						</header>
						<p> Make the most of your college experience by never missing an event! Our website allows you to create events and student organizations,
							as well as search through events and organizations to find things that match your interests. If you do not register, you will only be able to
							see public university events, but fear not -- as soon as you register with a university, you gain access to all of that university's events.
						</p>
						<p>
							Join a Registered Student Organization to see even more events, or request to create your own to gain access to creating events.
						</p>
					</section>
				</div>
				<div class="3u">
					<section id="sidebar2">
						<?php
							if(isset($_SESSION['id']) && $_SESSION['id'] != '') {
								$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
								$sql="SELECT given_name FROM student WHERE sid='".$_SESSION['id']."'";
								$sth=$dbh->prepare($sql);
								$sth->execute();
								$name = $sth->fetchColumn(0);
								echo '<header><h2> Welcome, '.$name.'!</h2></header>';
								echo '<form action="session.php" class="pure-form" method="post"> <button type="submit" value="Log out" class="small-button">Log out </button>';}
								else {

							echo <<<EOD
						<header>
							<h2>Log In</h2>
						</header>
						<form action="session.php" method="post" class="pure-form" method="post">
							<fieldset>
								<legend>See more events</legend>
								<input name="username" type="email" placeholder="Email">
								<br><br>
								<input name="password" type="password" placeholder="Password">
								<br><br>
								<a style="color:black;" href="register.php">Need to register?</a>
								<br>
								<button type="submit" name="submit" class="small-button">Submit</button>
							</fieldset>
						</form>
EOD;
						}



						if (isset($_SESSION['message']) &&  $_SESSION['message'] != '') {
							echo '<br><p>' . $_SESSION['message'] . '</p>';
							$_SESSION['message'] = '';
						}

						?>

					</section>

				</div>

				<div>

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
