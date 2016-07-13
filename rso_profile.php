<?php
session_start();
  // Declare a response message for any AJAX call
$response = array();
// Check if a user is not logged in
// If so, redirect them to the permissions page
if(!isset($_SESSION['id']))
{
  $url='permissions.php';
  echo '<META HTTP-EQUIV=REFRESH CONTENT="0; '.$url.'">';
}

// Connect to database
try {
  $pdo = new PDO('mysql:host=sdickerson.ddns.net;dbname=ces;port=3306', 'root', 'S#8roN*PJTMQWJ4m');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  $response['error'] = $e->getMessage();
}

// Check if they have permission to view the page
// TODO(timp): maybe consider changing the check to see if the user is a member of the RSO first
try {
  $sql = "SELECT COUNT(*) FROM student WHERE sid = :id";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
  $stmt->execute();
  $response['session id'] = $stmt->fetchColumn();
} catch (PDOException $e) {
  $response['error'] = $e->getMessage();
}

// Make sure that they are logged in
if (!$response['session id'])
  echo '<META HTTP-EQUIV=REFRESH CONTENT="0; permissions.php">';

// Check if an AJAX call was posted, post will not be empty
if(!empty($_POST)) {
  $response['$_POST'] = $_POST;
  // Fetch all the info on the RSO
  try {
    $sql = "SELECT * FROM rso WHERE rso_name = :name";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $_POST['rso_name'], PDO::PARAM_STR);
    $stmt->execute();
    $response['rso_details'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    $response['error'] = $e->getMessage();
  }

  // Get the list of participants
  try {
    // Note that the user must be approved to show up
    // TODO(timp) AND approved = 1
    $sql = "SELECT CONCAT_WS(' ', s.given_name, s.family_name) AS name, s.email FROM student s, joins_rso j WHERE j.rso_name = :name AND j.sid = s.sid";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $_POST['rso_name'], PDO::PARAM_STR);
    $stmt->execute();
    $response['participants'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    $response['error'] = $e->getMessage();
  }

  // Get the details about the president
  try {
    $sql = "SELECT CONCAT_WS(' ', s.given_name, s.family_name) AS name, s.email FROM student s WHERE s.sid = (SELECT c.sid FROM creates_rso c WHERE c.rso_name = :name)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $_POST['rso_name'], PDO::PARAM_STR);
    $stmt->execute();
    $response['president'] = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    $response['error'] = $e->getMessage();
  }

  // Get all the RSO events associated with this specific RSO, approved.
  try {
    $sql = "SELECT event_name AS name, event_start_time AS start_time, event_end_time AS end_time, description, event_category AS category, event_date AS `date`, eid AS event_id
            FROM rso_event re
            LEFT JOIN rso_approved_by rab
              ON rab.rso_name = :name
            WHERE re.eid IN (SELECT reab.eid FROM rso_e_approved_by reab)
            AND re.eid = (SELECT oe.rso_eid FROM owns_event oe WHERE oe.rso_name = :name)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $_POST['rso_name'], PDO::PARAM_STR);
    $stmt->execute();
    $response['events'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    $response['error'] = $e->getMessage();
  }


  echo json_encode($response);
  exit;
}

?>

