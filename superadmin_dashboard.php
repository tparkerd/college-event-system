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

// Check if an AJAX call was posted, post will not be empty
if(!empty($_POST)) {
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
    $response['error'] = $e->errorInfo[1];
  }


  // Is this a call to approve or reject an event?
  if(isset($_POST['action'])) {

    // If it's an approval, insert the event into the approved event of its type
    if ($_POST['action'] == 'approve') {
      // Is it a public event?
      try {
        $sql = "SELECT COUNT(*) FROM public_event WHERE eid = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam('id', $_POST['eid'], PDO::PARAM_STR);
        $stmt->execute();
        $response['event_type'] = $stmt->fetchColumn();
      } catch (PDOException $e) {
        $response = $e->errorInfo;
      }

      // If it is public...
      if($response['event_type']) {
        try {
          $response['message'] = 'trying to insert';
          $sql = "INSERT INTO public_approved_by VALUES(:eid, :superadmin_id)";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam('eid', $_POST['eid'], PDO::PARAM_STR);
          $stmt->bindParam('superadmin_id', $_SESSION['id'], PDO::PARAM_STR);
          $response = $stmt->execute();
        } catch (PDOException $e) {
          $response = $e->errorInfo[1];
        }
        echo json_encode($response);
        exit;
      }

      // Is it a private event?
      try {
        $sql = "SELECT COUNT(*) FROM private_event WHERE eid = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam('id', $_POST['eid'], PDO::PARAM_STR);
        $stmt->execute();
        $response['event_type'] = $stmt->fetchColumn();
      } catch (PDOException $e) {
        $response = $e->errorInfo;
      }

      // If it is private...
      if($response['event_type']) {
        try {
          $response['message'] = 'trying to insert';
          $sql = "INSERT INTO private_approved_by VALUES(:eid, :superadmin_id)";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam('eid', $_POST['eid'], PDO::PARAM_STR);
          $stmt->bindParam('superadmin_id', $_SESSION['id'], PDO::PARAM_STR);
          $response = $stmt->execute();
        } catch (PDOException $e) {
          $response = $e->errorInfo[1];
        }
        echo json_encode($response);
        exit;
      }

      // Is it an rso event?
      try {
        $sql = "SELECT COUNT(*) FROM rso_event WHERE eid = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam('id', $_POST['eid'], PDO::PARAM_STR);
        $stmt->execute();
        $response['event_type'] = $stmt->fetchColumn();
      } catch (PDOException $e) {
        $response = $e->errorInfo;
      }

      // If it is private...
      if($response['event_type']) {
        try {
          $response['message'] = 'trying to insert';
          $sql = "INSERT INTO rso_approved_by VALUES(:eid, :superadmin_id)";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam('eid', $_POST['eid'], PDO::PARAM_STR);
          $stmt->bindParam('superadmin_id', $_SESSION['id'], PDO::PARAM_STR);
          $response = $stmt->execute();
        } catch (PDOException $e) {
          $response = $e->errorInfo[1];
        }
        echo json_encode($response);
        exit;
      }


    // If it's a rejection, do nothing? Do I delete the event?
    } elseif ($_POST['action'] == 'rejection') {
      // Maaaaaybe delete?
      echo json_encode('What do you want me to do to reject something? Delete it from the e(EVENT) table?');
      exit;
    }
    echo json_encode($response);
    exit;


  }



  // Make sure that the user is a super admin
  try {
    $sql = "SELECT COUNT(*) FROM superadmin WHERE superadmin_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchColumn();
  } catch (PDOException $e) {
    $response['error'] = $e->errorInfo[1];
  }

  // If they aren't a super admin, kick them out (this caused problems before, so this may need to be watched)
  if (!$result)
    echo '<META HTTP-EQUIV=REFRESH CONTENT="0; permissions.php">';

  // Find out the super admin's university
  try {
    $sql = "SELECT university_name FROM creates_university WHERE superadmin_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
    $stmt->execute();
    $university_name = $response['university_name'] = $stmt->fetchColumn();
  } catch (PDOException $e) {
    $response['error'] = $e->errorInfo[1];
  }

  // Get a list of all events associated with the university
  try {
    // Get a list of any event that's listed in the public events (or any type that is, that is not listed in its respective sub-class)
    $sql = "SELECT eid, event_name, event_date, event_start_time, event_end_time, event_category, contact_email, contact_phone FROM e WHERE e.eid IN (SELECT p.eid FROM public_event p WHERE e.eid = p.eid)
            AND e.eid NOT IN (SELECT pab.eid FROM public_approved_by pab WHERE e.eid = pab.eid)
            AND e.approved_by_admin IN (SELECT sid FROM affiliates_university WHERE university_name = :university_name)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':university_name', $university_name, PDO::PARAM_STR);
    $stmt->execute();
    $result = $response['result'] = json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
  } catch (PDOException $e) {
    $response['error'] = $e->errorInfo[1];
  }

  // TODO(timp): include private and RSO events in the query unless I need to separate them out


  // Since this was an AJAX call, end the page here to avoid
  // echo json_encode($response['message']);
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
              data = JSON.parse(data.result)

              // Get reference to event table
              var table = $('#event_approval_table')

              // For each, build it's place in the table
              data.forEach(function(row, index) {
                console.log(row)

                // Make a new row
                var tr = $(document.createElement('tr'))

                // First column (Name)
                var td = $(document.createElement('td'))
                // Get name and make it a URL link to its page
                var name = $(document.createElement('a')).attr('href', 'event_profile.php?eid=' + row.eid).text(row.event_name)

                // Add to table
                table.append(tr.append(td.append(name)))

                // Second columnn (Date)
                var td = $(document.createElement('td'))
                // Get date
                var datetime = new Date(row.event_date + ' 00:00:00 EDT')

                // Format Date
                var monthNames = [
                                  "January", "February", "March",
                                  "April", "May", "June", "July",
                                  "August", "September", "October",
                                  "November", "December"
                                ];
                var month = monthNames[datetime.getMonth()]
                var date = datetime.getDay() + ' ' + month + ' ' + datetime.getFullYear()

                // Insert date into table
                table.append(tr.append(td.text(date)))

                // Third column (Duration/Time)
                var td = $(document.createElement('td'))
                // Add to table (\u2013 is UNICODE for an en dash)
                table.append(tr.append(td.text(row.event_start_time + ' \u2013 ' + row.event_end_time)))

                // Fouth column (Category)
                var td = $(document.createElement('td'))
                // Add to table
                table.append(tr.append(td.text(row.event_category)))

                // Fifth column (Contact Email)
                // Add to table
                table.append(tr.append($(document.createElement('td')).text(row.contact_email)))

                // Fifth column (Contact Phone)
                // Add to table & format phone number (the formatting may need to be removed based on how numbers can be entered by the user)
                table.append(tr.append($(document.createElement('td')).text(row.contact_phone.replace(/(\d\d\d)(\d\d\d)(\d\d\d\d)/, '($1) $2-$3'))))

                // Sixth column (Action Buttons)
                var td = $(document.createElement('td'))
                var approve = $(document.createElement('input')).attr('type', 'button').attr('value', 'Approve').attr('id', 'approval_' + row.eid).addClass('approval')
                table.append(tr.append(td.append(approve)))
                var approve = $(document.createElement('input')).attr('type', 'button').attr('value', 'Reject').attr('id', 'rejection_' + row.eid).addClass('rejection')
                table.append(tr.append(td.append(approve)))
              })
  					})
  					.fail(function(data) {
              console.log('Failure')
  						console.log(data)
  					})

            // Whenever an event is approved...
            $(document.body).on('click', '.approval', function(e) {
              $.ajax({
      					type			: 'POST',
      					url				: '',
      					data			: { 'action' : 'approve', 'eid' : e.target.id.split('_')[1] },
      					dataType	: 'json',
                encode		: true
              })
               .done(function(data) {
                 // remove the tr from the table
                 $(e.target).parent().parent().remove();
                 console.log(data)
                 console.log(rowparent)
               })
               .fail(function(data) {
                 // alert the user that it failed
                 console.log('Failed ajax')
                 console.log(data)
               })
            })

            // Whenever an event is rejected...
            $(document.body).on('click', '.rejection', function(e) {
              $.ajax({
      					type			: 'POST',
      					url				: '',
      					data			: { 'action' : 'rejection', 'eid' : e.target.id.split('_')[1] },
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
        border: 1px solid rgb(31,31,31);
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
      table tbody tr td:nth-child(n+2) {
        text-align: center;
        border-left: 1px solid #d0d0d0;
      }
      /* Shift the first element of a table over a little to the right (Name column) */
      table tr td:first-child {
        padding-left: 1rem;
      }
      .approval, .rejection {
        padding: 5px;
        margin: 3px 1px;
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
                        <h2>Superadmin Dashboard</h2>
                      </header>
                      <h4>Pending event approvals</h4>
                      <p>
                        In order for anything to show up, you have to be the superadmin for the university that any event is listed under. Right now, you have to had created the university.
                        A super admin needs to either approve or reject(delete???) an event (private, public, rso) and RSO
                      </p>
                      <ul>TODOs
                        <li>Try to tie in the accept and reject button into their EIDs (probably going to have to modify SQL query to provide that data as well)</li>
                        <li>Add in functionality to approve or reject buttons (maybe an AJAX call and then a recall of on load script..somethignsomething)</li>
                      </ul>
                      <table id="event_approval_table">
                        <thead>
                          <tr>
                            <th>
                              Name
                            </th>
                            <th>
                              Date
                            </th>
                            <th>
                              Time
                            </th>
                            <th>
                              Category
                            </th>
                            <th>
                              Contact Email
                            </th>
                            <th>
                              Contact Phone
                            </th>
                            <th>
                              Action
                            </th>
                          </tr>
                        </thead>
                        <tbody>

                        </tbody>
                      </table>
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
