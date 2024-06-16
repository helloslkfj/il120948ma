<?php 
    include_once "header-backened-nonfriend.php";

    //other session variables aren't there at the same moment so not unsetting of them
    //moreover we are concerned with being done with jsut the generated email
    if(isset($_POST["doneemail"]) == true && $_POST["doneemail"] == 'true') {
        if(isset($_SESSION["researchemailinfo"])) {
            unset($_SESSION["researchemailinfo"]);
        }

        echo "true";
    }

    if(isset($_POST["regenerate"]) == true && $_POST["regenerate"] == 'true') {
        if(isset($_SESSION["researchemailinfo"])) {
            if($_SESSION["researchemailinfo"]->attempts < 2) {
                $_SESSION["researchemailinfo"]->attempts += 1;

                $newresearchemail = regenerateResearchEmail($conn, $key, $encemail, $_SESSION["user"]->fname, $_SESSION["researchemailinfo"]->professorname, $_SESSION["researchemailinfo"]->professornotes, $_SESSION["researchemailinfo"]->publicationnotes, $_SESSION["researchemailinfo"]->template, $_SESSION["researchemailinfo"]->resume, $_SESSION["researchemailinfo"]->researchemail, $Open_API_Key);
                $newresearchemailsubject = createResearchEmailSubject($_SESSION["user"]->fname, $_SESSION["researchemailinfo"]->professorname, $newresearchemail, $Open_API_Key);

                executeSQL($conn, "UPDATE researchemails SET resemailsubject=?, resemailtext=? WHERE useremail=? AND emailid=?", ["s", "s", "s", "s"], encryptDataGivenIv([$newresearchemailsubject, $newresearchemail, $_SESSION["user"]->email, $_SESSION["researchemailinfo"]->emailid], $key, $_SESSION["user"]->iv), "update", "nothing");

                $_SESSION["researchemailinfo"]->researchemail = $newresearchemail;
                $_SESSION["researchemailinfo"]->researchemailsubject = $newresearchemailsubject;
            }
        }

        echo "true";
    }

    if(isset($_POST["exitemail"])) {
        if (isset($_SESSION["researchemailrequestobj"])) {
            unset($_SESSION["researchemailrequestobj"]);
        }

        if (isset($_SESSION["profwebextraction-error"])) {
            unset($_SESSION["profwebextraction-error"]);
        }

        if (isset($_SESSION["publicationextraction-error"])){
            unset($_SESSION["publicationextraction-error"]);
        }

        echo "true";
    }


    
?>