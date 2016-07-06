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

print $name."\n";
print $time."\n";
print $date."\n";
print $location."\n";
print $category."\n";
print $privacy."\n";
print $contact_phone."\n";
print $contact_email."\n";
print $description."\n";
print "latitude= ".$latitude."\n";
print "longitude= ".$longitude."\n";
/*$sql="INSERT INTO student VALUES (:sid, :fname, :lname, :email, :pword, :university)";
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

try{
    $sth->execute();
}
catch (PDOException $e) {
    $error = $e;
}
$success=true;*/
?>