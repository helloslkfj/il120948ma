<?php 
    include_once "header-backened-nonfriend.php";

    if(isset($_POST["saveCheck"])) {
        $emailtype = $_POST["type"];

        if($emailtype == "Research Email" || $emailtype == "Corporate Email") {
            $emailsubject = normalizeString($_POST["emailsubject"]);
            $emailbody = normalizeString($_POST['emailbody']);
            $emailid = $_POST["emailid"];
        }
        else {
            $message = normalizeString($_POST['message']);
            $messageid = $_POST["messageid"];
        }

        if($emailtype == "Research Email") {
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
            if($emailtype == "Corporate Email") {
                //do corporate emails
                $encemail = getDatafromSQLResponse(["corporateemailsubject", "corporateemailtext", "iv"], executeSQL($conn, "SELECT corporateemailsubject, corporateemailtext, iv FROM corporateemails WHERE useremail=? AND emailid=?", ["s", "s"], [$encemail, encryptSingleDataGivenIv([$emailid], $key, $_SESSION["user"]->iv)], "select", "nothing"));
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
                //do linkedin messages
                $encmessage = getDatafromSQLResponse(["corporatemessagetext", "iv"], executeSQL($conn, "SELECT corporatemessagetext, iv FROM corporatemessages WHERE useremail=? AND messageid=?", ["s", "s"], [$encemail, encryptSingleDataGivenIv([$messageid], $key, $_SESSION["user"]->iv)], "select", "nothing"));
                $decmessage = deleteIndexesfrom2DArray(decryptFullData($encmessage, $key, 1), [1]);
                
                $databasemessage= normalizeString($decmessage[0][0]);
    
                if($message != $databasemessage) {
                    echo "notsaved";
                }
                else {
                    echo "saved";
                }
            }
        }
        
    }

    if(isset($_POST["save"])) {
        $emailtype = $_POST["type"];

        if($emailtype == "Research Email" || $emailtype == "Corporate Email") {
            $emailsubject = $_POST["emailsubject"];
            $emailbody = $_POST['emailbody'];
            $emailid = $_POST["emailid"];
        }
        else {
            $message = $_POST['message'];
            $messageid = $_POST["messageid"];
        }

        if($emailtype == "Research Email") {
            executeSQL($conn, "UPDATE researchemails SET resemailsubject=?, resemailtext=? WHERE useremail=? AND emailid=?", ["s", "s", "s", "s"], encryptDataGivenIv([$emailsubject, $emailbody, $_SESSION["user"]->email, $emailid], $key, $_SESSION["user"]->iv), "update", "nothing");
            echo "true";
        }
        else {
            //do corporate emails update
            if($emailtype == "Corporate Email") {
                executeSQL($conn, "UPDATE corporateemails SET corporateemailsubject=?, corporateemailtext=? WHERE useremail=? AND emailid=?", ["s", "s", "s", "s"], encryptDataGivenIv([$emailsubject, $emailbody, $_SESSION["user"]->email, $emailid], $key, $_SESSION["user"]->iv), "update", "nothing");
                echo "true";
            } 
            //do linkedin messages update
            else {
                executeSQL($conn, "UPDATE corporatemessages SET corporatemessagetext=? WHERE useremail=? AND messageid=?", ["s", "s", "s"], encryptDataGivenIv([$message, $_SESSION["user"]->email, $messageid], $key, $_SESSION["user"]->iv), "update", "nothing");
                echo "true";
            }
        }
    }

    if(isset($_POST["search"])) {
        $searchqry = strtolower($_POST["searchqry"]);

        unset($_SESSION["search"]);
        if($searchqry != "" || strlen($searchqry) !== 0) {
            //repeated this code twice so for future use a function:
            $encresearchemaildata = getDatafromSQLResponse(["emailid", "resemailsubject", "resemailtext", "datentimeinteger", "iv"], executeSQL($conn, "SELECT * FROM researchemails WHERE useremail=?", ["s"], [$encemail], "select", "nothing"));
            $decresearchemaildata = insertElementIntoTwoDarray(deleteIndexesfrom2DArray(decryptFullData($encresearchemaildata, $key, 4), [4]), 'Research Email');
                    
            $enccorporateemaildata = getDatafromSQLResponse(["emailid", "corporateemailsubject", "corporateemailtext", "datentimeinteger", "iv"], executeSQL($conn, "SELECT * FROM corporateemails WHERE useremail=?", ["s"], [$encemail], "select", "nothing"));
            $deccorporateemaildata = insertElementIntoTwoDarray(deleteIndexesfrom2DArray(decryptFullData($enccorporateemaildata, $key, 4), [4]), 'Corporate Email');

            $enccorporatemessagedata = getDatafromSQLResponse(["messageid", "corporatemessagetext", "datentimeinteger", "iv"], executeSQL($conn, "SELECT * FROM corporatemessages WHERE useremail=?;", ["s"], [$encemail], "select", "nothing"));
            $deccorporatemessagedata = deleteIndexesfrom2DArray(decryptFullData($enccorporatemessagedata, $key, 3), [3]);
            $deccorporatemessagedata = insertElementIntoTwoDarray(insertnonStaticElementofTwodArrayIntoTwoDarray($deccorporatemessagedata, $deccorporatemessagedata, 2), 'LinkedIn Message');
            
            $totalemailmessagearr = array_merge($decresearchemaildata, $deccorporateemaildata, $deccorporatemessagedata);
            
            $relevantemailsarr = [];

            for($i=0;$i<count($totalemailmessagearr);$i++) {
                if($totalemailmessagearr[$i][4] == "Research Email" || $totalemailmessagearr[$i][4] == "Corporate Email") {
                    $totaltext =  strtolower($totalemailmessagearr[$i][1].$totalemailmessagearr[$i][2]);
                } else{
                    $totaltext = strtolower($totalemailmessagearr[$i][1]);
                }
                if(strpos($totaltext, $searchqry) !== false) {
                    $relevantemailsarr[] = $totalemailmessagearr[$i];
                }
            }

            $_SESSION["search"] = $relevantemailsarr;

        }

        echo "true";
    }
?>