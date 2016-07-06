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
    echo "Connected successfully<br>";
}

catch(PDOException $e)
{
    echo "Connection failed." . $e->getMessage();
}


session_start();
if(isset($_SESSION['id']) && $_SESSION['id'] != '')
{
  echo  $_SESSION['id'] . ' is already logged in<br>';
} else {

  echo 'Looking for POST variables<br>';
  if (isset($_POST)) {

    var_dump($_POST);
    try {
      $sql = "SELECT sid FROM student WHERE email = :email AND pword = :pword";
      $stmt = $pdo->prepare($sql);
      // Bind remaining values
      $stmt->bindParam(':email', $_POST["username"], PDO::PARAM_STR);
      $stmt->bindParam(':pword', $_POST["password"], PDO::PARAM_STR);

      echo 'Found them:<br>';
      echo $_POST["username"] . "<br>";
      echo $_POST["password"] . "<br>";

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

            // TODO(timp): Replace this with a general error message so that the user doesn't see what went wront with the database
            $data['message'] = $errors['message'];
        } else {

            // if there are no errors process our form, then return a message

            // DO ALL YOUR FORM PROCESSING HERE
            // THIS CAN BE WHATEVER YOU WANT TO DO (LOGIN, SAVE, UPDATE, WHATEVER)

            // show a message of success and provide a true success variable
            // $data['success'] = true;
            // $data['message'] = 'Successfully logged in!';
            // $data['$_POST'] = $_POST;
            // $data['$_SESSION'] = $_SESSION;
            // $data['current_user_id'] = $_SESSION['id'];
            $_SESSION['id'] = $user_id;
            echo $_SESSION['id'] . ' has logged in.';
        }

      // Return messages to the caller
      // echo json_encode($data);
    }
}
?>
