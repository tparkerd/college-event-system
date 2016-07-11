<?php session_start();?>
<?php
$error="";
$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
$name = $_POST['name'];
$date = $_POST['date'];
$event_start_time = $_POST['start_time'];
$event_end_time = $_POST['end_time'];
$location = $_POST['location'];
$category = $_POST['category'];
$privacy  = $_POST['privacy'];
$contact_phone = $_POST['contact_phone'];
$contact_email = $_POST['contact_email'];
$description = $_POST['description'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
if(!empty($_POST['rso_name']))
    $rso_name = $_POST['rso_name'];

$sql="INSERT INTO e(eid, event_name, event_start_time, event_end_time, event_date, description, event_category, contact_email, contact_phone, rating, approved_by_admin, approved_by_superadmin) VALUES (default, :event_name, :event_start_time, :event_end_time, :event_date, :description, :event_category, :contact_email, :contact_phone, :rating, :approved_by_admin, :approved_by_superadmin)";
$sth=$dbh->prepare($sql);
$sth->bindParam(':event_name', $name, PDO::PARAM_STR, 80);
$sth->bindValue(':event_start_time', $event_start_time);
$sth->bindValue(':event_end_time', $event_end_time);
$sth->bindValue(':event_date', $date);
$sth->bindValue(':rating', null, PDO::PARAM_INT);
$sth->bindValue(':approved_by_superadmin', null, PDO::PARAM_INT);
$sth->bindParam(':description', $description, PDO::PARAM_STR,500);
$sth->bindParam(':event_category', $category, PDO::PARAM_STR, 50);
$sth->bindParam(':contact_email', $contact_email, PDO::PARAM_STR, 90);
$sth->bindParam(':contact_phone', $contact_phone, PDO::PARAM_STR, 13);
$sth->bindValue(':approved_by_admin', $_SESSION['id']);


$stmt = $dbh->prepare("SELECT * FROM location WHERE location_name='".$location."'");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$row)
{
    $sql_location = "INSERT INTO location(location_name, latitude, longitude) VALUES (:location, :latitude, :longitude)";
    $prep_location = $dbh->prepare($sql_location);
    $prep_location->bindParam(':location', $location, PDO::PARAM_STR, 200);
    $prep_location->bindParam(':latitude', $latitude, PDO::PARAM_STR);
    $prep_location->bindParam(':longitude', $longitude, PDO::PARAM_STR);
    $prep_location->execute() or die(print_r($prep_location->errorInfo(), true));
}


$sql_check_overlap = "SELECT DISTINCT e.eid FROM e, at WHERE e.event_start_time <= '".$event_end_time."' AND e.event_end_time >= '".$event_start_time."' AND e.event_date = '".$date."' AND at.location_name='".$location."'";
$check_overlap = $dbh->prepare($sql_check_overlap);
$check_overlap->execute();
$overlap_result= $check_overlap->fetch(PDO::FETCH_ASSOC);


if($overlap_result){
    $error = "There is already an event scheduled at this location overlapping with the times you chose.";
}

