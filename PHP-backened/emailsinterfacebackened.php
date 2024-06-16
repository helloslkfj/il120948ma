<?php 
    include_once "header-backened-nonfriend.php";

    if(isset($_POST["saveCheck"])) {
        $emailsubject = normalizeString($_POST["emailsubject"]);
        $emailbody = normalizeString($_POST['emailbody']);
        $emailid = $_POST["emailid"];
        $emailtype = $_POST["type"];

        if($emailtype == "Research") {
            $encemail = getDatafromSQLResponse(["resemailsubject", "resemailtext", "iv"], executeSQL($conn, "SELECT resemailsubject, resemailtext, iv FROM researchemails WHERE useremail=? AND emailid=?", ["s", "s"], [$encemail, encryptSingleDataGivenIv([$emailid], $key, $_SESSION["user"]->iv)], "select", "nothing"));
            $decemail = deleteIndexesfrom2DArray(decryptFullData($encemail, $key, 2), [2]);
            
            $databasesubject = normalizeString($decemail[0][0]);
            $databasetext= normalizeString($decemail[0][1]);

            if($emailsubject != $databasesubject || $emailbody != $databasetext) {
                echo "notsaved";
            }
            else {
                echo "saved";
            }
        }
        else {
            //do corporate emails
        }
        
    }

    if(isset($_POST["save"])) {
        $emailsubject = $_POST["emailsubject"];
        $emailbody = $_POST['emailbody'];
        $emailid = $_POST["emailid"];
        $emailtype = $_POST["type"];

        if($emailtype == "Research") {
            executeSQL($conn, "UPDATE researchemails SET resemailsubject=?, resemailtext=? WHERE useremail=? AND emailid=?", ["s", "s", "s", "s"], encryptDataGivenIv([$emailsubject, $emailbody, $_SESSION["user"]->email, $emailid], $key, $_SESSION["user"]->iv), "update", "nothing");
            echo "true";
        }
        else {
            //do corporate emails update
        }
    }
?>