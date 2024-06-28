<?php 
    include_once __DIR__."/header-backened-nonfriend.php";

    if(isset($_POST['exitemailormes']) == true && $_POST['exitemailormes']=='true') {
        if(isset($_SESSION["corporateemailrequestobj"])) {
            unset($_SESSION["corporateemailrequestobj"]);
        }

        if(isset($_SESSION["personwebextraction-error"])) {
            unset($_SESSION["personwebextraction-error"]);
        }

        if(isset($_SESSION["companywebextraction-error"])) {
            unset($_SESSION["companywebextraction-error"]);
        }

        echo "true";
    }   

    if(isset($_POST['doneemail']) == true && $_POST['doneemail'] == 'true') {
        if(isset($_SESSION["corporateemailinfo"])) {
            unset($_SESSION["corporateemailinfo"]);
        }

        echo "true";
    }

    if(isset($_POST['donemessage']) == true && $_POST['donemessage'] == 'true') {
        if(isset($_SESSION["corporatemessageinfo"])) {
            unset($_SESSION["corporatemessageinfo"]);
        }

        echo "true";
    }

    if(isset($_POST["regenerate"]) == true && $_POST["regenerate"] == 'true') {
        if(isset($_SESSION["corporateemailinfo"])) {
            if($_SESSION["corporateemailinfo"]->attempts < 2) {
                $_SESSION["corporateemailinfo"]->attempts += 1;

                $regeneratedemailtext = regenerateCorporateEmail($conn, $key, $encemail, $_SESSION["user"]->fname, $_SESSION["corporateemailinfo"]->personname, $_SESSION["corporateemailinfo"]->industryofinterest, $_SESSION["corporateemailinfo"]->personnotes, $_SESSION["corporateemailinfo"]->companynotes, $_SESSION["corporateemailinfo"]->template, $_SESSION["corporateemailinfo"]->resume, $_SESSION["corporateemailinfo"]->corporateemail, $Open_API_Key);
                $regeneratedsubejct = createCorporateEmailSubject($_SESSION["user"]->fname, $_SESSION["corporateemailinfo"]->personname, $regeneratedemailtext, $Open_API_Key);
                
                executeSQL($conn, "UPDATE corporateemails SET corporateemailtext=?, corporateemailsubject=? WHERE useremail=? and emailid=?;", ["s", "s", "s", "s"], encryptDataGivenIv([$_SESSION["corporateemailinfo"]->corporateemail, $_SESSION["corporateemailinfo"]->corporateemailsubject, $_SESSION["user"]->email, $_SESSION["corporateemailinfo"]->emailid], $key, $_SESSION["user"]->iv), "update", "nothing");


                $_SESSION["corporateemailinfo"]->corporateemail = $regeneratedemailtext;
                $_SESSION["corporateemailinfo"]->corporateemailsubject = $regeneratedsubejct;
            }
        }

        if(isset($_SESSION["corporatemessageinfo"])) {
            if($_SESSION["corporatemessageinfo"]->attempts < 2) {
                $_SESSION["corporatemessageinfo"]->attempts += 1;

                $regeneratedmessage = regenerateLinkedInMessage($conn, $key, $encemail, $_SESSION["user"]->fname, $_SESSION["corporatemessageinfo"]->personname, $_SESSION["corporatemessageinfo"]->industryofinterest, $_SESSION["corporatemessageinfo"]->personnotes, $_SESSION["corporatemessageinfo"]->companynotes, $_SESSION["corporatemessageinfo"]->template, $_SESSION["corporatemessageinfo"]->resume, $_SESSION["corporatemessageinfo"]->corporatemessage, $Open_API_Key);

                executeSQL($conn, "UPDATE corporatemessages SET corporatemessagetext=? WHERE useremail=? AND messageid=?;", ["s", "s", "s"], encryptDataGivenIv([$regeneratedmessage, $_SESSION["user"]->email, $_SESSION["corporatemessageinfo"]->messageid], $key, $_SESSION["user"]->iv), "update", "nothing");

                $_SESSION["corporatemessageinfo"]->corporatemessage = $regeneratedmessage;
            }
        }

        echo "true";
    }

    
?>