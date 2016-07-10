<?php session_start();?>
<?php
//temporary just so html page doesnt error on empty values from field
$keywords = $category = $public = $private = $rso = $start_date = $end_date = "";
$keywords_clause = $category_clause = $between_clause = $starting_clause = $ending_clause = "";
if (isset($_POST['submit'])) {
	$public_search_query = "SELECT * FROM public_event WHERE event_date >= DATE(NOW())";
	$private_search_query = "SELECT * FROM private_event e WHERE event_date >= DATE(NOW()) AND (SELECT university FROM student WHERE sid = e.approved_by_admin) = (SELECT university FROM student WHERE sid='".$_SESSION['id']."')";
	$pattern = "^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$";
	$rso_search_query = "SELECT * FROM rso_event WHERE event_date >= DATE(NOW()) AND (SELECT rso_eid FROM owns_event WHERE rso_name = (SELECT rso_name FROM affiliates_rso WHERE sid='".$_SESSION['id']."'))";
	if (!empty($_POST['keywords']) && $_POST['keywords'] != ""){
		$keywords = strval($_POST['keywords']);
		$keywords_clause = "event_name LIKE '%".$keywords."%'";
	}
	if (!empty($_POST['category'])) {
		$category = strval($_POST['category']);
		$category_clause = "event_category='".$category."'";
	}
	if (!empty($_POST['start_date']) && !empty($_POST['end_date']) && !preg_match($pattern, $start_date) && !preg_match($pattern, $end_date)) {
		$start_date = strval($_POST['start_date']);
		$end_date = strval($_POST['end_date']);
		$between_clause = "(event_date BETWEEN '".$start_date."' AND '".$end_date."')";
	}
	if (!empty($_POST['start_date']) && empty($_POST['end_date']) && !preg_match($pattern, $start_date) && preg_match($pattern, $end_date)) {
		$start_date = strval($_POST['start_date']);
		$starting_clause = "event_date >='".$start_date."'";
	}
	if (!empty($_POST['end_date']) && empty($_POST['start_date']) && !preg_match($pattern, $end_date) && preg_match($pattern, $start_date)) {
		$start_date = strval($_POST['start_date']);
		$ending_clause = "(event_date BETWEEN ".DATE(NOW())." AND '".$end_date."')";
	}
	if (!empty($_POST['public']))
		$public = boolval($_POST['public']);
	if (!empty($_POST['private']))
		$private = boolval($_POST['private']);
	if (!empty($_POST['rso']))
		$rso = boolval($_POST['rso']);

	$clause_array = array($keywords_clause, $category_clause, $between_clause, $starting_clause, $ending_clause);
	foreach($clause_array as $i=>$value){
		if($value != ""){
			$public_search_query .= " AND ".$value;
			$private_search_query .= " AND ".$value;
			$rso_search_query .= " AND ".$value;


		}
	}
	
	$rso_search_query .= " ORDER BY event_date";
	$public_search_query .= " ORDER BY event_date";
	$private_search_query .= " ORDER BY event_date";

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
		<script>
			function loadDoc(url, cfunc) {
				var xhttp;
				xhttp=new XMLHttpRequest();
				xhttp.onreadystatechange = function() {
					if (xhttp.readyState == 4 && xhttp.status == 200) {
						cfunc(xhttp);
					}
				};
				xhttp.open("GET", url, true);
				xhttp.send();
			}

			function myFunction(xhttp) {
				document.getElementById("search_results").innerHTML = xhttp.responseText;
			}
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
							<li class="active"><a href="search.php">Search</a></li>
							<li><a href="universities.php">Universities</a></li>
						</ul>
					</nav>
				</div>
			</div>
			<!-- /Header -->

			<div id="page">
				<div class="container">
					<div class="row">
						<div class="9u skel-cell-important" id="event_results">
							<section id="content">
								<div>
									<header>
										<h2>Search Results</h2>
									</header>
									<p>
									<ul class="style3">
										<?php $dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
										$sth_public=$dbh->prepare($public_search_query);
										if($public) {
											$sth_public->execute();
											while ($row = $sth_public->fetch(PDO::FETCH_ASSOC)) {
												echo "<p><li>";
												echo '<p style="font-size:24pt;"><u><a href="event_profile.php?eid=' . $row['eid'] . '">';
												print $row['event_name'] . "\t";
												echo "</a></u></p>";
												echo "<p class=\"date\"><b>";
												print $row['event_date'] . "\t";
												echo "</b></a></p><br><br><p>";
												print $row['description'];
												echo '<br>';
												echo "</li></p>";
											}
										}
										if($private) {
											$sth_private = $dbh->prepare($private_search_query);
											$sth_private->execute();
											while ($row = $sth_private->fetch(PDO::FETCH_ASSOC)) {
												echo "<p><li>";
												echo '<p style="font-size:24pt;"><u><a href="event_profile.php?eid=' . $row['eid'] . '">';
												print $row['event_name'] . "\t";
												echo "</a></u></p>";
												echo "<p class=\"date\"><b>";
												print $row['event_date'] . "\t";
												echo "</b></a></p><br><br><p>";
												print $row['description'];
												echo '<br>';
												echo "</li></p>";
											}
										}
										if($rso) {
											$sth_rso = $dbh->prepare($rso_search_query);
											$sth_rso->execute();
											while ($row = $sth_rso->fetch(PDO::FETCH_ASSOC)) {
												echo "<p><li>";
												echo '<p style="font-size:24pt;"><u><a href="event_profile.php?eid=' . $row['eid'] . '">';
												print $row['event_name'] . "\t";
												echo "</a></u></p>";
												echo "<p class=\"date\"><b>";
												print $row['event_date'] . "\t";
												echo "</b></a></p><br><br><p>";
												print $row['description'];
												echo '<br>';
												echo "</li></p>";
											}
										}
										?>
									</p>

									<p>

									</p>
								</div>
							</section>
						</div>
						<div class="3u">
							<section id="sidebar2">
									<header style="text-align:center;">
										<h2 class="centered">Search Events</h2>
									</header>

								<form name="search_form" id="search_form" class="pure-form centered"method="POST">
									<fieldset>
										<legend>Find events that match your interests</legend>
										<input type="text" name="keywords" placeholder="Search Keywords">
										<br><br>
										<h2 style="text-align:left; margin-left:40px">Filters:</h2>
										<div style="text-align:left; margin-left:40px">
										<label for="public_event_search" class="pure-checkbox">
											<input name="public" type="checkbox" value="true">
											Public Events
										</label>
										<label for="private_event_search" class="pure-checkbox">
											<input name="private" type="checkbox" value="true">
											Private Events
											</label>
										<label for="rso_event_search" class="pure-checkbox">
											<input name="rso" type="checkbox" value="true">
											RSO Events
										</label>
										</div>
										<br>
										<select style="width:190px" name="category" placeholder="Event Category">
											<option value="" disabled selected>Event Category</option>
											<option value="concert">Concert</option>
											<option value="tech-talk">Tech Talk</option>
											<option value="hackathon">Hack-A-Thon</option>
											<option value="hackathon">Sporting Event</option>
										</select> <br><br>
										<label for="search_start_date">
											<div style="text-align:left;margin-left:40px">From: <br><br></div>
										<input type="date" id="start_date" name="start_date" placeholder="Start Date"><br><br>
										</label>
										<label for="search_end_date">
											<div style="text-align:left;margin-left:40px">To:<br><br></div>
										<input type="date" id="end_date" name="end_date" placeholder="End Date"><br><br>
											</label>
										<button type="submit" id="submit" name="submit" class="small-button">Search</button>
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
