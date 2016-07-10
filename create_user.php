<?php
    $error="";
    $dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $sid = $_POST['sid'];
    $sql="INSERT INTO student VALUES (:sid, :fname, :lname, :email, :pword, :university)";
    $sth=$dbh->prepare($sql);
    if(!empty($_POST['university'])) {
        $university = $_POST['university'];
        $sth->bindParam(':university', $university, PDO::PARAM_STR, 100);
    }
    else {
        $sth->bindValue(':university', null, PDO::PARAM_INT);
    }
    $sth->bindParam(':sid', $sid, PDO::PARAM_STR, 8);
    $sth->bindParam(':fname', $fname, PDO::PARAM_STR, 35);
    $sth->bindParam(':lname', $lname, PDO::PARAM_STR, 35);
    $sth->bindParam(':email', $email, PDO::PARAM_STR, 90);
    $sth->bindParam(':pword', $password, PDO::PARAM_STR, 100);

    try{
        $sth->execute();
    }
    catch (PDOException $e) {
        $error = $e;
    }
    $success=true;
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
                            <h2 class="centered">Register Today</h2>
                        </header>


                        <form action="create_user.php" class="pure-form centered" id="register">
                            <fieldset>
                                <legend>Register with us today to gain access to more events!</legend>
                                <br><input type="text" name="fname" id="txt_fname" style="width:25%;" placeholder="First Name" required>
                                <br><br>
                                <input type="text" name="lname" id="txt_lname" style="width:25%;" placeholder="Last Name" required>
                                <br><br>
                                <input type="text" name="sid" id="txt_sid" style="width:25%;" placeholder="Student ID" required>
                                <br><br>
                                <input type="email" name="email" id="txt_umail" style="width:25%;" placeholder="Email" required>
                                <br><br>
                                <input type="password" name="password" id="txt_upass" style="width:25%;" placeholder="Password" required>
                                <br><br>
                                <select style="width:25%;padding-bottom:5px;" name="university" id="txt_uname" placeholder="University">
                                    <?php
                                    $dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
                                    $sql ='SELECT university_name from university';
                                    $stmt = $dbh->prepare($sql);
                                    $stmt->execute();
                                    $unames=$stmt->fetchAll();
                                    ?>
                                        <?php foreach($unames as $uname):?>
                                            <option value="<?php print $uname['university_name']?>"><?php print $uname['university_name']; ?></option>
                                        <?php endforeach; ?>
                                </select>
                                <br>
                                <button name="submit" type="submit" class="small-button">Submit</button>
                                <br><br>
                                <?php
                                    if($success=true)
                                        print "Your registration was successful!";
                                    else {
                                        print "Sorry, there was an error with your registration.";
                                        echo "<br>";
                                        print "Make sure you are using the proper Student ID and try again.";
                                        echo "<br>";
                                    }
                                ?>
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