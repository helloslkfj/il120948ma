<?php 
    include_once __DIR__.'/header-backened-nonfriend.php';

    $error = 0;

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

    if(isset($_POST["messagetype"])) {
        $messagetype = $_POST["messagetype"];
        if($messagetype != "Email" and $messagetype != "LinkedIn Message") {
            $error += 1;
            echo "Either email or LinkedIn message must be selected<br>";
        }
    }
    else {
        $error += 1;
    }

    if(isset($_POST["industryofinterest"])) {
        $industryofinterest = $_POST["industryofinterest"];
        if(strlen($industryofinterest) < 2) {
            $error += 1;
            echo "The industry of interest stated must be atleast 2 characters long.";
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




    if(isset($_POST["generatecorporate"])) {
        if(isset($_SESSION["corporateemailinfo"])) {
            unset($_SESSION["corporateemailinfo"]);
        }

        if(isset($_SESSION["corporatemessageinfo"])) {
            unset($_SESSION["corporatemessageinfo"]);
        }

        if ($error < 1) {
            $encpersonwebpagelinks = getDatafromSQLResponse(["linktopersonwebsite", "iv"], executeSQL($conn, "SELECT * FROM personwebpages", "nothing", "nothing", "select", "nothing"));
            $decpersonwebpagelinks = collapse2DArrayto1D(deleteIndexesfrom2DArray(decryptFullData($encpersonwebpagelinks, $key, 1), [1]));

            $scrape = 0;

            if(in_array($personwebpage, $decpersonwebpagelinks)) {
                $iv = getIVfortheDataRowwithXVar("personwebpages", $conn, $key, ["linktopersonwebsite"], [$personwebpage]);
                
                $encpersonnotesanddate = getDatafromSQLResponse(["notestext", "datentimeinteger", "iv"], executeSQL($conn, "SELECT * FROM personwebpages WHERE linktopersonwebsite=?", ["s"], [encryptSingleDataGivenIv([$personwebpage], $key, $iv)], "select", "nothing"));
                $decpersonnotesanddate= decryptFullData($encpersonnotesanddate, $key, 2);

                $timedistance = strtotime(date("Y-m-d H:i:s")) - $decpersonnotesanddate[0][1];
                if($timedistance > 2592000) {
                    $scrape += 1;
                    executeSQL($conn, "DELETE FROM personwebpages WHERE linktopersonwebsite=?", ["s"], encryptSingleDataGivenIv([$personwebpage], $key, $iv), "delete", "nothing");
                } else {
                    $personnotes = $decpersonnotesanddate[0][0];
                }
            }
            else {
                $scrape += 1;
            }

            if($scrape > 0) {
                $api_nonfunc = 0;

                if(strpos($personwebpage, 'https://www.linkedin.com/in/') !== false) {
                    $personwebpagetext = getLinkedInInformation($Proxycurl_API_Key, $personwebpage);

                    if($personwebpagetext == "error") {
                        $api_nonfunc += 1;
                    }
                } else {
                    $api_nonfunc += 1;
                }

                if($api_nonfunc > 0) {
                    if(isset($_POST["personwebpagetextinput"])) {
                        $personwebpagetext = getImportantCorporateText($conn, $key, $personwebpage, $companywebpage, "personwebpage", $personwebpagetextinput, $personname, $messagetype, $industryofinterest, $resume, $template);
                    } else {
                        $personwebpagetext = getImportantCorporateText($conn, $key, $personwebpage, $companywebpage, "personwebpage", "", $personname, $messagetype, $industryofinterest, $resume, $template);
                    }
                }

                if(isset($_SESSION["personwebextraction-error"])) {
                    unset($_SESSION["personwebextraction-error"]);
                }

                $formattedpersonwebpagetext = "Person Webpage Text: |\.uejd/|".$personwebpagetext;
                $personnotes = makeNotesonPerson($personname, $formattedpersonwebpagetext, $Open_API_Key);

                executeSQL($conn, "INSERT INTO personwebpages(personname, linktopersonwebsite, notestext, datentimeinteger, iv) VALUES(?,?,?,?,?);", ["s", "s", "s", "s", "s"], array_merge(encryptDataGivenIv([$personname, $personwebpage, $personnotes, strtotime(date("Y-m-d H:i:s"))], $key, $_SESSION["user"]->iv), [$_SESSION["user"]->iv]), "insert", 4);
            }
            //add time equivalence to get corporate text in webpages and down below, (later on because this is very rare of getting linkedin links by those two random searches)also add linkedin scraping to get corporate text so if it is in webpages you don't just do it;
            $encexistingcompanywebpagelinks = getDatafromSQLResponse(["companylink", "iv"], executeSQL($conn, "SELECT * FROM companywebpages", "nothing", "nothing", "select", "nothing"));
            $decexistingcompanywebpagelinks = collapse2DArrayto1D(deleteIndexesfrom2DArray(decryptFullData($encexistingcompanywebpagelinks, $key, 1), [1]));

            $scrape = 0;

            if(in_array($companywebpage, $decexistingcompanywebpagelinks)) {
                $iv = getIVfortheDataRowwithXVar("companywebpages", $conn, $key, ["companylink"], [$companywebpage]);
                $enccompanynotesanddate = getDatafromSQLResponse(["companynotes", "datentimeinteger", "iv"], executeSQL($conn, "SELECT * FROM companywebpages WHERE companylink=?", ["s"], [encryptSingleDataGivenIv([$companywebpage], $key, $iv)], "select", "nothing"));
                $deccompanynotesanddate = deleteIndexesfrom2DArray(decryptFullData($enccompanynotesanddate, $key, 2), [2]);

                $timedistancecompany = strtotime(date("Y-m-d H:i:s")) - $deccompanynotesanddate[0][1];

                if($timedistancecompany > 2592000) {
                    $scrape += 1;
                    executeSQL($conn, "DELETE FROM companywebpages WHERE companylink=?;", ["s"], [encryptSingleDataGivenIv([$companywebpage], $key, $iv)], "delete", "nothing");
                } else {
                    $companynotes = $deccompanynotesanddate[0][0];
                }
            }
            else {
                $scrape += 1;
            }
            
            if($scrape > 0) {
                if(isset($_POST["companywebpagetextinput"])) {
                    $companywebpagetext = getImportantCorporateText($conn, $key, $companywebpage, $personwebpage, "companywebpage", $companywebpagetextinput, $personname, $messagetype, $industryofinterest, $resume, $template);
                }
                else {
                    $companywebpagetext = getImportantCorporateText($conn, $key, $companywebpage, $personwebpage, "companywebpage", "", $personname, $messagetype, $industryofinterest, $resume, $template);
                }

                if(isset($_SESSION["companywebextraction-error"])) {
                    unset($_SESSION["companywebextraction-error"]);
                }

                $formattedcompanywebpagetext = "Company Webpage Text: |\.uejd/|".$companywebpagetext;

                $companynotes = makeNotesonCompany($formattedcompanywebpagetext, $Open_API_Key);

                executeSQL($conn, "INSERT INTO companywebpages(companylink, companynotes, datentimeinteger, iv) VALUES(?,?,?,?);", ["s", "s", "s", "s"], array_merge(encryptDataGivenIv([$companywebpage, $companynotes, strtotime(date("Y-m-d H:i:s"))], $key, $_SESSION["user"]->iv), [$_SESSION["user"]->iv]), "insert", 3);
            }
            

            //only when you have text extraction errors, it works for both corporate messages and emails (the name is a little confusing cause it makes it seem that it mostly pertains to emails)
            if(isset($_SESSION["corporateemailrequestobj"])) {
                unset($_SESSION["corporateemailrequestobj"]);
            }

            if($messagetype == "Email") {
                $corporateemail = createCorporateEmail($conn, $key, $encemail, $_SESSION["user"]->fname, $personname, $industryofinterest, $personnotes, $companynotes, $template, $resume, $Open_API_Key);
                $corporateemailsubject = createCorporateEmailSubject($_SESSION["user"]->fname, $personname, $corporateemail, $Open_API_Key);
                $emailid = generateEmailId();

                $corporateemailinfo = new stdClass();
                $_SESSION["corporateemailinfo"] = createCorporateEmailInfo($corporateemailinfo, $personname, $industryofinterest, $personnotes, $companynotes, $template, $resume, $emailid, $corporateemail, $corporateemailsubject);

                //the corporate emails tab on the users table will account for both linkedin messages and actual corporate emails in terms of count
                $corporateemailcount = (int)getDatafromSQLResponse(["corporateemails"], executeSQL($conn, "SELECT * FROM users WHERE email=?", ["s"], [$encemail], "select", "nothing"));
                
                //adding count to the corporate emailcount
                $corporateemailcount += 1;

                //submiting updated count to the users database
                executeSQL($conn, "UPDATE users SET corporateemails=? WHERE email=?", ["i", "s"], [$corporateemailcount, $encemail], "update", "nothing");

                //submitting the corporate email to the corporate emails database
                $deccorporateemailinputsarr = [$emailid, $_SESSION["user"]->email, $personname, $personwebpage, $companywebpage, $corporateemailsubject, $corporateemail, $resume, $template, "nothing", strtotime(date("Y-m-d H:i:s"))];
                executeSQL($conn, "INSERT INTO corporateemails(emailid, useremail, personname, personwebpage, companywebpage, corporateemailsubject, corporateemailtext, resumename, templatename, rating1to10, datentimeinteger, iv) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)", ["s","s","s","s","s","s","s","s","s","s","s","s"], array_merge(encryptDataGivenIv($deccorporateemailinputsarr, $key, $_SESSION["user"]->iv), [$_SESSION["user"]->iv]), "insert", 11);

                echo "true";

            }
            else {
                //what to do generate the linkedin message (different session variable holding the information)
                //corporate message is a linkedin message

                $corporatemessage = createLinkedInMessage($conn, $key, $encemail, $_SESSION["user"]->fname, $personname, $industryofinterest, $personnotes, $companynotes, $template, $resume, $Open_API_Key);
                $messageid = generateEmailId();

                $corporatemessageinfo = new stdClass();
                $_SESSION["corporatemessageinfo"] = createCorporateMessageInfo($corporatemessageinfo, $personname, $industryofinterest, $personnotes, $companynotes, $template, $resume, $messageid, $corporatemessage);

                //adding one to corporate email count
                $corporateemailcount = (int)getDatafromSQLResponse(["corporateemails"], executeSQL($conn, "SELECT * FROM users WHERE email=?", ["s"], [$encemail], "select", "nothing"));
                $corporateemailcount += 1;
                executeSQL($conn, "UPDATE users SET corporateemails=? WHERE email=?", ["i", "s"], [$corporateemailcount, $encemail], "update", "nothing");

                $deccorporatemessageinputsarr = [$messageid, $_SESSION["user"]->email, $personname, $personwebpage, $companywebpage, $corporatemessage, $resume, $template, "nothing", strtotime(date("Y-m-d H:i:s"))];
                executeSQL($conn, "INSERT INTO corporatemessages(messageid, useremail, personname, personwebpage, companywebpage, corporatemessagetext, resumename, templatename, rating1to10, datentimeinteger, iv) VALUES(?,?,?,?,?,?,?,?,?,?,?);", ["s", "s", "s", "s", "s", "s", "s", "s", "s", "s", "s"], array_merge(encryptDataGivenIv($deccorporatemessageinputsarr, $key, $_SESSION["user"]->iv), [$_SESSION["user"]->iv]), "insert", 10);

                echo "true";

            }

//then build the search features, first loading them and then search features (might have to separate messages as they don't have a subject or maybe just make an empty  subject column where you add nothing as well then make them formatted like it shows the first sentence)
//work on adding templates by default when you signup
//encrypting the pdf documents --> adding more security
        }
    }

    



    
?>
