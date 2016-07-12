<?php session_start();?>
<?php
if(isset($_POST['search']))
	$keywords = $_POST['rso_name'];

if(isset($_POST['join_rso'])){
	print "requested to join ".$_POST['join_rso_name'];
	$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
	$sql = "INSERT INTO joins_rso(sid, rso_name, approved, since) VALUES (:sid, :rso_name, :approved, :since)";
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':sid', $_SESSION['id']);
	$sth->bindParam(':rso_name', $_POST['join_rso_name']);
	$sth->bindValue(':approved', 0);
	$sth->bindValue(':since', null);
	$sth->execute();
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
						<header>
							<h2>Registered Student Organizations</h2>
						</header>
						<ul class="style3">
							<?php $dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
							if(isset($_POST['search'])) {
								$sql = "SELECT * FROM belongs_to_university b WHERE rso_name LIKE '%".$keywords."%' AND university_name=(SELECT university FROM student WHERE sid='".$_SESSION['id']."') AND rso_name=(SELECT rso_name FROM rso_approved_by r WHERE b.rso_name=r.rso_name)";
								$sth = $dbh->prepare($sql);
								$sth->execute();
								while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
									$rso_name = $row['rso_name'];
									$sql = "SELECT description FROM rso WHERE rso_name='".$rso_name."'";
									$sth2 = $dbh->prepare($sql);
									$sth2->execute();
									$description = $sth2->fetchColumn();
									echo "<li>";
									echo '<form action="" method="POST"><input type="hidden" name="join_rso_name" value="'.$rso_name.'"><button style="height:50px; width:100px; float:right;font-size:smaller;display:inline-block;" class="small-button" type="submit" id="join_rso" name="join_rso">Join</button></form>';
									echo '<p style="font-size:24pt;"><u><a href="#">';
									print $rso_name;
									echo "</a></u>";
									echo "</p>";
									print $description . "\t";
									echo "</b></p><br><br></li>";
								}
							}
							else if(isset($_POST['all']) || !isset($_POST['search'])) {
 								$sql = "SELECT * FROM belongs_to_university b WHERE university_name=(SELECT university FROM student WHERE sid='".$_SESSION['id']."') AND rso_name=(SELECT rso_name FROM rso_approved_by r WHERE b.rso_name=r.rso_name)";
								$sth = $dbh->prepare($sql);
								$sth->execute();
								while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
									$rso_name = $row['rso_name'];
									$sql = "SELECT description FROM rso WHERE rso_name='".$rso_name."'";
									$sth2 = $dbh->prepare($sql);
									$sth2->execute();
									$description = $sth2->fetchColumn();
									echo "<li>";
									echo '<form action="" method="POST"><input type="hidden" name="join_rso_name" value="'.$rso_name.'"><button style="height:50px; width:100px; float:right;font-size:smaller;display:inline-block;" class="small-button" type="submit" id="join_rso" name="join_rso">Join</button></form>';
									echo '<p style="font-size:24pt;"><u><a href="#">';
									print $rso_name;
									echo "</a></u>";
									echo "</p>";
									echo "<p><b>";
									print $description . "\t";
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
								<button type="submit" name="search" style="width:50%;" class="small-button">Search</button><br><br>
								<button type="submit" name="all" value="all" style="width:50%;" class="small-button">See All RSO's</button>
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