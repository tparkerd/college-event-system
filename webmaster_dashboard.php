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
  $response['error'] = $e->getMessage();
}

// Make sure that the user is a webmaster
try {
  $sql = "SELECT COUNT(*) FROM webmaster WHERE wid = :id";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
  $stmt->execute();
  $result = $stmt->fetchColumn();
} catch (PDOException $e) {
  $response['error'] = $e->getMessage();
}

// If they aren't a webmaster, kick them out
if (!$result)
  echo '<META HTTP-EQUIV=REFRESH CONTENT="1; permissions.php">';


// Check if an AJAX call was posted, post will not be empty
if(!empty($_POST)) {
  // Is this a call to approve or reject an event?
  if(isset($_POST['action'])) {
    // If it's an event action
    if ($_POST['type'] == 'university') {
      // If it's an approval, insert the event into the approved event of its type
      if ($_POST['action'] == 'approve') {
        $response['$_POST'] = $_POST;
        // Insert the university into the approved table
          try {
            $sql = "INSERT INTO university_approved_by VALUES(:wid, :university_name)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam('wid', $_SESSION['id'], PDO::PARAM_STR);
            $stmt->bindParam('university_name', $_POST['university_name'], PDO::PARAM_STR);
            $response = $stmt->execute();
          } catch (PDOException $e) {
            $response = $e->getMessage();
          }
          echo json_encode($response);
          exit;
        $response['test'] = 'got here';
        // If it's a rejection, do nothing? Do I delete the event?
      } elseif ($_POST['action'] == 'reject') {
        $response['action'] = 'Delete';
        $response['$_POST'] = $_POST;

        try {
          // Get the id of the superadmin
          $sql = "SELECT superadmin_id FROM creates_university WHERE university_name = :name";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':name', $_POST['university_name'], PDO::PARAM_STR);
          $stmt->execute();
          $id = $stmt->fetchColumn();

          // Remove relationships with a university (affiliates_university, creates_university)
          $sql = "DELETE FROM affiliates_university WHERE university_name = :name";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam('name', $_POST['university_name']);
          $stmt->execute();
          $sql = "DELETE FROM creates_university WHERE university_name = :name";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam('name', $_POST['university_name']);
          $stmt->execute();

          // Remove the user as a super admin (as a user can only be a superadmin of ONE university at a time)
          $response['id to delete'] = $id;
          $sql = "DELETE FROM superadmin WHERE superadmin_id = :id";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam('id', $id, PDO::PARAM_STR);
          $stmt->execute();

          // Remove university
          $sql = "DELETE FROM university WHERE university_name = :name";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam('name', $_POST['university_name']);
          $stmt->execute();
        } catch (PDOException $e) {
          $response['error'] = $e->getMessage();
        }
      echo json_encode($response);
      exit;
    }
  }

  // No action was requested, so just load data
} else {
    // If nothing has been posted, load the unapproved universities
    try {
      $sql = "SELECT u.university_name, u.address, u.description, u.picture_one, u.picture_two, CONCAT_WS(' ', s.given_name, s.family_name) AS creator_name, s.email
              FROM creates_university c
              LEFT JOIN student s
                ON s.sid = c.superadmin_id
              LEFT JOIN university u
                ON u.university_name = c.university_name
              WHERE c.university_name NOT IN (SELECT uab.university_name
                                              FROM university_approved_by uab)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $response['universities'] = json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
  } catch (PDOException $e) {
      $response['error'] = $e->getmessage();
    }
  }
  echo json_encode($response);
  exit;
}

