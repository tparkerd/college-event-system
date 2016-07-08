<?php
session_start();?>
<?php
$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
if (!empty($_POST['eid']))
    $event_id = strval($_POST['eid']);
$student_id = $_SESSION['id'];
$delete_comment_sql = "DELETE FROM comments WHERE comments.eid='".$event_id."' AND comments.sid='".$student_id."'";
$delete_comment_stmt = $dbh->prepare($delete_comment_sql);
$delete_comment_stmt->execute() or die(print_r($delete_comment_stmt->errorInfo(), true));
header('Location: '.$_SERVER['REQUEST_URI']);?>