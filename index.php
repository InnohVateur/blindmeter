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
                //echo "<form method='post' action='index.php'><label>Mot de passe : </label><input type='password' name='passw'><input type='submit' value='Accéder au site'></form>";
                echo '<a href="blindtest.php">Blind tests</a>';
                echo '<a href="fthesong.php">Termine les paroles</a>';
        ?>
    </body>
</html>