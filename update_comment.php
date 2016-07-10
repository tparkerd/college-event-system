<?php
session_start();?>
<?php
$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
if (!empty($_POST['comment_text']))
    $text = strval($_POST['comment_text']);
if (!empty($_POST['rating']))
    $rating = intval($_POST['rating']);
if (!empty($_POST['eid']))
    $event_id = strval($_POST['eid']);
header('Location: event_profile.php?eid='.$event_id);
$student_id = $_SESSION['id'];
$update_comment_sql = "UPDATE comments SET comments.rating='".$rating."',comments.text='".$text."', comments.ctimestamp=default WHERE comments.eid='".$event_id."' AND comments.sid='".$student_id."'";
$update_comment_stmt = $dbh->prepare($update_comment_sql);
$update_comment_stmt->execute() or die(print_r($update_comment_stmt->errorInfo(), true));

?>