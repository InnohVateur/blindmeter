<?php
    if(session_status()==PHP_SESSION_NONE)session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>BlindMeter</title>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <link rel="stylesheet" href="style/index.css">
    </head>
    <body>
        <?php
                echo '<a href="blindtest.php">Blind test</a>';
                echo '<a href="fthesong.php">Find the lyrics</a>';
        ?>
    </body>
</html>