else{
    $sth->execute() or die(print_r($sth->errorInfo(), true));
    $eid_result = $dbh->lastInsertId('eid');
    $sql_at = "INSERT INTO at(eid, location_name) VALUES (:eid, :location)";
    $at = $dbh->prepare($sql_at);
    $at->bindParam(':eid', $eid_result, PDO::PARAM_INT);
    $at->bindParam(':location', $location, PDO::PARAM_STR,200);
    $at->execute() or die(print_r($at->errorInfo(), true));


    if($privacy == 'public'){
        $sql2="INSERT INTO public_event(eid, event_name, event_start_time,event_end_time, event_date, description, event_category, contact_email, contact_phone, rating, approved_by_admin, approved_by_superadmin) VALUES (:eid, :event_name, :event_start_time, :event_end_time,:event_date, :description, :event_category, :contact_email, :contact_phone, :rating, :approved_by_admin, :approved_by_superadmin)";
    }
    else if($privacy == 'private'){
        $sql2="INSERT INTO private_event(eid, event_name, event_start_time, event_end_time, event_date, description, event_category, contact_email, contact_phone, rating, approved_by_admin, approved_by_superadmin) VALUES (:eid, :event_name, :event_start_time, :event_end_time, :event_date, :description, :event_category, :contact_email, :contact_phone, :rating, :approved_by_admin, :approved_by_superadmin)";
    }
    else if($privacy == 'RSO'){
        $sql2="INSERT INTO rso_event(eid, event_name, event_start_time, event_end_time, event_date, description, event_category, contact_email, contact_phone, rating, approved_by_admin, approved_by_superadmin) VALUES (:eid, :event_name, :event_start_time, :event_end_time, :event_date, :description, :event_category, :contact_email, :contact_phone, :rating, :approved_by_admin, :approved_by_superadmin)";
        $sth2=$dbh->prepare($sql2);
        $sth2->bindValue(':eid', $eid_result);
        $sth2->bindParam(':event_name', $name, PDO::PARAM_STR, 80);
        $sth2->bindValue(':event_start_time', $event_start_time);
        $sth2->bindValue(':event_end_time', $event_start_time);
        $sth2->bindValue(':event_date', $date);
        $sth2->bindValue(':rating', null, PDO::PARAM_INT);
        $sth2->bindValue(':approved_by_superadmin', null, PDO::PARAM_INT);
        $sth2->bindParam(':description', $description, PDO::PARAM_STR,500);
        $sth2->bindParam(':event_category', $category, PDO::PARAM_STR, 50);
        $sth2->bindParam(':contact_email', $contact_email, PDO::PARAM_STR, 90);
        $sth2->bindParam(':contact_phone', $contact_phone, PDO::PARAM_STR, 13);
        $sth2->bindValue(':approved_by_admin', $_SESSION['id']);
        $sth2->execute() or die(print_r($sth2->errorInfo(), true));
        $sql_owns_event = "INSERT INTO owns_event(rso_eid, rso_name) VALUES (:eid, :rso_name)";
        $owns_event = $dbh->prepare($sql_owns_event);
        $owns_event->bindParam(':eid', $eid_result);
        $owns_event->bindParam(':rso_name', $rso_name);
        $owns_event->execute() or die(print_r($owns_event->errorInfo(), true));

    }

    if($privacy != 'RSO') {
        $sth2 = $dbh->prepare($sql2);
        $sth2->bindValue(':eid', $eid_result);
        $sth2->bindParam(':event_name', $name, PDO::PARAM_STR, 80);
        $sth2->bindValue(':event_start_time', $event_start_time);
        $sth2->bindValue(':event_end_time', $event_start_time);
        $sth2->bindValue(':event_date', $date);
        $sth2->bindValue(':rating', null, PDO::PARAM_INT);
        $sth2->bindValue(':approved_by_superadmin', null, PDO::PARAM_INT);
        $sth2->bindParam(':description', $description, PDO::PARAM_STR, 500);
        $sth2->bindParam(':event_category', $category, PDO::PARAM_STR, 50);
        $sth2->bindParam(':contact_email', $contact_email, PDO::PARAM_STR, 90);
        $sth2->bindParam(':contact_phone', $contact_phone, PDO::PARAM_STR, 13);
        $sth2->bindValue(':approved_by_admin', $_SESSION['id']);
        $sth2->execute() or die(print_r($sth2->errorInfo(), true));
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
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        #map {
            height: 360px;
            width:700px;
        }
        .controls {
            margin-top: 10px;
            border: 1px solid transparent;
            border-radius: 2px 0 0 2px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            height: 32px;
            outline: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        #pac-input {
            background-color: #fff;
            font-family: Roboto;
            font-size: 15px;
            font-weight: 300;
            margin-left: 12px;
            padding: 0 11px 0 13px;
            text-overflow: ellipsis;
            width: 300px;
        }

        #pac-input:focus {
            border-color: #4d90fe;
        }

        .pac-container {
            font-family: Roboto;
        }

        #type-selector {
            color: #fff;
            background-color: #4d90fe;
            padding: 5px 11px 0px 11px;
        }

        #type-selector label {
            font-family: Roboto;
            font-size: 13px;
            font-weight: 300;
        }
        #target {
            width: 345px;
        }
    </style>


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
                    <section id="content">

                        <br><br>
                        <?php
                        if($error != ''){
                            echo "<header><h2>Create Event Request Failed</h2></header><p>";
                            print $error;
                            echo "<br><br><a href='create_event.php' style='color:black;'>Click here to go back to event creation page</a></p>";
                        }
                        else{
                            echo "<header><h2>Request Successfully Submitted!</h2></header><p>";
                            print "Your request to create this event has been submitted. It must be approved before appearing on the website.";
                            echo "</p>";
                        }?>
                        <br>

                        </p>
                    </section>
                </div>
                <div class="3u">
                    <section id="sidebar2">
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
