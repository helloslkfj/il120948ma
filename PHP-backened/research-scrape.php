<?php 
    include_once 'header-backened-nonfriend.php';

    $error = 0; 

    if(isset($_POST['professorname'])) {
        $professorname = $_POST['professorname'];
        if(strlen($professorname) > 2) {
            if(strpos($professorname, 'Dr.') != false or strpos($professorname, 'Professor') != false) {
                $error += 1;
                echo "The name provided must be the full name of the Professor without Dr. or Professor, it should just include the first, middle and last name<br>";
            }
        }
        else {
            $error += 1;
            echo "Professor full name must be at least 2 characters<br>";
        }
    }
    else {
        $error += 1;
    }

    if(isset($_POST['professorwebpage'])) {
        $professorwebpage = $_POST['professorwebpage'];
        if(filter_var($professorwebpage, FILTER_VALIDATE_URL) != true) {
            echo "Professor webpage link is not valid<br>";
            $error += 1;
        } 
    }
    else {
        $error += 1;
    }

    if(isset($_POST['publicationwebpage'])) {
        $publicationwebpage = $_POST['publicationwebpage'];
        if(filter_var($publicationwebpage, FILTER_VALIDATE_URL) != true) {
            echo "The link to the publication is not valid<br>";
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
        $decresumes = deleteIndexesfrom2DArray(decryptFullData($encresumes, $key, 1), [1]);

        if(in_array($_POST['resume'], $decresumes) != true) {
            echo "The resume selected is not one of your stored resumes<br>";
            $error += 1;
        }
    }
    else {
        $error += 1;
    }

    if(isset($_POST['generateresemail'])) {
        if($error < 1) {
            $professortext = getAnythingFromHTML($professorwebpage, ['head', 'header', 'footer', 'script'], ['body'], 'text');
            if(strlen($professortext) < 2) {
                // have to error handling for when you can't load website --> set a session of email request and put the error in there then it makes a box for you to put text --> then you don't do deep search, same thing for the bottom after the arrow
                // also have to do error handling for dom document in webscraping --> same thing of setting a session of email request and pust the error in there then it makes a box for you to just copy and past the text --> 
                exit();
            }

            $alllinks = getLinksFromHTML($professorwebpage, ['script'], ['a', 'button'], 'href'); // --> if there was an error here in getting the page or loading it in dom document, it will be caught by the previous error so no handling
            $twoimportantresearchlinks = givetwoImportantResearchLinks($professorname, $professortext, $professorwebpage, $Open_API_Key);

            if($twoimportantresearchlinks == "error") {
                $link1text = "";
                $link2text = "";
            }
            else {
                $link1text = getAnythingFromHTML($twoimportantresearchlinks[0], ['head', 'header', 'footer', 'script'], ['body'], 'text'); //--> this will just be a nothing string in the case of an error
                $link2text = getAnythingFromHTML($twoimportantresearchlinks[0], ['head', 'header', 'footer', 'script'], ['body'], 'text');
            }

            $webpagetextlinkarray = [[$professorwebpage, $twoimportantresearchlinks[0], $twoimportantresearchlinks[1]], [$professortext, $link1text, $link2text]];
            for($i=0;$i<count($webpagetextlinkarray[0]);$i++) {
                executeSQL($conn, "INSERT INTO webpages(linktowebsite, webtext, iv) VALUES(?,?,?)", ["s", "s", "s"], [$webpagetextlinkarray[0][$i], $webpagetextlinkarray[1][$i], $_SESSION["user"]->iv], "insert", 2);
            }



           //get the notes and insert it into the database.

        }
        else {
            echo "There are errors in your inputs<br>";
        }
    }

    //$textofmain = getAnythingFromHTML($link, ['head', 'header', 'footer', 'script'], ['body'], 'text');
    //givetwoImportantResearchLinks('Tim Bussey', $textofmain, 'https://tcnlab.ca/our-team/', $Open_API_Key);
    // error handle if you get error, just extract text of main then
    //now just have to extract the text of the two links
    //handle the full request and start scraping
?>