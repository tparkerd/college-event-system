<?php session_start();?>
<?php
$event_id=intval($_GET['eid']);
$comment_text=strval($_GET['text']);
date_default_timezone_set('America/New_York');
$dbh = new PDO('mysql:host=sdickerson.ddns.net;port=3306;dbname=ces', 'root', 'S#8roN*PJTMQWJ4m');
$sql="SELECT * FROM comments WHERE eid='".$event_id."' AND sid='".$_SESSION['id']."'";
$sth=$dbh->prepare($sql);
foreach ($dbh->query($sql) as $row) {
    $text = $row['text'];
    $rating = $row['rating'];
}
$dbh=null;
?>

<script>
    function saveComment() {
        $.ajax({
            url: 'update_comment.php',
            type: 'post', // performing a POST request
            data : {
                eid : '<?php print $event_id?>', // will be accessible in $_POST['data1']
                comment_text: document.getElementById("comment_text").value,
                rating: document.getElementById("rating").value

            },
            dataType: 'json',
            success: function(data)
            {
                // etc...
            }
        });
    }
</script>

<form class="pure-form" action="update_comment.php" method="POST">
    <fieldset>
        <legend>Edit your comment</legend>
        <br>
        <div id="rating" name="rating" value="<?php print $rating?>">
            Rating:
            <label style="display:inline;margin-right:10px;margin-left:10px;" for="one_star" class="pure-radio">
                <input id="one_star" type="radio" name="rating" value="1" checked>
                1
            </label>
            <label style="display:inline;margin-right:10px;" for="two_star" class="pure-radio">
                <input id="two_star" type="radio" name="rating" value="2" checked>
                2
            </label>
            <label style="display:inline;margin-right:10px;" for="three_star" class="pure-radio label-inline">
                <input id="three_star" type="radio" name="rating" value="3" checked>
                3
            </label>
            <label style="display:inline;margin-right:10px;" for="four_star" class="pure-radio">
                <input id="four_star" type="radio" name="rating" value="4" checked>
                4
            </label>
            <label style="display:inline;margin-right:10px;" for="five_star" class="pure-radio">
                <input id="five_star" type="radio" name="rating" value="5" checked>
                5
            </label>
        </div>
        <br><br>
        <input type="hidden" id="eid" name="eid" value="<?php print $event_id?>">
        <textarea rows="8" cols="50" name="comment_text" id="comment_text"><?php print $comment_text ?></textarea><br>
        <button type="submit" id="update_comment" name="update_comment" class="small-button">Save</button;
            <?php if(isset($_POST['update_comment'])) print "update comment sql: ".$update_comment_sql;?>
    </fieldset>
</form>