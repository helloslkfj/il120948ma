<?php 
    include_once __DIR__.'/header-backened-nonfriend.php';

    $error = 0;

    $encemail = encryptSingleDataGivenIv([$_SESSION["user"]->email], $key, $_SESSION["user"]->iv);

    if(isset($_POST['templatetitle'])) {
        $templatetitle = mysqli_real_escape_string($conn, $_POST['templatetitle']);

        $enctemplates = getDatafromSQLResponse(["email", "title", "textt", "datentimeinteger", "iv"], executeSQL($conn, "SELECT * FROM templates WHERE email='$encemail';", "nothing", "nothing", "select", "nothing"));
        $dectemplates = decryptFullData($enctemplates, $key, 4);

        if(strlen($templatetitle) < 2) {
            $error +=1;
            echo "The title of your template must be atleast two characters long.<br>";
        }

        for ($i=0;$i<count($dectemplates);$i++) {
            if(isset($_SESSION["template"])) {
                if($dectemplates[$i][1] != $_SESSION["template"]->title) {
                    if($templatetitle == $dectemplates[$i][1]) {
                        $error += 1;
                        echo "One of your saved templates already has this title. Choose another title.";
                        break;
                    }
                }
            } else {
                if($templatetitle == $dectemplates[$i][1]) {
                    $error += 1;
                    echo "One of your saved templates already has this title. Choose another title.";
                    break;
                }
            }
        }
    }
    else {
        $error += 1;
    }

    if(isset($_POST['templatetext'])) {
        $templatetext = mysqli_real_escape_string($conn, $_POST['templatetext']);

        if(strlen($templatetext) < 2) {
            $error += 1;
            echo "The text of your template must be atleast two characters long.";
        }
        else if(strlen($templatetext) > 800) {
            $error += 1;
            echo "The text of your template must be no greater than 800 characters.";
        }
    }
    else {
        $error += 1;
    }

    if($error < 1) {
        if(isset($_SESSION["template"])) {
            $enctemplatetitle = encryptSingleDataGivenIv([$_SESSION["template"]->title], $key, $_SESSION["template"]->iv);
            $templateupdatesql = "UPDATE templates SET title=?, textt=?, datentimeinteger=?, iv=? WHERE email='$encemail' AND title='$enctemplatetitle';";
            date_default_timezone_set('America/New_York');// if we are always referencing EST time then we will always be true on when they created the template and can arrange based on recent

            $templatedataarr = [$templatetitle, $templatetext, strtotime(date("Y-m-d H:i:s"))];
            if(gettype($templatedataarr[2]) == 'boolean') {
                exit("Error: Unix timestamp not obtained!");
            }

            $enctemplatedataarr = encryptDataGivenIv($templatedataarr, $key, $_SESSION["user"]->iv);
            executeSQL($conn, $templateupdatesql, ["s", "s", "s", "s"], array_merge($enctemplatedataarr, [$_SESSION["user"]->iv]), "update", 3);   

            unset($_SESSION["template"]);
            
            echo "true";
        } 
        else {
            $templateinsertsql = "INSERT INTO templates(email, title, textt, datentimeinteger, iv) VALUES(?,?,?,?,?);";
            date_default_timezone_set('America/New_York');// if we are always referencing EST time then we will always be true on when they created the template and can arrange based on recent

            $templatedataarr = [$_SESSION["user"]->email, $templatetitle, $templatetext, strtotime(date("Y-m-d H:i:s"))];
            if(gettype($templatedataarr[3]) == 'boolean') {
                exit("Error: Unix timestamp not obtained!");
            }

            $enctemplatedataarr = encryptDataGivenIv($templatedataarr, $key, $_SESSION["user"]->iv);
            executeSQL($conn, $templateinsertsql, ["s", "s", "s", "s", "s"], array_merge($enctemplatedataarr, [$_SESSION["user"]->iv]), "insert", 4);   

            echo "true";
        }
    }
    else {
        exit;
    }

    
?>