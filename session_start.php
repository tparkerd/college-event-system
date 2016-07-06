<?php
$host = 'sdickerson.ddns.net';
$port = '3306';
$db   = 'ces';
$user = 'root';
$pass = 'S#8roN*PJTMQWJ4m';
$charset = 'utf8';
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $user, $pass, $opt);


try {
    $dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=test', 'root', 'S#8roN*PJTMQWJ4m');
    $con = new PDO('mysql:host='.$host.';dbname='.$db.';port=3306', $user, $pass);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}

catch(PDOException $e)
{
}


session_start();
if(isset($_SESSION['id']) && $_SESSION['id'] != '')
{
  header('location: index.php');
} else {

  if (isset($_POST)) {

    try {
      $sql = "SELECT sid FROM student WHERE email = :email AND pword = :pword";
      $stmt = $pdo->prepare($sql);
      // Bind remaining values
      $stmt->bindParam(':email', $_POST["username"], PDO::PARAM_STR);
      $stmt->bindParam(':pword', $_POST["password"], PDO::PARAM_STR);


      // Execute Query
      $stmt->execute();

      $user_id = $stmt->fetchColumn();
      $data['email'] = $_POST["username"];
      $data['password'] = $_POST["password"];
      $data['query'] = "SELECT sid FROM student WHERE email = " . $data['email'] . " AND pword = " . $data['password'];
      $data['results'] = $user_id;
      if ($user_id == false)
      {
        $errors['message'] = "User not found.";
      }
      else {
        $_SESSION['id'] = $user_id;
      }
    } catch (Exception $e) {
      $errors['message'] = $e->getMessage();
    }

    if ( !empty($errors)) {
            // if there are items in our errors array, return those errors
            $data['success'] = false;
            $data['errors']  = $errors;
            $data['message'] = $errors['message'];
        } else {
            $_SESSION['id'] = $user_id;
        }
    }
}
header('location: index.php');
?>
