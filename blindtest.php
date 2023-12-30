<?php
    if(session_status()==PHP_SESSION_NONE)session_start(); #Starts the session if not started
    $mdp = "Eliott&Phoebe@.."; #The mdp to access blindmeter
    if(!isset($_SESSION['isConnected'])) $_SESSION['isConnected'] = false; #
    if(!isset($_SESSION['yP'])) $_SESSION['yP'] = 0;
    if(!isset($_SESSION['rP'])) $_SESSION['rP'] = 0;
    if(!isset($_SESSION['gP'])) $_SESSION['gP'] = 0;
    if(!isset($_SESSION['bP'])) $_SESSION['bP'] = 0;

    if(isset($_POST['mdp']) and $_POST['mdp'] == $mdp){
        $_SESSION['isConnected'] = true;
    }
    if(isset($_POST['reset'])){
        unset($_SESSION['previousSongs']);
        $_SESSION['yP'] = 0;
        $_SESSION['rP'] = 0;
        $_SESSION['gP'] = 0;
        $_SESSION['bP'] = 0;
    }
    if(isset($_POST['win'])){
        switch($_POST['win']){
            case 'y1': $_SESSION['yP']++; break;
            case 'y2': $_SESSION['yP']+=2; break;
            case 'y-': if($_SESSION['yP']>0) $_SESSION['yP']--; break;
            case 'yr': $_SESSION['yP'] = 0; break;
            case 'r1': $_SESSION['rP']++; break;
            case 'r2': $_SESSION['rP']+=2; break;
            case 'r-': if($_SESSION['rP']>0) $_SESSION['rP']--; break;
            case 'rr': $_SESSION['rP'] = 0; break;
            case 'g1': $_SESSION['gP']++; break;
            case 'g2': $_SESSION['gP']+=2; break;
            case 'g-': if($_SESSION['gP']>0) $_SESSION['gP']--; break;
            case 'gr': $_SESSION['gP'] = 0; break;
            case 'b1': $_SESSION['bP']++; break;
            case 'b2': $_SESSION['bP']+=2; break;
            case 'b-': if($_SESSION['bP']>0) $_SESSION['bP']--; break;
            case 'br': $_SESSION['bP'] = 0; break;
        }
    }
    function getRandomSong(){
        $conn = new mysqli("localhost","blindtest","EliottLeo29170@..");
        if($conn->connect_error){
            die("Connection failed : ". $conn->connect_error);
        }
        $conn->select_db("blindtest");
        $req1 = "SELECT * FROM `songs`
        ORDER BY RAND()
        LIMIT 1";
        $answer = $conn->query($req1)->fetch_assoc();
        if(isset($_SESSION['previousSongs']) and in_array($answer, $_SESSION['previousSongs'])){
            while(in_array($answer, $_SESSION['previousSongs'])){
                $answer = $conn->query($req1)->fetch_assoc();
            }
        }
        if(!isset($_SESSION['previousSongs'])){
            $_SESSION['previousSongs'] = array($answer);
            return $answer;
        }
        if(count($_SESSION['previousSongs']) >= 150){
            array_shift($_SESSION['previousSongs']);
        }
        array_push($_SESSION['previousSongs'], $answer);
        return $answer;
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>BlindMeter - Blind Test</title>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <link rel="stylesheet" href="style/blindtest.css">
        <?php
            if($_SESSION['isConnected']){
                if(!isset($_POST['win'])){
                    $song = getRandomSong();
                    echo "<meta class='song' url='".$song['preview']."'>";
                }else{
                    $song = $_SESSION['previousSongs'][count($_SESSION['previousSongs']) - 1];
                    echo "<meta class='song' url='".$song['preview']."' grantingpoints='true'>";
                }
            }
        ?>
    </head>
    <body>
        <?php
            if($_SESSION['isConnected']){
                include('rsc/btrsc.php');
            }else{
                include('rsc/connectionform.php');
            }
        ?>
    </body>
    <script src="script/blindtest.js"></script>
</html>