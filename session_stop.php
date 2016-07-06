<?php

  session_start();
  if (isset($_SESSION['id']) && $_SESSION['id'] != '') {
    var_dump($_POST);
    var_dump($_SESSION);
    echo $_SESSION['id'] . ' has logged out';
    session_destroy();
    // header('location: index.php');
  } else {
    echo 'no user to log out';
  }
 ?>
