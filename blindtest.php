<?php
    if(session_status()==PHP_SESSION_NONE)session_start(); #Starts the session if not started

    $env = parse_ini_file('.env'); #Converts the .env config file to an array
    $access_mdp = $env['ACCESS_PASSWD']; #THE MDP REQUIRED TO ACCESS THE APP

    $previous_length = $env['PREVIOUS_LENGTH']; #The number of previous songs that are tracked
    
    #INFORMATIONS FOR THE MYSQL SERVER CONNECTION
    $dbms_mdp = $env['DBMS_PASSWD'];
    $dbms_user = $env['DBMS_USER'];
    $dbms_host = $env['DBMS_HOST'];

    #INFORMATIONS ABOUT THE DATABASE AND THE BLINDTEST TABLE
    $db_name = $env['DB_NAME'];
    $table_name = $env['TABLE_NAME'];

    #Defines entries of the session if not defined
    if(!isset($_SESSION['isConnected'])) $_SESSION['isConnected'] = false;
    if(!isset($_SESSION['yP'])) $_SESSION['yP'] = 0;
    if(!isset($_SESSION['rP'])) $_SESSION['rP'] = 0;
    if(!isset($_SESSION['gP'])) $_SESSION['gP'] = 0;
    if(!isset($_SESSION['bP'])) $_SESSION['bP'] = 0;

    if(isset($_POST['mdp']) and $_POST['mdp'] == $access_mdp){ #If the password entered is right
        $_SESSION['isConnected'] = true; #Then let the user access
    }
    if(isset($_POST['reset'])){ #If the reset button has been pressed
        unset($_SESSION['previousSongs']); #Undefines the list of the previous songs

        #Sets the score to 0
        $_SESSION['yP'] = 0;
        $_SESSION['rP'] = 0;
        $_SESSION['gP'] = 0;
        $_SESSION['bP'] = 0;
    }

    if(isset($_POST['pointchange'])){ #If a change appeared with the points
        switch($_POST['pointchange']){ #Checks what changed
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
    function getRandomSong(){ #Gets a random song that has not been chosen in the last $previous_length songs
        global $dbms_host, $dbms_user, $dbms_mdp, $db_name, $table_name, $previous_length; #Gets the value of the global variables

        $conn = new mysqli($dbms_host, $dbms_user, $dbms_mdp); #Connects to the MySQL server
        if($conn->connect_error){ #If there is a connection error
            die("Connection failed : ". $conn->connect_error); #Then print the error on the page
        }
        $conn->select_db($db_name); #Selects the blindtest database
        $req1 = "SELECT * FROM `$table_name` #Gets a random song from the song table
        ORDER BY RAND()
        LIMIT 1";
        $answer = $conn->query($req1)->fetch_assoc(); #Fetches the value returned
        if(isset($_SESSION['previousSongs']) and in_array($answer, $_SESSION['previousSongs'])){ #If the songs has been chosen in the last $previous_length songs
            while(in_array($answer, $_SESSION['previousSongs'])){ #Then while the chosen songs does
                $answer = $conn->query($req1)->fetch_assoc(); #Gets another random song
            }
        }
        if(!isset($_SESSION['previousSongs'])){ #If the previous songs array is not set
            $_SESSION['previousSongs'] = array($answer); #Set it to an array containing only the answer
            return $answer; #Return the answer
        }
        if(count($_SESSION['previousSongs']) >= $previous_length){ #If the length of the array is greater or equal to the max length
            array_shift($_SESSION['previousSongs']); #Remove the first songs of the array (the oldest)
        }
        array_push($_SESSION['previousSongs'], $answer); #Add the song to the end of the array
        return $answer; #Return the song
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>BlindMeter - Blind Test</title>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <link rel="stylesheet" href="style/blindtest.css">
        <?php
            if($_SESSION['isConnected']){ #If the user can access the page
                if(!isset($_POST['pointchange'])){ #If the player hasn't hit a button
                    $song = getRandomSong(); #Then select a random song
                    echo "<meta class='song' url='".$song['preview']."'>"; #Prints a meta component for javascript interpretation
                }else{ #Otherwise
                    $song = $_SESSION['previousSongs'][count($_SESSION['previousSongs']) - 1]; #Gets the last song
                    echo "<meta class='song' url='".$song['preview']."' grantingpoints='true'>"; #Prints a meta component with the last song chosen and the grantingpoints attribute for javascript interpretation
                }
            }
        ?>
    </head>
    <body>
        <?php
            if($_SESSION['isConnected']){ #If the user is connected
                include('rsc/btrsc.php'); #Then display the blindtest page
            }else{ #Otherwise
                include('rsc/connectionform.php'); #Display the connection form
            }
        ?>
    </body>
    <script src="script/blindtest.js"></script>
</html>