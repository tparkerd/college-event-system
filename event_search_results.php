<!DOCTYPE html>
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
    <?php
        //temporary just so html page doesnt error on empty values from field
        $keywords = "";
        $category ="";
        $public = "";
        $private = "";
        $rso = "";
        $start_date = "";
        $end_date = "";
        if(isset($_GET['keywords']))
            $keywords = strval($_GET['keywords']);
        if(isset($_GET['public']))
           $public = boolval($_GET['public']);
        if(isset($_GET['private']))
            $private = boolval($_GET['private']);
        if(isset($_GET['rso']))
            $rso = boolval($_GET['rso']);
        if(isset($_GET['category']))
            $category = strval($_GET['category']);
        if(isset($_GET['start_date']))
            $start_date = strval($_GET['start_date']);
        if(isset($_GET['end_date']))
            $end_date = strval($_GET['end_date']);
    ?>
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
                            </p>
                        </div>
                    </section>
                </div>
                <div class="3u">
                    <section id="sidebar2">
                        <header style="text-align:center;">
                            <h2 class="centered">Search Events</h2>
                        </header>

                        <form class="pure-form centered" action="event_search_results.php">
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
                                <button type="submit" class="small-button">Search</button>
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