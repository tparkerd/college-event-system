<!DOCTYPE html>
<html>
<head>
</head>
<body>

<?php
$q = strval($_GET['q']);
$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
$sql="SELECT * FROM university WHERE university_name='".$q."'";
$sth=$dbh->prepare($sql);
// $sql="SELECT * FROM university WHERE university_name = :name AND university_name IN (SELECT university_name FROM university_approved_by)";
// $sth->bindParam(':name', $q, PDO::PARAM_STR);
foreach ($dbh->query($sql) as $row) {
    $uni_name = $row['university_name'];
    $address= $row['address'];
    $img_url_1 = $row['picture_one'];
    $img_url_2 = $row['picture_two'];
    $description = $row['description'];
    $num_students = $row['num_students'];
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
                        <p> There are <?php print $num_students?> registered students for <?php print $uni_name?>.</p>
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
</div>

</body>
</html>
