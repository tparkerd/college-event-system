<?php session_start()?>
<?php
$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
echo '<title>College Events</title>';

$rso_error = $name_taken_error = $email_error = $email_error2 = $university_error = $description_error = "";

if (isset($_POST['submit'])) {
	$admin_id = $_SESSION['id'];

	if (!empty($_POST['rso_name'])){
		$rso_name = strval($_POST['rso_name']);
	}
	else{
		$rso_error = "Name field is required";
	}
	if (!empty($_POST['rso_university'])) {
		$rso_university = strval($_POST['rso_university']);
	}else{
		$university_error = "University field is required";
	}
	if (empty($_POST['mem1_email']) || empty($_POST['mem2_email']) || empty($_POST['mem3_email']) || empty($_POST['mem4_email']) || empty($_POST['mem5_email'])) {
		$email_error = "Five member emails are required";
	}
	else{
		$email1 = $_POST['mem1_email'];
		$email2 = $_POST['mem2_email'];
		$email3 = $_POST['mem3_email'];
		$email4 = $_POST['mem4_email'];
		$email5 = $_POST['mem5_email'];
		$domain1 = substr(strrchr($email1, "@"), 1);
		$domain2 = substr(strrchr($email2, "@"), 1);
		$domain3 = substr(strrchr($email3, "@"), 1);
		$domain4 = substr(strrchr($email4, "@"), 1);
		$domain5 = substr(strrchr($email5, "@"), 1);
		if(!(($domain1 == $domain2) && ($domain2 == $domain3) && ($domain3 == $domain4) && ($domain4 == $domain5))){
			$email_error2 = "All emails must have the same domain name";
		}
	}
	if (!empty($_POST['rso_university'])) {
		$description = strval($_POST['description']);
	}else{
		$description_error = "Description is required";
	}

	if($rso_error == "" && $university_error == "" && $email_error == "" && $email_error2 == "" && $description_error == ""){
		$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
		$rso_name_sql = "SELECT COUNT(*) FROM rso WHERE rso_name ='".$rso_name."'";
		$rso_name_stmt = $dbh->prepare($rso_name_sql);
		$rso_name_stmt->execute();
		$data=$rso_name_stmt->fetch(PDO::FETCH_NUM);
		$name_result=$data[0];

		if($name_result == 0){
			date_default_timezone_set('America/New_York');
			$insert_rso_sql = "INSERT INTO rso(rso_name, admin_id) VALUES (:rso_name, :admin_id)";
			$insert_rso_stmt = $dbh->prepare($insert_rso_sql);
			$insert_rso_stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_STR, 8);
			$insert_rso_stmt->bindParam(':rso_name', $rso_name, PDO::PARAM_STR, 80);
			$insert_rso_stmt->execute() or die(print_r($insert_rso_stmt->errorInfo(), true));

			$affiliates_rso_sql = "INSERT INTO affiliates_rso(sid, rso_name) VALUES (:sid, :rso_name)";
			$affiliates_rso_stmt = $dbh->prepare($affiliates_rso_sql);
			$affiliates_rso_stmt->bindParam(':sid', $admin_id, PDO::PARAM_STR, 80);
			$affiliates_rso_stmt->bindParam(':rso_name', $rso_name, PDO::PARAM_STR, 255);
			$affiliates_rso_stmt->execute() or die(print_r($affiliates_rso_stmt->errorInfo(), true));

			$belongs_to_university_sql = "INSERT INTO belongs_to_university(rso_name, university_name) VALUES (:rso_name, :university_name)";
			$belongs_to_university_stmt = $dbh->prepare($belongs_to_university_sql);
			$belongs_to_university_stmt->bindParam(':rso_name', $rso_name, PDO::PARAM_STR, 80);
			$belongs_to_university_stmt->bindParam(':university_name', $rso_university, PDO::PARAM_STR, 255);
			$belongs_to_university_stmt->execute() or die(print_r($belongs_to_university_stmt->errorInfo(), true));

			$creates_rso_sql = "INSERT INTO creates_rso(sid, rso_name) VALUES (:sid, :rso_name)";
			$creates_rso_stmt = $dbh->prepare($creates_rso_sql);
			$creates_rso_stmt->bindParam(':sid', $admin_id, PDO::PARAM_STR, 8);
			$creates_rso_stmt->bindParam(':rso_name', $rso_name, PDO::PARAM_STR, 80);
			$creates_rso_stmt->execute() or die(print_r($creates_rso_stmt->errorInfo(), true));

			$joins_rso_sql = "INSERT INTO joins_rso(sid, rso_name, approved, since) VALUES (:sid, :rso_name, :approved, :since)";
			$joins_rso_stmt = $dbh->prepare($joins_rso_sql);
			$joins_rso_stmt ->bindParam(':sid', $admin_id, PDO::PARAM_STR, 8);
			$joins_rso_stmt ->bindParam(':rso_name', $rso_name, PDO::PARAM_STR, 80);
			$joins_rso_stmt ->bindValue(':approved', 1);
			$joins_rso_stmt ->bindValue(':since', date("Y-m-d"));
			$joins_rso_stmt->execute() or die(print_r($joins_rso_stmt->errorInfo(), true));

			$owns_rso_sql = "INSERT INTO owns_rso(admin_id, rso_name) VALUES (:admin_id, :rso_name)";
			$owns_rso_stmt = $dbh->prepare($owns_rso_sql);
			$owns_rso_stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_STR, 8);
			$owns_rso_stmt->bindParam(':rso_name', $rso_name, PDO::PARAM_STR, 80);
			$owns_rso_stmt->execute() or die(print_r($owns_rso_stmt->errorInfo(), true));

		}
		else{
			$name_taken_error = "An RSO with the name ".$rso_name." already exists. Please select another name.";
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
					<?php
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
					}
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
							<h2>Student Organization Guidelines</h2>
						</header>
						<p>
						<ul style="list-style-type:circle" class="style3">
							<li class="first">All fields in this form are required.</li class="first">
							<li>Member e-mail addresses must all end in the same domain (your university's domain is preferred).</li>
							<li>Your Registered Student Organization must be approved by your university's super administrator before you can create events for it.</li>
							<li>Whoever requests to create the organization will become the group's administrator. Make sure you are okay with being responsible for member approval requests before signing up as the administrator.</li>
							<li>Provide a good description so other students will know if your group is something they would be interested in!</li>
							<br><br>
						</ul>
						</p>
					</section>
				</div>
				<div class="3u">
					<section id="sidebar2">
						<header style="text-align:center;">
							<h2 class="centered">Create Organization</h2>
						</header>

						<form class="pure-form centered" method="POST">
							<fieldset>
								<legend>Request to create a registered student organization</legend>
								<input style="width:80%;" type="text" name="rso_name" placeholder="Organization Name">
								<br><br>
								<?php if($name_taken_error != "") echo '<div>'.$name_taken_error.'</div><br>';?>
								<?php if($rso_error != "") echo '<div>'.$rso_error.'</div><br>';?>
								<select style="width:80%;" name="rso_university">
									<?php
									$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
									$sql ="SELECT university from student WHERE sid='".$_SESSION['id']."'";
									$stmt = $dbh->prepare($sql);
									$stmt->execute();
									$unames=$stmt->fetchAll();
									?>
									<option disabled selected>University</option>
									<?php foreach($unames as $uname):?>
										<option value="<?php print $uname['university']?>"><?php print $uname['university']; ?></option>
									<?php endforeach; ?>
								</select>
								<br><br>
								<?php if($university_error != "") echo '<div>'.$university_error.'</div><br>';?>
								<input style="width:80%;" type="email" name="mem1_email" placeholder="Member E-mail"><br><br>
								<input style="width:80%;" type="email" name="mem2_email" placeholder="Member E-mail"><br><br>
								<input style="width:80%;" type="email" name="mem3_email" placeholder="Member E-mail"><br><br>
								<input style="width:80%;" type="email" name="mem4_email" placeholder="Member E-mail"><br><br>
								<input style="width:80%;" type="email" name="mem5_email" placeholder="Member E-mail"><br><br>
								<?php if($email_error != "") echo '<div>'.$email_error.'</div><br>';?>
								<?php if($email_error2 != "") echo '<div>'.$email_error2.'</div><br>';?>
								<textarea style="width:80%;" rows="8" placeholder="Enter your organization description here." name="event_description"></textarea><br><br>
								<?php if($description_error != "") echo '<div>'.$description_error.'</div><br>';?>
								<button type="submit" name="submit" id="submit" class="small-button">Submit</button>
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