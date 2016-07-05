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
									$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=test', 'root', 'S#8roN*PJTMQWJ4m');
									$sql="SELECT * FROM e WHERE event_date >= DATE(NOW()) LIMIT 3";
									$sth=$dbh->prepare($sql);
									$sth->execute();
									while($row = $sth->fetch(PDO::FETCH_ASSOC))
									{
										echo "<li>";
										echo "<p class=\"date\"><a href=\"#\"><b>";
										print $row['event_date'] . "\t";
										echo "</b></a></p>";
										echo '<p><a href="event_profile.php?eid='.$row['eid'].'">';
										print $row['event_name'] . "\t";
										echo "</a>";
										echo "<p>";
										print "location will go here";
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
						<p>Aliquam erat volutpat. Pellentesque tristique ante ut risus. Quisque dictum. Integer nisl risus, sagittis convallis, rutrum id, elementum congue, nibh. Suspendisse dictum porta lectus. Donec placerat odio vel elit. Nullam ante orci, pellentesque eget, tempus quis, ultrices in, est. Curabitur sit amet nulla. Nam in massa. Sed vel tellus. Curabitur sem urna, consequat vel, suscipit in, mattis placerat, nulla. Sed ac leo. Donec leo. Vivamus fermentum nibh in augue. Nulla enim eros, porttitor eu, tempus id, varius non, nibh. Duis enim nulla, luctus eu, dapibus lacinia, venenatis id, quam. Vestibulum imperdiet, magna nec eleifend rutrum, nunc lectus vestibulum velit.</p>
						<p>Aliquam erat volutpat. Pellentesque tristique ante ut risus. Quisque dictum. Integer nisl risus, sagittis convallis, rutrum id, elementum congue, nibh. Suspendisse dictum porta lectus. Donec placerat odio vel elit. Nullam ante orci, pellentesque eget, tempus quis, ultrices in, est. Curabitur sit amet nulla. Nam in massa. Sed vel tellus. Curabitur sem urna, consequat vel, suscipit in, mattis placerat, nulla. Sed ac leo. Donec leo. Vivamus fermentum nibh in augue. Nulla enim eros, porttitor eu, tempus id, varius non, nibh. Duis enim nulla, luctus eu, dapibus lacinia, venenatis id, quam. Vestibulum imperdiet, magna nec eleifend rutrum, nunc lectus vestibulum velit, euismod lacinia.<br>
						</p>
					</section>
				</div>
				<div class="3u">
					<section id="sidebar2">
						<header>
							<h2>Log In</h2>
						</header>
						<form class="pure-form" method="post">
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