<!DOCTYPE HTML>
<html>
<head>
	<title></title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,900' rel='stylesheet' type='text/css'>
	<!--[if lte IE 8]><script src="js/html5shiv.js"></script><![endif]-->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
	<script src="js/skel.min.js"></script>
	<script src="js/skel-panels.min.js"></script>
	<script src="js/init.js"></script>
  <style>
    #participants ul {
      margin-top: 1rem;
    }
    h3 {
      font-size: 2rem;
      font-weight: bold;
    }
  </style>
	<noscript>
		<link rel="stylesheet" href="css/skel-noscript.css" />
		<link rel="stylesheet" href="css/style.css" />
	</noscript>
	<!--[if lte IE 8]><link rel="stylesheet" href="css/ie/v8.css" /><![endif]-->
	<!--[if lte IE 9]><link rel="stylesheet" href="css/ie/v9.css" /><![endif]-->
  <script type="text/javascript">
    function getURLParam(sParam) {
      var sPageURL = window.location.search.substring(1)
      var sURLVariables = sPageURL.split('&')
      for (var i = 0; i < sURLVariables.length; i++) {
        var sParameterName = sURLVariables[i].split('=')
        if (sParameterName[0] == sParam)
          return sParameterName[1]
      }
    }

    // AJAX call to same page
    $(function() {
        $.ajax({
          type			: 'POST',
          url				: '',
          data			: { 'rso_name' : getURLParam('name').replace(/%20/g, ' ') },
          dataType	: 'json',
          encode		: true
        })
          .done(function(data) {
            $('title').text(data.rso_details[0].rso_name)
            $('#name').text(data.rso_details[0].rso_name)
            $('#description').text( (data.rso_details[0].description == null) ? 'No description provided.' : data.rso_details[0].description)

            for(participant in data.participants) {
              $('#participants')
                .append($(document.createElement('ul'))
                  .append($(document.createElement('li'))
                    .text((data.president.name == data.participants[participant].name) ? data.participants[participant].name + ' (Admin)' : data.participants[participant].name)))
            }

            // Construct RSO event list
            var list = $(document.createElement('ul')).addClass('style3')
            for(event in data.events) {
              var datetime = new Date(data.events[event].date)
              // Format Date
              var monthNames = [
                                "January", "February", "March",
                                "April", "May", "June", "July",
                                "August", "September", "October",
                                "November", "December"
                              ];
              var date = (datetime.getDate() + 1) + ' ' + monthNames[datetime.getMonth()] + ' ' + datetime.getFullYear()

              // Create List
              var item = $(document.createElement('li'))
              var p = $(document.createElement('p')).attr('style', 'font-size:24pt');
              var u = $(document.createElement('u'))
              var a = $(document.createElement('a')).attr('href', 'event_profile.php?eid=' + data.events[event].event_id).text(data.events[event].name)
              var p2 = $(document.createElement('p')).addClass('date').text(data.events[event].date)
              var desc = $(document.createElement('p')).text(data.events[event].description)

              list.append(item.append(p.append(u.append(a)))).append(p2).append(desc)
            }
            $('#events').append(list)
            if (data.events.length == 0)
              $('#events').text('Currently there are no planned events.')



          })
          .fail(function(data) {
            console.log('Failure')
            console.log(data)
          })
          .always(function(data) {
            console.log(data)
          })
    }
  )

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
					<li class="active"><a href="create.php">Create</a></li>
					<li><a href="search.php">Search</a></li>
					<li><a href="universities.php">Universities</a></li>
					<?php
					$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
					$superadmin_sql = "SELECT * FROM superadmin WHERE superadmin_id ='".$_SESSION['id']."'";
					$prep_sql = $dbh->prepare($superadmin_sql);
					$prep_sql->execute();
					$result = $prep_sql->fetch();
					if($result) {
						echo '<li><a href="superadmin_dashboard.php">Dashboard</a></li>';
					}
					$webmaster_sql = "SELECT * FROM webmaster WHERE wid ='".$_SESSION['id']."'";
					$prep_webmaster_sql = $dbh->prepare($webmaster_sql);
					$prep_webmaster_sql->execute();
					$result2 = $prep_webmaster_sql->fetch();
					if($result2){
					echo '<li><a href="webmaster_dashboard.php">Webmaster Dashboard</a></li>';
					}?>
				</ul>
			</nav>
		</div>
	</div>
	<!-- /Header -->

	<div id="page">
		<div style="max-width=500px" class="container">
			<div style="max-width:80%;" class="row" >
				<div class="9u skel-cell-important">
					<section id="content">
						<header>
							<h2 id="name"></h2>
						</header>
            <div>
              <h3>Description</h3>
              <p id="description"></p>
            </div>
            <div>
              <h3>Events</h3>
              <div id="events"></div>
            </div>
					<br><br>
				</div>
				<div class="3u">
					<section id="sidebar2">
						<header style="text-align:left; margin-top:20px">
							<h2>Participants</h2>
              <div id="participants"></div>
						</header>
            <div class="sidebar">

            </div>
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
