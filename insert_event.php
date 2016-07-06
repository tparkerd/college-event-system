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

try{
    $sth->execute();
}
catch (PDOException $e) {
    $error = $e;
}
$success=true;
?>