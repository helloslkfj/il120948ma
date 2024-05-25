<?php 
    include_once __DIR__.'/header-backened-nonfriend.php';

    if(isset($_POST['delete'])) {
        if(isset($_POST['templatetitle'])) {
            $templatetitle = $_POST['templatetitle'];

            if(isset($_SESSION["template"])) {
                if($templatetitle == $_SESSION["template"]->title) {
                    unset($_SESSION["template"]);
                }
            }
            
            $enctemplatetitle = encryptSingleDataGivenIv([$_POST['templatetitle']], $key, $_SESSION["user"]->iv);

            executeSQL($conn, "DELETE FROM templates WHERE email=? AND title=?", ["s", "s"], [$encemail, $enctemplatetitle], "delete", "nothing");

            echo "true";
        }
    }

    if(isset($_POST['exit'])) {
        unset($_SESSION["template"]);

        echo "true";
    }

?>