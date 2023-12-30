<header>
    <form action="" method="post" class="queryform">
        <input type="text" name="prompt">
    </form>
    <form action="" method="post">
        <input type="hidden" name="resetdbsongs" value="TRUE">
        <input type="submit" value="UPDATE">
    </form>
</header>
<section class="body">
    <?php
        if($_SESSION['isRepeat_b']) echo "<p style='color:red;'>ERROR : The music is already in the database !</p>\n"; unset($_SESSION['isRepeat_b']);
        if(isset($_POST["prompt"]) and $_POST['prompt'] != ""){
            $song = json_decode(querySong(iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $_POST['prompt']), 1, $_SESSION['offset']));
            $contributors = array();
            foreach($song->contributors as $contr){
                array_push($contributors, $contr->name);
            }

            if(isset($song)){
                echo "<p class='songid'>".$song->id."</p>";
                echo "<img src='".$song->album->cover_xl."'>";
                echo "<figure>\n\t<figcaption>" . $song->title_short . ", " . join(", ", $contributors) . "</figcation>\n\t";
                echo "<audio controls src='" . $song->preview . "'>\n\t\t<a href='" . $song->preview . "'>Download</a>\n\t</audio>";
                echo "\n</figure>\n";
                echo "<section class='verifbuttons'>";
                if($_SESSION['hasPrev']){echo '<form action="#" method="post" class="formdecr"><input type="hidden" name="verif" value="false-"><input type="hidden" name="prompt" value="'.$_POST['prompt'].'"><input type="submit" value="←"></form>';}
                else{echo '<form class="formdecr"><input type="submit" value="←" disabled="disabled"></form>';}
                echo '<form action="#" method="post" class="formverif"><input type="hidden" name="verif" value="true"><input type="hidden" name="prompt" value="'.$_POST['prompt'].'"><input type="submit" value="Add to Database"></form>';
                if($_SESSION['hasNext']){echo '<form action="#" method="post" class="formincr"><input type="hidden" name="verif" value="false+"><input type="hidden" name="prompt" value="'.$_POST['prompt'].'"><input type="submit" value="→"></form>';}
                else{echo '<form class="formincr"><input type="submit" value="→" disabled="disabled"></form>';}
                echo "</section>";
            } 
        }
    ?>
</section>