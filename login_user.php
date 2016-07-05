<?php
/**
 * Created by PhpStorm.
 * User: moriah
 * Date: 7/5/16
 * Time: 7:06 PM
 */
$username = $password = "null";
if (isset($_POST['email']) && isset($_POST['password'])){
    $username = $_POST['email'];
    $password = $_POST['password'];

    echo "Username is :" . $username . "<br>";
    echo "Password is :" . $password;
}
print "Welcome, ".$username;
print $password;
var_dump($_GET);
var_dump($_POST);
var_dump($_REQUEST);
?>