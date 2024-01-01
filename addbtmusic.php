<?php
    if(session_status()==PHP_SESSION_NONE)session_start(); #Starts the session if not started

    $env = parse_ini_file('.env'); #Converts the .env config file to an array
    $access_psswd = $env['ACCESS_PASSWD']; #THE PSSWD REQUIRED TO ACCESS THE APP
    
    #INFORMATIONS FOR THE MYSQL SERVER CONNECTION
    $dbms_psswd = $env['DBMS_PASSWD'];
    $dbms_user = $env['DBMS_USER'];
    $dbms_host = $env['DBMS_HOST'];

    #INFORMATIONS ABOUT THE DATABASE AND THE BLINDTEST TABLE
    $db_name = $env['DB_NAME'];
    $table_name = $env['TABLE_NAME'];

    #Defines entries of the session if not defined
    if(!isset($_SESSION['isRepeat_b'])) $_SESSION['isRepeat_b'] = false;
    if(!isset($_SESSION['offset'])) $_SESSION['offset'] = 0;
    if(!isset($_SESSION['isConnected'])) $_SESSION['isConnected'] = false;
    if(!isset($_SESSION['hasPrev'])) $_SESSION['hasPrev'] = false;
    if(!isset($_SESSION['hasNext'])) $_SESSION['hasNext'] = false;

    if(isset($_POST['psswd']) and $_POST['psswd'] == $access_psswd){ #If the password entered is right
        $_SESSION['isConnected'] = true; #Then let the user access
    }
    function querySong(string $q, int $r_number, int $index){ #get a song with a query and an offset

        $curl = curl_init(); #Initializes the curl session

        #Sets the url to get the search object of the song
        curl_setopt($curl, CURLOPT_URL, "api.deezer.com/search?index=" . $index . "&limit=" . $r_number . "&q=" . str_replace(" ", "+", $q));

        #Sets the transfer return as a string of the return value when the GET request will be executed
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        #Sets the HTTP verification method to default
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        #Executes the GET request
        $s_el = curl_exec($curl);

        #Prints the error on the page if the request failed
        if(!$s_el) die("API REQUEST FAILED : ". curl_error($curl));

        #Sets the new url to get the track object of the song
        curl_setopt($curl, CURLOPT_URL, "api.deezer.com/track/" . json_decode($s_el)->data[0]->id);

        #Re-executes the GET request with the new url
        $result = curl_exec($curl);

        #Prints the error on the page if the request failed
        if(!$result) die("API REQUEST FAILED : ". curl_error($curl));

        #Closes the curl session
        curl_close($curl);

        #Checks if the search object has a previous or a next object
        $_SESSION['hasPrev'] = isset(json_decode($s_el)->prev) ? true : false;
        $_SESSION['hasNext'] = isset(json_decode($s_el)->next) ? true : false;

        #Returns the track object
        return $result;
    }
    function getSongByID($id){ #Gets a track object of a song, given an id
        $curl = curl_init(); #Initializes the curl session

        #Sets the url to get the track object of the song
        curl_setopt($curl, CURLOPT_URL, "api.deezer.com/track/" . $id);

        #Sets the transfer return as a stringof the return value when the GET request will be executed
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);

        #Sets the HTTP verification method to default
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        #Executes the GET request
        $result = curl_exec($curl);

        #Prints the error on the page if the request failed
        if(!$result) die("API REQUEST FAILED : ". curl_error($curl));

        #Closes the curl session
        curl_close($curl);

        #Returns the track object
        return $result;
    }
