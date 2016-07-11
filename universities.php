<?php session_start();?>


<html>
<head>
	<title>College Events</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="css/style.css" />
	<link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,900' rel='stylesheet' type='text/css'>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
	<script src="js/skel.min.js"></script>
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
					<li><a href="index.php">Homepage</a></li>
					<li><a href="create.php">Create</a></li>
					<li><a href="search.php">Search</a></li>
					<li  class="active"><a href="universities.php">Universities</a></li>
					<?php
					$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
					$superadmin_sql = "SELECT * FROM superadmin WHERE superadmin_id ='".$_SESSION['id']."'";
					$prep_sql = $dbh->prepare($superadmin_sql);
					$prep_sql->execute();
					$result = $prep_sql->fetch();
					if($result){
						echo '<li><a href="superadmin_dashboard.php">Dashboard</a></li>';
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
				<div class="9u skel-cell-important" >
					<section id="section-content">
						<div id="txtHint">
							<?php
							$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
							$sql="SELECT * FROM university LIMIT 1";
							$sth=$dbh->prepare($sql);
							foreach ($dbh->query($sql) as $row) {
								$uni_name = $row['university_name'];
								$address= $row['address'];
								$img_url_1 = $row['picture_one'];
								$img_url_2 = $row['picture_two'];
								$description = $row['description'];
								$num_students = $row['num_students'];
							}
							$dbh=null;
							?>
							<div class="container">
								<div class="row">
									<div class="3u">
										<section id="box1">
											<header>
												<h2>University Info</h2>
											</header>
											<ul class="style3">
												<li class="first">
													<p class="date">Address</p>
													<p><?php print $address?></p>
												</li>
												<li>
													<p> There are <?php print $num_students?> registered students for <?php print $uni_name?>.</p>
												</li>
											</ul>
										</section>
									</div>
									<div class="6u">
										<section id="box2">
											<header>
												<h2><?php print $uni_name ?></h2>
											</header>
											<div> <a href="#" class="image full"><img src="<?php echo $img_url_1?>" alt=""></a> </div>
											<p><?php print $description?></p>
										</section>
									</div>

								</div>
							</div>
						</div>
					</section>
				</div>
				<div class="3u">
					<section id="sidebar2">
						<header style="text-align:center;">
							<h2 class="centered">Find a University to Join</h2>
						</header>

						<form id="form_join_university" class="pure-form centered">
							<fieldset>
								<legend>Add a university to your profile to see more events</legend>
								<br><br>
								<?php
								$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
								$sql ='SELECT university_name from university';
								$stmt = $dbh->prepare($sql);
								$stmt->execute();
								$unames=$stmt->fetchAll();
								?>
								<select name="university_name" onchange="getUniversityProfile(this.value)" style="width:260px;padding-bottom:5px" id="university" placeholder="University">
									<?php foreach($unames as $uname):?>
										<option value="<?php print $uname['university_name']?>"><?php print $uname['university_name']; ?></option>
									<?php endforeach; ?>
								</select>
								<br><br>
								<button type="submit" class="small-button">Join</button>
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
<script type="text/javascript">
	function getUniversityProfile(name) {
		if (name == "") {
			name='University of Central Florida';
		}
		if (window.XMLHttpRequest) {
			// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp = new XMLHttpRequest();
		} else {
			// code for IE6, IE5
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange = function () {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				document.getElementById("txtHint").innerHTML = xmlhttp.responseText;
			}
		};
		xmlhttp.open("GET", "get_university.php?q="+name,true);
		xmlhttp.send();
	}

	// AJAX call to same page to join university
	$(function() {
		$('#form_join_university').submit(function(event) {
			var formData = {
				'university_name' : $('select[name=university_name]').val()
			}
			$.ajax({
				type			: 'POST',
				url				: '',
				data			: formData,
				dataType	: 'json',
				encode		: true
			})
				.done(function(data) {
					console.log(data)
					$('#form_join_university').parent().html(data.message)

				})
				.fail(function(data) {
					console.log(data)
				})
			event.preventDefault()
		})
	})

</script>
<?php if (!empty($_POST))
	// TODO(timp): implement a check for listing only approved universities (may need additional relationship in DB)
// Check if the form was posted
{
	// Check if a user is logged in
	if(!isset($_SESSION['id']))
	{
		echo json_encode("Please log in in order to join a university.");
		exit;
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

	// If successfully connected to the database...
	if(!isset($error_type)) {
		// Declare the reponse
		$response = array();

		// See if the user is a super admin, if so, they can never change their university from their assigned one
		try {
			$sql = "SELECT COUNT(*) FROM superadmin WHERE superadmin_id = :id";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
			$stmt->execute();
			$result = $stmt->fetchColumn();
		} catch (PDOException $e) {
			$response['error'] = $e->errorInfo[1];
		}

		// If they aren't a super admin, do not allow him/her ot switch universities
		if ($result) {
			$response['message'] = 'Since you are the super admin, you cannot switch universities.';
			echo json_encode($response);
			exit;
		}

		// Start checks
		// Is user already affiliated with a university?
		try {
			$sql = "SELECT university_name FROM affiliates_university WHERE sid = :id";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
			$stmt->execute();
			$response['university_name'] = $stmt->fetchColumn();
		} catch (PDOException $e) {
			$response['error'] = $e->errorInfo[1];
		}

		// If they are not assigned a university yet
		if ($response['university_name']) {
			// If the user already is a student of the university
			if ($response['university_name'] == $_POST['university_name'])
			{
				// ERROR: Student already belongs to the university, do nothing
				$response['message'] = "You are already a student of " . $_POST['university_name'];

				// Student is switching universities
			} else {
				try {
					// Update the student's university attribute
					$sql = "UPDATE student SET university = :university_name WHERE sid = :id";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':university_name', $_POST["university_name"], PDO::PARAM_STR);
					$stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
					$stmt->execute();

					// Update affiliates_university
					$sql = "UPDATE affiliates_university SET university_name = :university_name WHERE sid = :id";
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':university_name', $_POST["university_name"], PDO::PARAM_STR);
					$stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
					$stmt->execute();
				}	catch (PDOException $e) {
					$response['error'] = $e->errorInfo[1];
				}
				$response['message'] = "You have successfully switched to " . $_POST['university_name'];

			}
		} else {
			// Otherwise, the student does not have a university set.
			// Therefore, assign them to the university of their liking
			// UPDATE student & INSERT affiliates_university
			try {
				// Update the student's university attribute
				$sql = "UPDATE student SET university = :university_name WHERE sid = :id";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
				$stmt->bindParam(':university_name', $_POST["university_name"], PDO::PARAM_STR);
				$stmt->execute();

				// Affiliate the user with the university to increase student count
				$sql = "INSERT INTO affiliates_university VALUES(:id, :university_name)";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
				$stmt->bindParam(':university_name', $_POST["university_name"], PDO::PARAM_STR);
				$stmt->execute();

				$response['message'] = "You have successfully joined " . $_POST['university_name'];
			} catch (PDOException $e) {
				// Get the error code
				$response['error'] = $e->errorInfo[1];
			}
		}

		// Return the message to the AJAX call
		echo json_encode($response);
		exit;
	}
}

?>

</body>
</html>
