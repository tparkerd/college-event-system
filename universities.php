<?php session_start();
// Check if the form was posted
if (!empty($_POST))
{
	// Check if a user is logged in
	if(!isset($_SESSION['id']))
	{
		echo json_encode("Please log in in order to join a university.");
		exit;
	}


	$response = array();
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
		$response['error'] = $e->errorInfo[0];
	}
	// If successfully connected to the database...
	if(!isset($response['error']) && isset($_POST['action'])) {
		// Always populate the drop down list
		$sql = "SELECT university_name FROM university_approved_by ORDER BY university_name";
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
		$response['list'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

		// Initialize what university should be shown
		if ($_POST['action'] == 'init') {
			try {
				$sql = "SELECT * FROM university WHERE university_name IN (SELECT university_name FROM university_approved_by ORDER BY university_name) LIMIT 1";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':name', $_POST['university_name'], PDO::PARAM_STR);
				$stmt->execute();
				$response['default_university'] = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch (PDOException $e) {
				$response['error'] = $e->getMessage();
			}
		// Load the data on the university into the page
		} elseif ($_POST['action'] == 'load') {
			try {
				$sql = "SELECT * FROM university WHERE university_name = :name AND university_name IN (SELECT university_name FROM university_approved_by ORDER BY university_name)";
				$stmt = $pdo->prepare($sql);
				$stmt->bindParam(':name', $_POST['university_name'], PDO::PARAM_STR);
				$stmt->execute();
				$response = $stmt->fetch(PDO::FETCH_ASSOC);
			} catch (PDOException $e) {
				$response['error'] = $e->getMessage();
			}

		// Otherwise, the user must be trying to join a university
		} elseif($_POST['action'] == 'join') {
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
		}
		// Return the message to the AJAX call
		echo json_encode($response);
		exit;
	}
}
?>

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
	<script type="text/javascript">
		// AJAX call to same page to join university
		$(function() {

			// Initialize the page with University of Central Florida information
				var formData = {
					'university_name' : 'University of Central Florida',
					'action' : 'init'
				}

				$.ajax({
					type			: 'POST',
					url				: '',
					data			: formData,
					dataType	: 'json',
					encode		: true
				})
					.done(function(data) {
						var d = data.default_university
						// Display name
						$('#university_name').text(d.university_name)
						// Display address
						$('#address').text(d.address)
						// Display number of afffiliated students
						$('#num_students').text(
																		(parseInt(d.num_students) == 1) ?
																		'There is 1 student registered for ' + d.university_name + '.' :
																		'There are ' + d.num_students + ' students registered for ' + d.university_name + '.'
																		)
						// Display photos of university
						$('#university_photos').html($(document.createElement('a')).addClass('image full').attr('href', d.picture_one).append($(document.createElement('img')).attr('src', d.picture_one)))
						$('#university_photos').append($(document.createElement('a')).addClass('image full').attr('href', d.picture_two).append($(document.createElement('img')).attr('src', d.picture_two)))
						// Display description
						$('#description').text(d.description)

						// Populate the drop down menu
						var list = $('#university_list')
						for (var row in data.list) {
							list.append($(document.createElement('option')).attr('value', data.list[row].university_name).text(data.list[row].university_name))
						}
					})
					.fail(function(data) {
						console.log('Fail')
					})
					.always(function(data) {
						console.log(data)
					})

			$('#university_list').change(function(e) {
				var formData = {
					'university_name' : $('select[name=university_name]').val(),
					'action' : 'load'
				}

				$.ajax({
					type			: 'POST',
					url				: '',
					data			: formData,
					dataType	: 'json',
					encode		: true
				})
					.done(function(data) {
						// Display name
						$('#university_name').text(data.university_name)
						// Display address
						$('#address').text(data.address)
						// Display number of afffiliated students
						$('#num_students').text(
																		(parseInt(data.num_students) == 1) ?
																		'There is 1 student registered for ' + data.university_name + '.' :
																		'There are ' + data.num_students + ' students registered for ' + data.university_name + '.'
																	)
						// Display photos of university
						$('#university_photos').html($(document.createElement('a')).addClass('image full').attr('href', data.picture_one).append($(document.createElement('img')).attr('src', data.picture_one)))
						$('#university_photos').append($(document.createElement('a')).addClass('image full').attr('href', data.picture_two).append($(document.createElement('img')).attr('src', data.picture_two)))
						// Display description
						$('#description').text(data.description)
					})
					.fail(function(data) {
						console.log('Fail')
					})
					.always(function(data) {
						console.log(data)
					})

			})

			$('#form_join_university').submit(function(event) {
				var formData = {
					'university_name' : $('select[name=university_name]').val(),
					'type' 						: 'join'
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
				<div class="9u skel-cell-important" >
					<section id="section-content">
						<div id="txtHint">
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
													<p id="address"></p>
												</li>
												<li>
													<p id="num_students"></p>
												</li>
											</ul>
										</section>
									</div>
									<div class="6u">
										<section id="box2">
											<header>
												<h2 id="university_name"></h2>
											</header>
											<div id="university_photos"></div>
											<p id="description"></p>
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
								<select name="university_name" style="width:260px;padding-bottom:5px" id="university_list">
										<option selected="true" disabled>Select a university</option>
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
</body>
</html>
