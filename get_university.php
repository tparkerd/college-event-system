<!DOCTYPE html>
<html>
<head>
</head>
<body>

<?php
$q = strval($_GET['q']);

$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=test', 'root', 'S#8roN*PJTMQWJ4m');
$sql="SELECT * FROM Universities WHERE name='".$q."'";
$sth=$dbh->prepare($sql);
$dbh->query($sth);
foreach ($dbh->query($sql) as $row) {
    $uni_name = $row['name'];
	$address= $row['address'];
	$img_url_1 = $row['imageURL1'];
	$img_url_2 .$row['imageURL2'];
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


</body>
</html>
