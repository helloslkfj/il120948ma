<?php 
    include_once __DIR__.'header-backened-nonfriend.php';

    if(isset($_POST['personname'])) {
        $personname = $_POST['personname'];
        if(strlen($personname) < 2) {
            $error += 1;
            echo "Full name must be at least 2 characters<br>";
        }
    }
    else {
        $error += 1;
    }

    if(isset($_POST['personwebpage'])) {
        $personwebpage = $_POST['personwebpage'];
        if(filter_var($personwebpage, FILTER_VALIDATE_URL) != true) {
            echo "The webpage link of the person you are outreaching to is not valid<br>";
            $error += 1;
        } 
    }
    else {
        $error += 1;
    }

    if(isset($_POST['companywebpage'])) {
        $companywebpage = $_POST['companywebpage'];
        if(filter_var($companywebpage, FILTER_VALIDATE_URL) != true) {
            echo "The link to the company webpage is not valid<br>";
            $error += 1;
        }
    }
    else {
        $error += 1;
    }

    if(isset($_POST["personwebpagetextinput"])) {
        $personwebpagetextinput = $_POST["personwebpagetextinput"];
        if(strlen($personwebpagetextinput) < 30) {
            $error += 1;
            echo "The webpage text inputted of the person you are outreaching to must be atleast 30 characters";
        }
    }

    if(isset($_POST["companywebpagetextinput"])) {
        $companywebpagetextinput = $_POST["companywebpagetextinput"];
        if(strlen($companywebpagetextinput) < 30) {
            $error += 1;
            echo "The company webpage text inputted must be atleast 30 characters";
        }
    }

    if(isset($_POST['template'])) {
        $template = $_POST['template'];

        $enctemplates = getDatafromSQLResponse(["title", "iv"], executeSQL($conn, "SELECT * FROM templates WHERE email=?", ["s"], [$encemail], "select", "nothing"));
        $dectemplates = getAllElementsin1Dfrom2Darr(deleteIndexesfrom2DArray(decryptFullData($enctemplates, $key, 1), [1]));

        if(in_array($_POST['template'], $dectemplates) != true) {
            echo "The template selected is not one of your stored templates<br>";
            $error += 1;
        }
    }
    else {
        $error += 1;
    }

    if(isset($_POST['resume'])) {
        $resume = $_POST['resume'];

        $encresumes = getDatafromSQLResponse(["resumename", "iv"], executeSQL($conn, "SELECT * FROM resumes WHERE email=?", ["s"], [$encemail], "select", "nothing"));
        $decresumes = getAllElementsin1Dfrom2Darr(deleteIndexesfrom2DArray(decryptFullData($encresumes, $key, 1), [1]));

        if(in_array($_POST['resume'], $decresumes) != true) {
            echo "The resume selected is not one of your stored resumes<br>";
            $error += 1;
        }
    }
    else {
        $error += 1;
    }

    if(isset($_POST["generatecorporateemail"])) {
        if(isset($_SESSION["corporateemailinfo"])) {
            unset($_SESSION["corporateemailinfo"]);
        }

        if ($error < 1) {
            $encpersonwebpagelinks = getDatafromSQLResponse(["linktopersonwebsite", "iv"], executeSQL($conn, "SELECT * FROM personwebpages", "nothing", "nothing", "select", "nothing"));
            $decpersonwebpagelinks = collapse2DArrayto1D(deleteIndexesfrom2DArray(decryptFullData($encpersonwebpagelinks, $key, 1), [1]));

            if(in_array($personwebpage, $decpersonwebpagelinks)) {
                $iv = getIVfortheDataRowwithXVar("personwebpages", $conn, $key, ["linktopersonwebsite"], [$personwebpage]);
                $encpersonnotes = getDatafromSQLResponse(["notestext", "iv"], executeSQL($conn, "SELECT * FROM personwebpages where linktopersonwebpage=?", ["s"], [encryptSingleDataGivenIv([$personwebpage], $key, $iv)], "select", "nothing"));
                $decpersonnotes = deleteIndexesfrom2DArray(decryptFullData($encpersonnotes, $key, 1), [1]);

                $personnotes = $decpersonwebpagenotes[0][0];
            }
            else {
                if(isset($_POST["personwebpagetextinput"])) {
                    // $personwebpagetext = getImportantCorporate text function
                } else {

                }
            }
        }
    }





    
?>
