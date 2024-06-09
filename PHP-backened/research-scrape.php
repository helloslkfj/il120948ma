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

            //apply getSpecificAttributeDecryptedinList in all code

            if($foundprofwebpage > 0) { //main purpose is to just provide the profnotes for this split if and elese section
                $iv = getIvfortheDataRowwithXVar("profwebpages", $conn, $key, ["professorname", "linktowebsite"], [$professorname, $professorwebpage]);

                $encprofessornotes = getDatafromSQLResponse(["notestext", "iv"], executeSQL($conn, "SELECT * FROM profwebpages WHERE professorname=? AND linktowebsite=?", ["s", "s"], encryptDataGivenIv([$professorname, $professorwebpage], $key, $iv), "select", "nothing"));
                $profnotes = decryptFullData($encprofessornotes, $key, 1)[0][0];
            }
            else {
                $decwebpagelinks = getSpecificAttributeDecryptedinList("linktowebsite", "webpages", $conn, $key); //getting the webpage links in the webpage database

                if(in_array($professorwebpage, $decwebpagelinks)) {
                    $iv =  getIvfortheDataRowwithXVar("webpages", $conn, $key, ["linktowebsite"], [$professorwebpage]);
                    $encprofessortext = getDatafromSQLResponse(["webtext", "iv"], executeSQL($conn, "SELECT webtext, iv FROM webpages WHERE linktowebsite=?", ["s"], [encryptSingleDataGivenIv([$professorwebpage], $key, $iv)], "select", "nothing"));
                    $professortext = getAllElementsin1Dfrom2Darr(deleteIndexesfrom2DArray(decryptFullData($encprofessortext, $key, 1), [1]))[0]; // this professor text could have been from a second hand link so it can be nothing or it can something; it depends
                    if(strlen($professortext) < 1) {
                        // have to error handling for when you can't load website --> set a session of email request and put the error in there then it makes a box for you to put text --> then you don't do deep search, same thing for the bottom after the arrow
                        // also have to do error handling for dom document in webscraping --> same thing of setting a session of email request and pust the error in there then it makes a box for you to just copy and past the text --> 

                        exit(); //if session variable has error than we don't exit --> update that link in the database and put the text

                        //make a function for this process
                    }
                    //don't do anyhting to db if theres already stuff in the database
                }
                else {
                    $professortext = getAnythingFromHTML($professorwebpage, ['head', 'header', 'footer', 'script'], ['body'], 'text'); //if the session of error is set then we just don't do this and make professortext = "";
                    if(strlen($professortext) < 2) { 
                        // have to error handling for when you can't load website --> set a session of email request and put the error in there then it makes a box for you to put text --> then you don't do deep search, same thing for the bottom after the arrow
                        // also have to do error handling for dom document in webscraping --> same thing of setting a session of email request and pust the error in there then it makes a box for you to just copy and past the text --> 

                        exit(); //if session variable has error than we don't exit --> insert that link in the database and put the text
                    }
                    else {
                        //add the professor text and link to the database through insertion
                        $decpwebpageinsert = [$professorwebpage, $professortext];
                        $encpwebpageinsert = encryptDataGivenIv($decpwebpageinsert, $key, $_SESSION["user"]->iv);

                        executeSQL($conn, "INSERT INTO webpages(linktowebsite, webtext, iv) VALUES(?,?,?)", ["s", "s", "s"], array_merge($encpwebpageinsert, [$_SESSION["user"]->iv]), "insert", 2);
                    }
                }
    
                $alllinks = getLinksFromHTML($professorwebpage, ['script'], ['a', 'button'], 'href'); // --> if there was an error here in getting the page or loading it in dom document, it will be caught by the previous error so no handling
                $twoimportantresearchlinks = givetwoImportantResearchLinks($professorname, $professortext, $professorwebpage, $Open_API_Key);
    
                if($alllinks== "error" or $twoimportantresearchlinks == "error") {
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

                //professor notes text creation and insertion
                $totalprofessortext = "Professor Website: ".$professortext."<br> Important website 1 concerning professor research: ".$link1text."<br> Important website 2 concerning professor's research: ".$link2text;


                echo $totalprofessortext;

                $profnotes = makeNotesOnProfessor($professorname, $totalprofessortext, $Open_API_Key);
                $decprofwebinfodata = [$professorname, $professorwebpage, $profnotes];
                $encprofwebinfodata = encryptDataGivenIv($decprofwebinfodata, $key, $_SESSION["user"]->iv);
                executeSQL($conn, "INSERT INTO profwebpages(professorname, linktowebsite, notestext, iv) VALUES(?,?,?,?)", ["s","s","s","s"], array_merge($encprofwebinfodata, [$_SESSION["user"]->iv]), "insert", 3);
                
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