?>
<?php
    if(isset($_POST['verif'])){ #If the user entered a button
        #Connects to the MySQL server
        $connection = new mysqli($dbms_host, $dbms_user, $dbms_psswd);

        if($connection->connect_error){ #If there is a connection error
            die("Connection failed : " . $connection->connect_error); #Then display it on the page
        }
        $connection->select_db($db_name); #Selects the blindtest database
        if($_POST['verif'] == 'true'){ #If the user entered the "add to database" button
            $song = json_decode(querySong(iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $_POST['prompt']), 1, $_SESSION['offset'])); #Decodes the song to a php array

            #Gets the field variables
            $id = $song->id;
            $title = str_replace("'", "\'", $song->title_short);
            $preview = $song->preview;
            $picture = $song->album->cover_xl;
            $contributors = array(); #Initializes an array to add the contributors

            #Adds each contributor the the array
            foreach($song->contributors as $contr){
                array_push($contributors, $contr->name);
            }

            $artists = str_replace("'", "\'", join("&|&", $contributors)); #Joins the contributors in a string
            $checkreq = "SELECT 1 FROM `$table_name` WHERE deez_id=$id"; #Makes a request to check if the song is already on the table
            $result = $connection->query($checkreq); #Get the result
            if($result->num_rows == 0){ #If the song isn't on the table

                #Adds it on the table
                $req = "INSERT INTO $table_name (deez_id, title, preview, picture, artists)
                VALUES ($id, '$title', '$preview', '$picture', '$artists')";

                if(!($connection->query($req) === TRUE)){ #If the request fails
                    die("Error: $req =>" . $connection->error); #Then print the error on the page
                }
            }else{ #Otherwise
                $_SESSION['isRepeat_b'] = true; #Sets the repeat state to true (to display the repeat message)
            }
            $_SESSION['offset'] = 0; #Sets the offset to 0
            unset($_POST['prompt']); #Unsets the prompt entered by the user in the search bar
        }else if($_POST['verif'] == 'false+') $_SESSION['offset']++; #If the user hit the next button then increments the offset
        else if($_POST['verif'] == 'false-') $_SESSION['offset']--; #If the user hit the previous button then decrements the offset
        unset($_POST['verif']); #Deletest the verif variable
        $connection->close(); #Closes the connection
    }else { #If the user didn't hit a button
        $_SESSION['offset'] = 0; #Then set the offset to 0
    }

    if(isset($_POST['resetdbsongs']) and $_POST['resetdbsongs'] == "TRUE"){ #If the user hit the button to reload the whole database
        $conn = new mysqli($dbms_host, $dbms_user, $dbms_psswd); #Connects to the MySQL server
        if($conn->connect_error){ #If there is a connection error
            die('Connection failed : '. $conn->connect_error); #Then display the error on the page
        }
        $conn->select_db($db_name); #Selects the blindtest database
        $req1 = "SELECT deez_id FROM $table_name"; #The request to get all the songs' ids
        $result1 = $conn->query($req1)->fetch_all(); #Fetches all the datas
        $req2 = "TRUNCATE TABLE $table_name"; #Empties the table
        $result2 = $conn->query($req2); #Executes it
        if($result2){ #It the table is empty
            ini_set('max_execution_time', 0); #Temporarily removes the max execution time
            foreach($result1 as $value){ #For each song in the song list
                $val = $value[0]; #Gets the first value (they are one_lengthed arrays)
                $track = json_decode(getSongByID($val)); #Decodes it into a song

                #Gets all the fields to re-add the song
                $id = $track->id;
                $title = str_replace("'", "\'", $track->title_short);
                $preview = $track->preview;
                $picture = $track->album->cover_xl;
                $contributors = array(); #Initializes an array to add the contributors

                #Adds each contributor to the array
                foreach($track->contributors as $contr){
                    array_push($contributors, $contr->name);
                }

                $artists = str_replace("'", "\'", join("&|&", $contributors)); #Joins the contributors in a string
                $checkreq = "SELECT 1 FROM `$table_name` WHERE deez_id=$id"; #Makes a check request
                $checking = $conn->query($checkreq); #Checks
                if($checking->num_rows == 0){ #If the song isn't a repeat
                    $req3 = "INSERT INTO $table_name (deez_id, title, preview, picture, artists) #The request to insert it
                    VALUE ($id, '$title', '$preview', '$picture', '$artists')";
                    if(!($conn->query($req3) === TRUE)){ #If the request failed
                        die("Error: $req3 =>" . $conn->error); #Then print the error on the page
                    }
                }
            }
            ini_set('max_execution_time', 120); #Resets the max execution time to 2 minutes (the default value)
            $conn->close(); #Closes the connection
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>BlindMeter - Add a music to the Blind Test Database</title>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <link rel="stylesheet" href="style/addbtmusic.css">
        <link rel="shortcut icon" href="rsc/favicon.png" type="image/png">
    </head>
    <body>
        <?php
            if($_SESSION['isConnected']){ #If the user can access the page
                include('rsc/addbtrsc.php'); #Then print it
            }else{ #Otherwise
                include('rsc/connectionform.php'); #Print the connection form
            }
        ?>
    </body>
</html>