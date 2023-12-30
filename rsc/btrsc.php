<aside class="points">
    <section class="headerP">
        <form action="" method="post" class="reset">
            <input type="hidden" name="reset" value="true">
            <input type="submit" value="Réinitialiser">
        </form>
        <a href="addbtmusic.php" target="_blank">
            <section>
                <p>+</p>
            </section>
        </a>
    </section>
    <section class="teamS">
        <p>Équipe Jaune : <?= $_SESSION['yP']." point".($_SESSION['yP']>1||$_SESSION['yP']==0?"s":"")?></p>
        <section>
            <section>
                <form action="" method="post" class="yF">
                    <input type="hidden" name="pointchange" value="y1">
                    <input type="submit" value="+1">
                </form>
                <form action="" method="post" class="yF">
                    <input type="hidden" name="pointchange" value="y2">
                    <input type="submit" value="+2">
                </form>
            </section>
            <section>
                <form action="" method="post" class="yF">
                    <input type="hidden" name="pointchange" value="y-">
                    <input type="submit" value="-1">
                </form>
                <form action="" method="post" class="yF">
                    <input type="hidden" name="pointchange" value="yr">
                    <input type="submit" value="∅">
                </form>
            </section>
        </section>
    </section>
    <section class="teamS">
        <p>Équipe Rouge : <?= $_SESSION['rP']." point".($_SESSION['rP']>1||$_SESSION['rP']==0?"s":"")?></p>
        <section>
            <section>
                <form action="" method="post" class="rF">
                    <input type="hidden" name="pointchange" value="r1">
                    <input type="submit" value="+1">
                </form>
                <form action="" method="post" class="rF">
                    <input type="hidden" name="pointchange" value="r2">
                    <input type="submit" value="+2">
                </form>
            </section>
            <section>
                <form action="" method="post" class="rF">
                    <input type="hidden" name="pointchange" value="r-">
                    <input type="submit" value="-1">
                </form>
                <form action="" method="post" class="rF">
                    <input type="hidden" name="pointchange" value="rr">
                    <input type="submit" value="∅">
                </form>
            </section>
        </section>
    </section>
    <section class="teamS">
        <p>Équipe Verte : <?= $_SESSION['gP']." point".($_SESSION['gP']>1||$_SESSION['gP']==0?"s":"")?></p>
        <section>
            <section>
                <form action="" method="post" class="gF">
                    <input type="hidden" name="pointchange" value="g1">
                    <input type="submit" value="+1">
                </form>
                <form action="" method="post" class="gF">
                    <input type="hidden" name="pointchange" value="g2">
                    <input type="submit" value="+2">
                </form>
            </section>
            <section>
                <form action="" method="post" class="gF">
                    <input type="hidden" name="pointchange" value="g-">
                    <input type="submit" value="-1">
                </form>
                <form action="" method="post" class="gF">
                    <input type="hidden" name="pointchange" value="gr">
                    <input type="submit" value="∅">
                </form>
            </section>
        </section>
    </section>
    <section class="teamS">
        <p>Équipe Bleue : <?= $_SESSION['bP']." point".($_SESSION['bP']>1||$_SESSION['bP']==0?"s":"")?></p>
        <section>
            <section>
                <form action="" method="post" class="bF">
                    <input type="hidden" name="pointchange" value="b1">
                    <input type="submit" value="+1">
                </form>
                <form action="" method="post" class="bF">
                    <input type="hidden" name="pointchange" value="b2">
                    <input type="submit" value="+2">
                </form>
            </section>
            <section>
                <form action="" method="post" class="bF">
                    <input type="hidden" name="pointchange" value="b-">
                    <input type="submit" value="-1">
                </form>
                <form action="" method="post" class="bF">
                    <input type="hidden" name="pointchange" value="br">
                    <input type="submit" value="∅">
                </form>
            </section>
        </section>
    </section>
</aside>
<section class="mainbody">
    <p class="counter"><span class="seconds">20</span>:<span class="tens">00</span></p>
    <img src="rsc/playbutton.svg" alt="play" class="startbtn">
    <figure class="answer">
        <form action="" method="post">
            <input type="image" src="<?= $song['picture'] ?>" alt="ALBUM COVER">
        </form>
        <figcaption><?= $song['title'].",<br />". str_replace("&|&", ", ", $song['artists']) ?></figcaption>
    </figure>
</section>