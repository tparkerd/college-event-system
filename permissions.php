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
                    <section id="content" >
                        <header>
                            <h2>INVALID PERMISSIONS</h2><br><br>
                            <p> You have tried to view a page which you currently do not have permissions to see.</p>
                        </header>
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
