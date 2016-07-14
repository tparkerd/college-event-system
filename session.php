<?php
session_start();
$response = array();
$response['error'] = array();
$_SESSION['message'] = '';

// If the user is already logged in, log out
if (isset($_SESSION['id']) && $_SESSION['id'] != '') {
  $response['action'] = 'log out';
  session_destroy();

// Otherwise, attempt to log in
} else {
  $response['action'] = 'log in';

  try {
    $pdo = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch(PDOException $e) {
    }

  if (isset($_POST)) {
    try {
      $sql = "SELECT sid, given_name AS name FROM student WHERE email = :email AND pword = :pword";
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':email', $_POST["username"], PDO::PARAM_STR);
      $stmt->bindParam(':pword', $_POST["password"], PDO::PARAM_STR);
      $stmt->execute();
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($user == false) {
        $response['message'] = $response['error'] = "User not found.";
      }
      else
        $_SESSION['id'] = $user["sid"];
    } catch (Exception $e) {
      $response['error'] = $e->getMessage();
    }

    // If the user was found without throwing any errors, set their session id
    if (empty($response['error'])) {
      $_SESSION['id'] = $user["sid"];
      $response['user id'] = $_SESSION['id'];
      $response['message'] = "Welcome, " . $user["name"] . "!";
    } else {
      $_SESSION['message'] = 'User not found. Make sure you have entered the correct email and password.';
    }
  }
}
// Send info back to the user
// This is commented out because the log in is not an AJAX call
// echo json_encode($response);
header('location: index.php');
?>
