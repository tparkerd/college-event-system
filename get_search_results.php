<?php start_session();?>
<?php
        //temporary just so html page doesnt error on empty values from field
        $keywords = $category = $public = $private = $rso = $start_date = $end_date = "";
        if(isset($_POST['keywords']))
            $keywords = strval($_POST['keywords']);
        if(isset($_POST['public']))
            $public = boolval($_POST['public']);
        if(isset($_POST['private']))
            $private = boolval($_POST['private']);
        if(isset($_POST['rso']))
            $rso = boolval($_POST['rso']);
        if(isset($_POST['category']))
            $category = strval($_POST['category']);
        if(isset($_POST['start_date']))
            $start_date = strval($_POST['start_date']);
        if(isset($_POST['end_date']))
            $end_date = strval($_POST['end_date']);
    ?>
?>

<!DOCTYPE html>
<html>
<head>
</head>
<body>

    <div class="container">
    <header>
        <h2>Search Results for <?php print $keywords?></h2>
    </header>
    <p> Public: <?php print $public?> <br>
        Private: <?php print $private?> <br>
        RSO: <?php print $rso?> <br>
        Category: <?php print $category?> <br>
        Start Date: <?php print $start_date?> <br>
        End Date: <?php print $end_date?>
    </p>
    </div>
</body>
</html>