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

    if(isset($_POST["profwebpagetextinput"])) { 
        $profwebpagetextinput = $_POST["profwebpagetextinput"];
        if(strlen($profwebpagetextinput) < 30) {
            $error += 1;
            echo "The professor webpage text inputted must be greater than 30 characters<br>";
        }
    }

    if(isset($_POST["publicationtextinput"])) {
        $publicationtextinput = $_POST["publicationtextinput"];
        if(strlen($publicationtextinput) < 30) {
            $error += 1;
            echo "The publication text inputted must be greater than 30 characters<br>";
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

    if(isset($_POST['generateresemail'])) {
        if($error < 1) {
            //try to check the publication table and the notes there similar to prof notes checking
            //then you just have to check in the webpages --> everything is in there which is in the common function itself
            //put the important research text function and try to condense the original function
            
            $encpublicationlinks = getDatafromSQLResponse(["publicationlink", "iv"], executeSQL($conn, "SELECT * FROM publications", "nothing", "nothing", "select", "nothing"));
            $decpublicationlinks = collapse2DArrayto1D(deleteIndexesfrom2DArray(decryptFullData($encpublicationlinks, $key, 1), [1]));

            if(in_array($publicationwebpage, $decpublicationlinks)) {
                $iv = getIvfortheDataRowwithXVar("publications", $conn, $key, ["publicationlink"], [$publicationwebpage]);
                $encpublicationnotes = getDatafromSQLResponse(["publicationnotes", "iv"], executeSQL($conn, "SELECT * FROM publications WHERE publicationlink=?", ["s"], [encryptSingleDataGivenIv([$publicationwebpage], $key, $iv)], "select", "nothing"));
                $decpublicationnotes = deleteIndexesfrom2DArray(decryptFullData($encpublicationnotes, $key, 1), [1]);

                $publicationnotes = $decpublicationnotes[0][0];
            }
            else {
                if(isset($_POST["publicationtextinput"])) {
                    $publicationtext = getImportantResearchText($conn, $key, $publicationwebpage, $professorwebpage, "publication", $publicationtextinput, $professorname, $template, $resume);
                }
                else {
                    $publicationtext = getImportantResearchText($conn, $key, $publicationwebpage, $professorwebpage, "publication", "", $professorname, $template, $resume);
                }

                if(isset($_SESSION["publicationextraction-error"])) {
                    unset($_SESSION["publicationextraction-error"]);
                }

                $formattedpublicationtext = "Publication: |\.uejd/|".$publicationtext;
                $publicationnotes = makeNotesOnPublication($professorname, $formattedpublicationtext, $Open_API_Key);

                executeSQL($conn, "INSERT publications(publicationlink, publicationnotes, iv) VALUES (?,?,?)", ["s","s","s"], array_merge(encryptDataGivenIv([$publicationwebpage, $publicationnotes], $key, $_SESSION["user"]->iv), [$_SESSION["user"]->iv]), "insert", 2);
            }

            $encexistingprofwebpages = getDatafromSQLResponse(["professorname", "linktowebsite", "iv"], executeSQL($conn, "SELECT * FROM profwebpages", "nothing", "nothing", "select", "nothing"));
            $decexistingprofwebpages = deleteIndexesfrom2DArray(decryptFullData($encexistingprofwebpages, $key, 2), [2]);

            $foundprofwebpage = 0;
            for($i=0; $i<count($decexistingprofwebpages); $i++) {
                if ($professorname == $decexistingprofwebpages[$i][0]) {
                    if($professorwebpage == $decexistingprofwebpages[$i][1]) {
                        $foundprofwebpage += 1;
                        break;
                    }
                }
            } 

            if($foundprofwebpage > 0) { //main purpose is to just provide the profnotes for this split if and elese section
                $iv = getIvfortheDataRowwithXVar("profwebpages", $conn, $key, ["professorname", "linktowebsite"], [$professorname, $professorwebpage]);

                $encprofessornotes = getDatafromSQLResponse(["notestext", "iv"], executeSQL($conn, "SELECT * FROM profwebpages WHERE professorname=? AND linktowebsite=?", ["s", "s"], encryptDataGivenIv([$professorname, $professorwebpage], $key, $iv), "select", "nothing"));
                $profnotes = decryptFullData($encprofessornotes, $key, 1)[0][0];
            }
            else {
                if(isset($_POST["profwebpagetextinput"])) {
                    $professortext = getImportantResearchText($conn, $key, $professorwebpage, $publicationwebpage, "professorwebpage", $profwebpagetextinput, $professorname, $template, $resume);
                }
                else {
                    $professortext = getImportantResearchText($conn, $key, $professorwebpage, $publicationwebpage, "professorwebpage", "", $professorname, $template, $resume);
                }
                $alllinks = getLinksFromHTML($professorwebpage, ['script'], ['a', 'button'], 'href'); // --> if there was an error here in getting the page or loading it in dom document, it will be caught by the previous error so no handling
                $twoimportantresearchlinks = givetwoImportantResearchLinks($professorname, $professortext, $professorwebpage, $Open_API_Key);
        
                if($alllinks== "error" || $twoimportantresearchlinks == "error") {
                    $link1text = "";
                    $link2text = "";
                }
                else {
                    for($i=0;$i<count($twoimportantresearchlinks);$i++) {
                        $decwebpagelinks = getSpecificAttributeDecryptedinList("linktowebsite", "webpages", $conn, $key);
                        $linktextvarname = "link".($i+1)."text";
                        if(in_array($twoimportantresearchlinks[$i], $decwebpagelinks) != true) {
                            $linktext = getAnythingFromHTML($twoimportantresearchlinks[$i], ['head', 'header', 'footer', 'script'], ['body'], 'text');
                            $decwebpageinsert = [$twoimportantresearchlinks[$i], $linktext];
                            $encwebpageinsert = encryptDataGivenIv($decwebpageinsert, $key, $_SESSION["user"]->iv);
                            executeSQL($conn, "INSERT INTO webpages(linktowebsite, webtext, iv) VALUES(?,?,?)", ["s", "s", "s"], array_merge($encwebpageinsert, [$_SESSION["user"]->iv]), "insert", 2);

                            $$linktextvarname = $linktext; 
                        }
                        else { 
                                //for searches you have to be able to get the iv that is in the database for that row of data
                            $iv = getIvfortheDataRowwithXVar("webpages", $conn, $key, ["linktowebsite"], [$twoimportantresearchlinks[$i]]);
                            $$linktextvarname = deleteIndexesfrom2DArray(decryptFullData(getDatafromSQLResponse(["webtext", "iv"], executeSQL($conn, "SELECT * FROM webpages WHERE linktowebsite=?", ["s"], [encryptSingleDataGivenIv([$twoimportantresearchlinks[$i]], $key, $iv)], "select", "nothing")), $key, 1), [1])[0][0];
                        }
                    }
                }

                if(isset($_SESSION["profwebextraction-error"])) {
                    unset($_SESSION["profwebextraction-error"]);
                }

                //professor notes text creation and insertion
                $totalprofessortext = "Professor Website: |\.uejd/|".$professortext."|\.uejd/| <br> Important website 1 concerning professor research: |\.uejd/|".$link1text."|\.uejd/| <br> Important website 2 concerning professor's research: |\.uejd/|".$link2text;
                $profnotes = makeNotesOnProfessor($professorname, $totalprofessortext, $Open_API_Key);
                $decprofwebinfodata = [$professorname, $professorwebpage, $profnotes];
                $encprofwebinfodata = encryptDataGivenIv($decprofwebinfodata, $key, $_SESSION["user"]->iv);
                executeSQL($conn, "INSERT INTO profwebpages(professorname, linktowebsite, notestext, iv) VALUES(?,?,?,?)", ["s","s","s","s"], array_merge($encprofwebinfodata, [$_SESSION["user"]->iv]), "insert", 3);
            }

            echo $publicationnotes;
            echo "<br>";
            echo $profnotes;
        }
        else {
            echo "There are errors in your inputs<br>";
        }
        
    }

    //write the email as it is able to scrape the data and write notes and handle errors

    //$textofmain = getAnythingFromHTML($link, ['head', 'header', 'footer', 'script'], ['body'], 'text');
    //givetwoImportantResearchLinks('Tim Bussey', $textofmain, 'https://tcnlab.ca/our-team/', $Open_API_Key);
    // error handle if you get error, just extract text of main then
    //now just have to extract the text of the two links
    //handle the full request and start scraping
?>