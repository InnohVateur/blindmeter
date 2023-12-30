<?php
    if(session_status()==PHP_SESSION_NONE)session_start();
    if(!isset($_SESSION['isRepeat_b'])) $_SESSION['isRepeat_b'] = false;
    if(!isset($_SESSION['offset'])) $_SESSION['offset'] = 0;
    if(!isset($_SESSION['isConnected'])) $_SESSION['isConnected'] = false;
    if(!isset($_SESSION['hasPrev'])) $_SESSION['hasPrev'] = false;
    if(!isset($_SESSION['hasNext'])) $_SESSION['hasNext'] = false;
    if(isset($_POST['mdp']) and $_POST['mdp'] == $mdp){
        $_SESSION['isConnected'] = true;
    }
    function querySong(string $q, int $r_number, int $index){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "api.deezer.com/search?index=" . $index . "&limit=" . $r_number . "&q=" . str_replace(" ", "+", $q));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        $s_el = curl_exec($curl);
        if(!$s_el) die("API REQUEST FAILED : ". curl_error($curl));
        curl_setopt($curl, CURLOPT_URL, "api.deezer.com/track/" . json_decode($s_el)->data[0]->id);
        $result = curl_exec($curl);
        if(!$result) die("API REQUEST FAILED : ". curl_error($curl));
        curl_close($curl);
        $_SESSION['hasPrev'] = isset(json_decode($s_el)->prev) ? true : false;
        $_SESSION['hasNext'] = isset(json_decode($s_el)->next) ? true : false;
        return $result;
    }
    function getSongByID($id){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "api.deezer.com/track/" . $id);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        $result = curl_exec($curl);
        if(!$result) die("API REQUEST FAILED : ". curl_error($curl));
        curl_close($curl);
        return $result;
    }
?>
<?php
    if(isset($_POST['verif'])){
        $connexion = new mysqli("localhost", "blindtest", "EliottLeo29170@..");
        if($connexion->connect_error){
            die("Connection failed : " . $connexion->connect_error);
        }
        $connexion->select_db("blindtest");
        if($_POST['verif'] == 'true'){
            $song = json_decode(querySong(iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $_POST['prompt']), 1, $_SESSION['offset']));
            $id = $song->id;
            $title = str_replace("'", "\'", $song->title_short);
            $preview = $song->preview;
            $picture = $song->album->cover_xl;
            $contributors = array();
            foreach($song->contributors as $contr){
                array_push($contributors, $contr->name);
            }
            $artists = str_replace("'", "\'", join("&|&", $contributors));
            $checkreq = "SELECT 1 FROM `songs` WHERE deez_id=$id";
            $result = $connexion->query($checkreq);
            if($result->num_rows == 0){
                $req = "INSERT INTO songs (deez_id, title, preview, picture, artists)
                VALUES ($id, '$title', '$preview', '$picture', '$artists')";
                if(!($connexion->query($req) === TRUE)){
                    die("Error: $req =>" . $connexion->error);
                }
            }else{
                $_SESSION['isRepeat_b'] = true;
            }
            $_SESSION['offset'] = 0;
            unset($_POST['prompt']);
        }else if($_POST['verif'] == 'false+') $_SESSION['offset']++;
        else if($_POST['verif'] == 'false-') $_SESSION['offset']--;
        unset($_POST['verif']);
        $connexion->close();
    }else{
        $_SESSION['offset'] = 0;
    }

    if(isset($_POST['resetdbsongs']) and $_POST['resetdbsongs'] == "TRUE"){
        $conn = new mysqli('localhost', 'blindtest', 'EliottLeo29170@..');
        if($conn->connect_error){
            die('Connection failed : '. $conn->connect_error);
        }
        $conn->select_db('blindtest');
        $req1 = "SELECT deez_id FROM songs";
        $result1 = $conn->query($req1)->fetch_all();
        $req2 = "TRUNCATE TABLE songs";
        $result2 = $conn->query($req2);
        if($result2){
            ini_set('max_execution_time', 0);
            foreach($result1 as $value){
                $val = $value[0];
                $track = json_decode(getSongByID($val));
                $id = $track->id;
                $title = str_replace("'", "\'", $track->title_short);
                $preview = $track->preview;
                $picture = $track->album->cover_xl;
                $contributors = array();
                foreach($track->contributors as $contr){
                    array_push($contributors, $contr->name);
                }
                $artists = str_replace("'", "\'", join("&|&", $contributors));
                $checkreq = "SELECT 1 FROM `songs` WHERE deez_id=$id";
                $checking = $conn->query($checkreq);
                if($checking->num_rows == 0){
                    $req3 = "INSERT INTO songs (deez_id, title, preview, picture, artists)
                    VALUE ($id, '$title', '$preview', '$picture', '$artists')";
                    if(!($conn->query($req3) === TRUE)){
                        die("Error: $req3 =>" . $conn->error);
                    }
                }
            }
            ini_set('max_execution_time', 120);
            $conn->close();
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>BlindMeter - Add a music to the Blind Test Database</title>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <link rel="stylesheet" href="style/addbtmusic.css">
    </head>
    <body>
        <?php
            if($_SESSION['isConnected']){
                include('rsc/addbtrsc.php');
            }else{
                include('rsc/connectionform.php');
            }
        ?>
    </body>
</html>