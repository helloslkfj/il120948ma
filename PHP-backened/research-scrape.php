<?php 
    include_once 'header-backened-nonfriend.php';

    $error = 0; 

    if(isset($_POST['professorwebpage'])) {
        $professorwebpage = $_POST['professorwebpage'];
        if(filter_var($professorwebpage, FILTER_VALIDATE_URL) != true) {
            echo "Professor webpage link is not valid";
            $error += 1;
        } 
    }
    else {
        $error += 1;
    }

    if(isset($_POST['publicationwebpage'])) {
        $publicationwebpage = $_POST['publicationwebpage'];
        if(filter_var($publicationwebpage, FILTER_VALIDATE_URL) != true) {
            echo "The link to the publication is not valid";
            $error += 1;
        }
    }
    else {
        $error += 1;
    }

    if(isset($_POST['template'])) {
        $template = $_POST['template'];

        $enctemplates = getDatafromSQLResponse(["title", "iv"], executeSQL($conn, "SELECT * FROM templates WHERE email=?", ["s"], [$encemail], "select", "nothing"));
        $dectemplates = deleteIndexesfrom2DArray(decryptFullData($enctemplates, $key, 1), [1]);

        if(in_array($_POST['template'], $dectemplates) != true) {
            echo "The template selected is not one of your stored templates";
            $error += 1;
        }
    }
    else {
        $error += 1;
    }

    if(isset($_POST['resume'])) {
        $resume = $_POST['resume'];

        $encresumes = getDatafromSQLResponse(["resumename", "iv"], executeSQL($conn, "SELECT * FROM resumes WHERE email=?", ["s"], [$encemail], "select", "nothing"));
        $decresumes = deleteIndexesfrom2DArray(decryptFullData($encresumes, $key, 1), [1]);

        if(in_array($_POST['resume'], $decresumes) != true) {
            echo "The resume selected is not one of your stored resumes";
            $error += 1;
        }
    }
    else {
        $error += 1;
    }

    if(isset($_POST['generatereseamil'])) {
        if($error < 1) {
            
        }
    }

    //handle the full request and start scraping
?>