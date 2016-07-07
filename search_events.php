<?php session_start();?>
<?php
//temporary just so html page doesnt error on empty values from field
$keywords = $category = $public = $private = $rso = $start_date = $end_date = "";
$keywords_clause = $category_clause = $between_clause = $starting_clause = $ending_clause = "";
if (isset($_POST['submit'])) {
	$public_search_query = "SELECT * FROM public_event WHERE ";
	$private_search_query = "SELECT * FROM private_event WHERE ";

	if (isset($_POST['keywords'])){
		$keywords = strval($_POST['keywords']);
		$keywords_clause = "event_name LIKE %".$keywords."%";
	}
	if (isset($_POST['category'])) {
		$category = strval($_POST['category']);
		$category_clause = "event_category='".$category."'";
	}
	if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
		$start_date = strval($_POST['start_date']);
		$end_date = strval($_POST['end_date']);
		$between_clause = "(event_date BETWEEN '".$start_date."' AND '".$end_date."')";
	}
	if (isset($_POST['start_date']) && !isset($_POST['end_date'])) {
		$start_date = strval($_POST['start_date']);
		$starting_clause = "event_date >='".$start_date."'";
	}
	if (isset($_POST['start_date']) && !isset($_POST['end_date'])) {
		$start_date = strval($_POST['start_date']);
		$ending_clause = "(event_date BETWEEN DATE(NOW()) AND '".$end_date."')";
	}
	if (isset($_POST['public']))
		$public = boolval($_POST['public']);
	if (isset($_POST['private']))
		$private = boolval($_POST['private']);
	if (isset($_POST['rso']))
		$rso = boolval($_POST['rso']);

	$clause_array = array($keywords_clause, $category_clause, $between_clause, $starting_clause, $ending_clause);
	if($public){
		foreach($clause_array as $i=>$value){
			if($value != "" && $i == 0){
				$public_search_query .= $value;
			}
			else if($value != "" && $i != 0){
				$public_search_query .= " AND ".$value;
			}
		}
	}
	if($private){
		foreach($clause_array as $i=>$value){
			if($value != "" && $i == 0){
				$private_search_query .= $value;
			}
			else if($value != "" && $i != 0){
				$private_search_query .= " AND ".$value;
			}
		}
	}
	if($rso){

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
										<h2>Search Results for <?php print $keywords?></h2>
									</header>
									<p> Public: <?php print $public?> <br>
										Private: <?php print $private?> <br>
										RSO: <?php print $rso?> <br>
										Category: <?php print $category?> <br>
										Start Date: <?php print $start_date?> <br>
										End Date: <?php print $end_date?>

										<?php print $public_search_query?><br><br><br>
										<?php print $private_search_query?><br><br><br>
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
