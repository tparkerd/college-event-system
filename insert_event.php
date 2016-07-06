<?php session_start();?>
<?php
$error="";
$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
$name = $_POST['name'];
$date = $_POST['date'];
$time = $_POST['time'];
$location = $_POST['location'];
$category = $_POST['category'];
$privacy  = $_POST['privacy'];
$contact_phone = $_POST['contact_phone'];
$contact_email = $_POST['contact_email'];
$description = $_POST['description'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$sql="INSERT INTO e(eid, event_name, event_time, event_date, description, event_category, contact_email, contact_phone, rating, approved_by_admin, approved_by_superadmin) VALUES (default, :event_name, :event_time, :event_date, :description, :event_category, :contact_email, :contact_phone, :rating, :approved_by_admin, :approved_by_superadmin)";
$sth=$dbh->prepare($sql);
$sth->bindParam(':event_name', $name, PDO::PARAM_STR, 80);
$sth->bindValue(':event_time', $time);
$sth->bindValue(':event_date', $date);
$sth->bindValue(':rating', null, PDO::PARAM_INT);
$sth->bindValue(':approved_by_superadmin', null, PDO::PARAM_INT);
$sth->bindParam(':description', $description, PDO::PARAM_STR,500);
$sth->bindParam(':event_category', $category, PDO::PARAM_STR, 50);
$sth->bindParam(':contact_email', $category, PDO::PARAM_STR, 90);
$sth->bindParam(':contact_phone', $category, PDO::PARAM_STR, 13);
$sth->bindValue(':approved_by_admin', $_SESSION['id']);
$sth->execute() or die(print_r($sth->errorInfo(), true));


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
                        <header>
                            <h2>Event Guidelines</h2>
                        </header>
                        <p> <?php print $name."\n";
                            print $time."\n";
                            print $date."\n";
                            print $location."\n";
                            print $category."\n";
                            print $privacy."\n";
                            print $contact_phone."\n";
                            print $contact_email."\n";
                            print $description."\n";
                            print "latitude= ".$latitude."\n";
                            print "longitude= ".$longitude."\n";?>

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
