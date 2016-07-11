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

// Make sure that the user is a super admin (maybe move this and the database connection before the check for !empty($_POST))
try {
  $sql = "SELECT COUNT(*) FROM superadmin WHERE superadmin_id = :id";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
  $stmt->execute();
  $result = $stmt->fetchColumn();
} catch (PDOException $e) {
  $response['error'] = $e->getMessage();
}

// If they aren't a super admin, kick them out (this caused problems before, so this may need to be watched)
if (!$result)
  echo '<META HTTP-EQUIV=REFRESH CONTENT="0; permissions.php">';

// Check if an AJAX call was posted, post will not be empty
if(!empty($_POST)) {
  // Is this a call to approve or reject an event?
  if(isset($_POST['action'])) {

    // If it's an event action
    if ($_POST['type'] == 'event') {
      // If it's an approval, insert the event into the approved event of its type
      if ($_POST['action'] == 'approve') {
        $response['$_POST'] = $_POST;
        // Is it a public event?
        try {
          $sql = "SELECT COUNT(*) FROM public_event WHERE eid = :id";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam('id', $_POST['eid'], PDO::PARAM_STR);
          $stmt->execute();
          $response['event_type'] = $stmt->fetchColumn();
        } catch (PDOException $e) {
          $response = $e->getMessage();
        }
        $response['Public?'] = $response['event_type'];
        // If it is public...
        if($response['event_type']) {
          try {
            $sql = "INSERT INTO public_approved_by VALUES(:eid, :superadmin_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam('eid', $_POST['eid'], PDO::PARAM_STR);
            $stmt->bindParam('superadmin_id', $_SESSION['id'], PDO::PARAM_STR);
            $response = $stmt->execute();
          } catch (PDOException $e) {
            $response = $e->getMessage();
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
          $response = $e->getMessage();
        }

        $response['Private?'] = $response['event_type'];
        // If it is private...
        if($response['event_type']) {
          try {
            $sql = "INSERT INTO private_approved_by VALUES(:eid, :superadmin_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam('eid', $_POST['eid'], PDO::PARAM_STR);
            $stmt->bindParam('superadmin_id', $_SESSION['id'], PDO::PARAM_STR);
            $response = $stmt->execute();
          } catch (PDOException $e) {
            $response = $e->getMessage();
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
          $response = $e->getMessage();
        }

        $response['RSO?'] = $response['event_type'];
        // If it is rso...
        if($response['event_type']) {
          try {
            $sql = "INSERT INTO rso_approved_by VALUES(:eid, :superadmin_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam('eid', $_POST['eid'], PDO::PARAM_STR);
            $stmt->bindParam('superadmin_id', $_SESSION['id'], PDO::PARAM_STR);
            $response = $stmt->execute();
          } catch (PDOException $e) {
            $response = $e->getMessage();
          }


          echo json_encode($response);
          exit;
        }


        // If it's a rejection, do nothing? Do I delete the event?
      } elseif ($_POST['action'] == 'reject') {
        $response['action'] = 'Delete';
        $response['Eid'] = $_POST['eid'];
        $response['$_POST'] = $_POST;

        try {
          // Remove relationships with an EVENT (at, comments, private_event, public_event, rso_event)
          $sql = "DELETE FROM at WHERE eid = :id";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam('id', $_POST['eid']);
          $stmt->execute();
          $sql = "DELETE FROM comments WHERE eid = :id";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam('id', $_POST['eid']);
          $stmt->execute();
          $sql = "DELETE FROM private_event WHERE eid = :id";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam('id', $_POST['eid']);
          $stmt->execute();
          $sql = "DELETE FROM public_event WHERE eid = :id";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam('id', $_POST['eid']);
          $stmt->execute();
          $sql = "DELETE FROM rso_event WHERE eid = :id"; // may be unnecessary
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam('id', $_POST['eid']);
          $stmt->execute();
          $sql = "DELETE FROM creates_event WHERE eid = :id"; // may be unnecessary
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam('id', $_POST['eid']);
          $stmt->execute();

          // Remove RSO
          $sql = "DELETE FROM e WHERE eid = :id";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam('id', $_POST['eid']);
          $stmt->execute();

        } catch (PDOException $e) {
          $response['error'] = $e->getMessage();
        }

      }
      echo json_encode($response);
      exit;

    // But if its an RSO...
  } elseif ($_POST['type'] == 'rso') {
      // If it's an approval, insert the event into the approved event of its type
      if ($_POST['action'] == 'approve') {

          try {
            $response['message'] = 'trying to insert';
            $response['superadmin id'] = $_SESSION['id'];
            $sql = "INSERT INTO rso_approved_by(rso_name, superadmin_id) VALUES(:rso_name, :superadmin_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam('rso_name', $_POST['rso_name'], PDO::PARAM_STR);
            $stmt->bindParam('superadmin_id', $_SESSION['id'], PDO::PARAM_STR);
            $response = $stmt->execute();
          } catch (PDOException $e) {
            $response['error'] = $e->getMessage();
          }
          echo json_encode($response);
          exit;

        // If it's a rejection, do nothing? Do I delete the event?
      } elseif ($_POST['action'] == 'reject') {
        $response['action'] = 'Delete';
        $response['RSO name'] = $_POST['rso_name'];
        $response['$_POST'] = $_POST;

        try {
          // Remove relationships with the user and its RSO
          $sql = "DELETE FROM creates_rso WHERE rso_name = :rso_name";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam('rso_name', $_POST['rso_name']);
          $stmt->execute();
          $sql = "DELETE FROM owns_rso WHERE rso_name = :rso_name";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam('rso_name', $_POST['rso_name']);
          $stmt->execute();
          $sql = "DELETE FROM affiliates_rso WHERE rso_name = :rso_name";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam('rso_name', $_POST['rso_name']);
          $stmt->execute();
          $sql = "DELETE FROM joins_rso WHERE rso_name = :rso_name"; // I have no idea why there is an affiliates_rso and joins_rso... o.O
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam('rso_name', $_POST['rso_name']);
          $stmt->execute();

          // Remove RSO
          $sql = "DELETE FROM rso WHERE rso_name = :rso_name";
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam('rso_name', $_POST['rso_name']);
          $stmt->execute();

        } catch (PDOException $e) {
          $response['error'] = $e->getMessage();
        }

        echo json_encode($response);
        exit;
      }
      echo json_encode($response);
      exit;
    }
  }

  // Find out the super admin's university
  try {
    $sql = "SELECT university_name FROM creates_university WHERE superadmin_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $_SESSION['id'], PDO::PARAM_STR);
    $stmt->execute();
    $university_name = $stmt->fetchColumn();
  } catch (PDOException $e) {
    $response['error'] = $e->getMessage();
  }

  // Get a list of all events associated with the university
  try {
    // Get a list of any event that has not yet been approved
    $sql = "SELECT eid, event_name, event_date, event_start_time, event_end_time, event_category, contact_email, contact_phone
            FROM e
            WHERE e.eid IN (SELECT p.eid FROM public_event p WHERE e.eid = p.eid)
              AND e.eid NOT IN (SELECT pab.eid FROM public_approved_by pab WHERE e.eid = pab.eid)
              AND e.approved_by_admin IN (SELECT sid FROM affiliates_university WHERE university_name = :university_name)
            UNION
            SELECT eid, event_name, event_date, event_start_time, event_end_time, event_category, contact_email, contact_phone
            FROM e
            WHERE e.eid IN (SELECT p.eid FROM private_event p WHERE e.eid = p.eid)
              AND e.eid NOT IN (SELECT pab.eid FROM private_approved_by pab WHERE e.eid = pab.eid)
              AND e.approved_by_admin IN (SELECT sid FROM affiliates_university WHERE university_name = :university_name)
            UNION
            SELECT eid, event_name, event_date, event_start_time, event_end_time, event_category, contact_email, contact_phone
            FROM e
            WHERE e.eid IN (SELECT p.eid FROM rso_event p WHERE e.eid = p.eid)
              AND e.eid NOT IN (SELECT reab.eid FROM rso_e_approved_by reab WHERE e.eid = reab.eid)
              AND e.approved_by_admin IN (SELECT sid FROM affiliates_university WHERE university_name = :university_name)
            ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':university_name', $university_name, PDO::PARAM_STR);
    $stmt->execute();
    $response['events'] = json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
  } catch (PDOException $e) {
    $response['error'] = $e->getMessage();
  }

  // Get a list of all RSOs associated with the university
  try {
    // Get a list of any RSO that has not yet been approved
    $sql = "SELECT r.rso_name, r.admin_id, s.given_name, s.family_name, s.email
            FROM rso r, student s
            WHERE r.rso_name NOT IN (SELECT rab.rso_name FROM rso_approved_by rab WHERE r.rso_name = rab.rso_name)
              AND s.sid = r.admin_id
              AND r.admin_id IN (SELECT sid FROM affiliates_university WHERE university_name = :university_name)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':university_name', $university_name, PDO::PARAM_STR);
    $stmt->execute();
    $response['rsos'] = json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
  } catch (PDOException $e) {
    $response['error'] = $e->getMessage();
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
              events = JSON.parse(data.events)
              rsos = JSON.parse(data.rsos)
              console.log(data)
              // Get reference to event table
              var table = $('#event_approval_table')

              // If there are no results, replace the table with text
              if (events.length == 0) {
                  table.replaceWith($(document.createElement('p')).text('There are no events pending approval at this time.'))
              }

              // For each, build it's place in the table
              events.forEach(function(row, index) {
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
                var date = datetime.getDate() + ' ' + month + ' ' + datetime.getFullYear()

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
                table.append(tr.append($(document.createElement('td')).append($(document.createElement('a')).attr('href', 'mailto:' + row.contact_email).text(row.contact_email))))

                // Fifth column (Contact Phone)
                // Add to table & format phone number (the formatting may need to be removed based on how numbers can be entered by the user)
                table.append(tr.append($(document.createElement('td')).text(row.contact_phone.replace(/[^0-9]/g, '').replace(/(\d\d\d)(\d\d\d)(\d\d\d\d)/, '($1) $2-$3'))))

                // Sixth column (Action Buttons)
                var td = $(document.createElement('td'))
                var approve = $(document.createElement('input')).attr('type', 'button').attr('value', 'Approve').attr('id', 'approval_' + row.eid).addClass('approval').addClass('event')
                table.append(tr.append(td.append(approve)))
                var approve = $(document.createElement('input')).attr('type', 'button').attr('value', 'Reject').attr('id', 'rejection_' + row.eid).addClass('rejection').addClass('event')
                table.append(tr.append(td.append(approve)))
              })


              // Construct RSO approval table
              var table = $('#rso_approval_table')
              if (rsos.length == 0) {
                  table.replaceWith($(document.createElement('p')).text('There are no RSOs pending approval at this time.'))
              }

              rsos.forEach(function(row, index) {
                // Make a new row
                var tr = $(document.createElement('tr'))

                // First column (RSO name)
                var td = $(document.createElement('td'))
                // Get name and make it a URL link to its page
                var name = $(document.createElement('a')).attr('href', 'rso_profile.php?rso_name=' + row.rso_name).text(row.rso_name)
                // Add to table
                table.append(tr.append(td.append(name)))

                // Second column (Creator name)
                // Add to table
                table.append(tr.append($(document.createElement('td')).text(row.given_name + ' ' + row.family_name)))

                // Third column (Creator email)
                // Add to table
                table.append(tr.append($(document.createElement('td')).append($(document.createElement('a')).attr('href', 'mailto:' + row.email).text(row.email))))
                // table.append(tr.append($(document.createElement('td')).append($(document.createElement('a')).attr('href', 'mailto:' + row.contact_email).text(row.contact_email))))

                // Fourth column (Action Buttons)
                var td = $(document.createElement('td'))
                var approve = $(document.createElement('input')).attr('type', 'button').attr('value', 'Approve').attr('id', 'approval_' + row.rso_name).addClass('approval').addClass('rso')
                table.append(tr.append(td.append(approve)))
                var approve = $(document.createElement('input')).attr('type', 'button').attr('value', 'Reject').attr('id', 'rejection_' + row.rso_name).addClass('rejection').addClass('rso')
                table.append(tr.append(td.append(approve)))
              })
  					})
  					.fail(function(data) {
              console.log('Failure')
  						console.log(data)
  					})

            // Whenever an event is approved...
            $(document.body).on('click', '.approval.event', function(e) {
              $.ajax({
      					type			: 'POST',
      					url				: '',
      					data			: { 'action' : 'approve', 'type' : 'event', 'eid' : e.target.id.split('_')[1] },
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

            // Whenever an event is rejected...
            $(document.body).on('click', '.rejection.event', function(e) {
              $.ajax({
      					type			: 'POST',
      					url				: '',
      					data			: { 'action' : 'reject', 'type' : 'event', 'eid' : e.target.id.split('_')[1] },
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

            // Whenever an rso is approved...
            $(document.body).on('click', '.approval.rso', function(e) {
              $.ajax({
      					type			: 'POST',
      					url				: '',
      					data			: { 'action' : 'approve', 'type' : 'rso', 'rso_name' : e.target.id.split('_')[1] },
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

            // Whenever an rso is rejected...
            $(document.body).on('click', '.rejection.rso', function(e) {
              $.ajax({
      					type			: 'POST',
      					url				: '',
      					data			: { 'action' : 'reject', 'type' : 'rso', 'rso_name' : e.target.id.split('_')[1] },
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
                      <h4>Pending Events</h4>
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

                      <h4>Pending RSOs</h4>
                      <table id="rso_approval_table">
                        <thead>
                          <tr>
                            <th>
                              Name
                            </th>
                            <th>
                              Creator
                            </th>
                            <th>
                              Contact Email
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
                        <li>Do we want to create RSO profile pages to link to?</li>
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
