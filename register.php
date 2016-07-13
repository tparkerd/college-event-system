<?php session_start();
// TODO(timp): Implement check if student id was already used
	$error = array();
  if (!empty($_POST)) {
    // Connect to database
    $dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Collect Input Values
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $sid = $_POST['sid'];

    // Try to create user
    try {
      $sql="INSERT INTO student(sid, given_name, family_name, email, pword, university) VALUES (:sid, :fname, :lname, :email, :pword, :university)";
      $sth=$dbh->prepare($sql);
      $sth->bindParam(':sid',   $sid,      PDO::PARAM_STR, 8);
      $sth->bindParam(':fname', $fname,    PDO::PARAM_STR, 35);
      $sth->bindParam(':lname', $lname,    PDO::PARAM_STR, 35);
      $sth->bindParam(':email', $email,    PDO::PARAM_STR, 90);
      $sth->bindParam(':pword', $password, PDO::PARAM_STR, 100);

      // If an initial university was chosen, include it at creation
      if(!empty($_POST['university']))
        $sth->bindParam(':university', $_POST['university'], PDO::PARAM_STR, 100);
      else
        $sth->bindValue(':university', null, PDO::PARAM_INT);

      $sth->execute();
    } catch (PDOException $e) {
      $error = $e;
    }


    // If a university was chosen, make sure to affiliate it with the student
    if(!empty($_POST['university'])) {
      try {
        // Affiliate the user with the university to increase student count
        $sql = "INSERT INTO affiliates_university VALUES(:id, :university_name)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $sid, PDO::PARAM_STR);
        $stmt->bindParam(':university_name', $_POST['university'], PDO::PARAM_STR);
        $stmt->execute();
      } catch (PDOException $e) {
        $error = $e;
      }
    }
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
						<div class="12u">
							<section id="content">
								<header>
									<h2 class="centered">Register Today</h2>
								</header>

								<form class="pure-form centered" action="" method="POST">
									<fieldset>
										<legend>Register with us today to gain access to more events!</legend>
										<br><input type="text" name="fname" style="width:25%;" placeholder="First Name" required>
										<br><br>
										<input type="text" name="lname" style="width:25%;" placeholder="Last Name" required>
										<br><br>
										<input type="text"  name="sid" style="width:25%;" placeholder="Student ID" required>
										<br><br>
										<input type="email" name="email" style="width:25%;" placeholder="Email" required>
										<br><br>
										<input type="password" name="password" style="width:25%;" placeholder="Password" required>
										<br><br>
										<?php
										$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
										$sql ='SELECT university_name from university WHERE university_name IN (SELECT university_name FROM university_approved_by)';
										$stmt = $dbh->prepare($sql);
										$stmt->execute();
										$unames=$stmt->fetchAll();
										?>
										<select name="university" style="width:25%;padding-bottom:5px" id="university" placeholder="University">
											<option>Select a University</option>
											<?php foreach($unames as $uname):?>
												<option value="<?php print $uname['university_name']?>"><?php print $uname['university_name']; ?></option>
											<?php endforeach; ?>
										</select>
										<br>
										<button name="btn-signup" type="submit" class="small-button">Submit</button>
										<?php
											if (!empty($_POST)) {
												if(empty($error))
												print "<br><br>Your registration was successful!";
												else {
													print "<br><br>Sorry, there was an error with your registration.";
													echo "<br>";
													print "Make sure you are using the proper Student ID and try again.";
													echo "<br>";
												}
											}
										?>
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
