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

			function getUniversityProfile(name) {
				if (name == "") {
					name="University of Central Florida"
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
					xmlhttp.open("GET", "/college-event-website/get_university.php?q="+name,true);
					xmlhttp.send();
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
							<li><a href="search.php">Search</a></li>
							<li  class="active"><a href="universities.php">Universities</a></li>
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
									$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=test', 'root', 'S#8roN*PJTMQWJ4m');
									$sql="SELECT * FROM Universities WHERE name='University of Central Florida'";
									$sth=$dbh->prepare($sql);
									foreach ($dbh->query($sql) as $row) {
										$uni_name = $row['name'];
										$address= $row['address'];
										$img_url_1 = $row['imageURL1'];
										$img_url_2 = $row['imageURL2'];
										$description = $row['description'];
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
															<p class="date"><a href="#">10.03.2012</a></p>
															<p><a href="#">Pellentesque erat erat, tincidunt in, eleifend, malesuada bibendum. Suspendisse sit amet  in eros bibendum condimentum. </a> </p>
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
								</div>";
							</section>
						</div>
						<div class="3u">
							<section id="sidebar2">
									<header style="text-align:center;">
										<h2 class="centered">Find a University to Join</h2>
									</header>

								<form class="pure-form centered">
									<fieldset>
										<legend>Add a university to your profile to see more events</legend>
										<br><br>
											<?php
												$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=test', 'root', 'S#8roN*PJTMQWJ4m');
												$sql ='SELECT name from Universities';
												$stmt = $dbh->prepare($sql);
												$stmt->execute();
												$unames=$stmt->fetchAll();
											?>
										<select onchange="getUniversityProfile(this.value)" style="width:260px;padding-bottom:5px" id="university" placeholder="University">
											<?php foreach($unames as $uname): ?>
												<option value="<?= $uname['name']; ?>"><?= $uname['name']; ?></option>
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
	</body>
</html>