?>

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
    <script src="js/init.js"></script>
    <script type="text/javascript">
  		// AJAX call to same page
  		$(function() {
  				$.ajax({
  					type			: 'POST',
  					url				: '',
  					data			: { 'AJAX Call Status' : 'Sent' },
  					dataType	: 'json',
            encode		: true
  				})
  					.done(function(data) {
              // Remember to parse JSON data back from the AJAX call
              universities = JSON.parse(data.universities)
              // Get reference to event table
              var table = $('#university_approval_table')

              // If there are no results, replace the table with text
              if (universities.length == 0) {
                  table.replaceWith($(document.createElement('p')).text('There are no universities pending approval at this time.'))
              }

              // For each, build it's place in the table
              universities.forEach(function(row, index) {
                console.log(row)

                // Start a new row
                var tr = $(document.createElement('tr'))

                // Append the university's name (column 1)
                table.append(tr.append($(document.createElement('td')).text(row.university_name)))

                // Append unversity's address (column 2)
                table.append(tr.append($(document.createElement('td')).text(row.address)))

                // Append unversity's description (column 3)
                table.append(tr.append($(document.createElement('td')).text(row.description)))

                // Append the name/email of the user that requested that the university be created (column 4)
                table.append(tr.append($(document.createElement('td')).html($(document.createElement('a')).text(row.creator_name).attr('href', 'mailto:' + row.email))))

                // Append the photos of the university (column 5)
                table.append(tr.append($(document.createElement('td')).append($(document.createElement('a')).attr('href', row.picture_one).html($(document.createElement('img')).attr('src', row.picture_one).addClass('thumbnail')))))
                table.append(tr.append($(document.createElement('td')).append($(document.createElement('a')).attr('href', row.picture_two).html($(document.createElement('img')).attr('src', row.picture_two).addClass('thumbnail')))))

                // Action Buttons (column 6)
                var td = $(document.createElement('td'))
                var approve = $(document.createElement('input')).attr('type', 'button').attr('value', 'Approve').attr('id', 'approval_' + row.university_name).addClass('approval').addClass('university')
                table.append(tr.append(td.append(approve)))
                var approve = $(document.createElement('input')).attr('type', 'button').attr('value', 'Reject').attr('id', 'rejection_' + row.university_name).addClass('rejection').addClass('university')
                table.append(tr.append(td.append(approve)))
              })

  					})
  					.fail(function(data) {
              console.log('Failure')
  						console.log(data)
  					})

            // Whenever an university is approved...
            $(document.body).on('click', '.approval.university', function(e) {
              $.ajax({
      					type			: 'POST',
      					url				: '',
      					data			: { 'action' : 'approve', 'type' : 'university', 'university_name' : e.target.id.split('_')[1] },
      					dataType	: 'json',
                encode		: true
              })
               .done(function(data) {
                 // remove the tr from the table
                 $(e.target).parent().parent().remove();
                 console.log(data)
               })
               .fail(function(data) {
                 // alert the user that it failed
                 console.log('Failed ajax')
                 console.log(data)
               })
            })

            // Whenever an university is rejected...
            $(document.body).on('click', '.rejection.university', function(e) {
              $.ajax({
      					type			: 'POST',
      					url				: '',
      					data			: { 'action' : 'reject', 'type' : 'university', 'university_name' : e.target.id.split('_')[1] },
      					dataType	: 'json',
                encode		: true
              })
               .done(function(data) {
                 // remove the tr from the table
                 $(e.target).parent().parent().remove();
                 console.log(data)
               })
               .fail(function(data) {
                 // alert the user that it failed
                 console.log('Failed ajax')
                 console.log(data)
               })
            })
  		}
    )

  	</script>
    <noscript>
        <link rel="stylesheet" href="css/skel-noscript.css" />
        <link rel="stylesheet" href="css/style.css" />
    </noscript>
    <style media="screen">
      h4 {
        font-size: 2rem;
      }
      table {
        width: 100%;
        margin: 2rem 0 1rem 0;
        outline: 1px solid rgb(31,31,31);
      }
      table thead tr:first-child {
        background: rgb(31,31,31);
        color: white;
      }
      table thead tr th:nth-child(n+2) {
        border-left: 1px solid #403f3f;
        padding: 0 3px;
      }
      table tr {
        line-height: 2rem;
      }
      table tr th {
        font-weight: 400;
      }
      table tbody tr td {
        vertical-align: middle;
      }
      table tbody tr:not(:last-child) { /* In case this doesn't work, nth-last-child(n+2)*/
        border-bottom: 1px solid #d0d0d0;
      }
      table tbody tr td:nth-child(n+2) {
        text-align: center;
        border-left: 1px solid #d0d0d0;
      }
      /* Shift the first element of a table over a little to the right (Name column) */
      table tr td:first-child {
        padding-left: 1rem;
      }
      table tbody tr:nth-child(even) {
        background: rgb(240, 240, 240);
      }
      .approval, .rejection {
        padding: 5px;
        margin: 3px 1px;
      }
      .thumbnail {
        max-width: 100px;
        margin: 0.5rem;
      }
    </style>
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
                        <h2>Webmaster Dashboard</h2>
                      </header>
                      <h4>Pending Universities</h4>
                      <table id="university_approval_table">
                        <thead>
                          <tr>
                            <th>
                              Name
                            </th>
                            <th>
                              Address
                            </th>
                            <th>
                              Description
                            </th>
                            <th>
                              Creator
                            </th>
                            <th>
                              Picture #1
                            </th>
                            <th>
                              Picture #2
                            </th>
                            <th>
                              Action
                            </th>
                          </tr>
                        </thead>
                        <tbody>

                        </tbody>
                      </table>

                      <h4>TODOs</h4>
                      <ol>
                        <li>EVERYTHINGS</li>
                      </ol>
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
