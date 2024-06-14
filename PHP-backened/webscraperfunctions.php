<?php 

    function getHTMLfromlink($link) { 
        //curl html extraction that is done before so if a site timesout then we don't send it to nodejs for wasting more time and possibly closing the server
        $firstScrape = curl_init();
        curl_setopt($firstScrape, CURLOPT_URL, $link);
        curl_setopt($firstScrape, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($firstScrape, CURLOPT_TIMEOUT, 7);

        try {
            $fresponse = curl_exec($firstScrape);
        }
        catch (Exception $e) {
            echo "Error", $e ->getMessage(), "<br>";
            $fresponse = "error";
        }

        $position = strpos(curl_error($firstScrape), "Operation timed out");
        if ($position !== false) {
            $fresponse = "timeout";
        }

        curl_close($firstScrape);

        if ($fresponse != "timeout" && $fresponse != "error") {
            //nodejs html extraction
            $linkinfo = json_encode(array("link" => $link));
            $scrapeCurl = curl_init();
            curl_setopt($scrapeCurl, CURLOPT_URL, "http://localhost:3000/webscrape");
            curl_setopt($scrapeCurl, CURLOPT_POST, true);
            curl_setopt($scrapeCurl, CURLOPT_POSTFIELDS, $linkinfo);
            curl_setopt($scrapeCurl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($scrapeCurl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($scrapeCurl,CURLOPT_TIMEOUT,20);

            $response = ""; 

            try {
                $responseobj = json_decode(curl_exec($scrapeCurl));
                $response = $responseobj->html;
            }
            catch (Exception $e) {
                echo "Error", $e ->getMessage(), "<br>";
                $response ="error";
            }

            if (strpos(curl_error($scrapeCurl), "Operation timed out") !== false) {
                $response = "timeout";
            }

            curl_close($scrapeCurl);

            return $response;
        }
        else {
            return "error";
        }
    }

    function createDOMDocwLoadedHTML($html) {
        $dom = new DOMDocument();

        libxml_use_internal_errors(true);

        try {
            $dom->loadHTML($html);
        }
        catch (Exception $e) {
            echo "Error loading HTML: ", $e -> getMessage(), "<br>";
        }

        libxml_use_internal_errors(false);

        return $dom;
    }

    function getTagAnything($tagname, $htmldata, $property) {
        $tdom = createDOMDocwLoadedHTML($htmldata);
        $tag_info_array = array();

        libxml_use_internal_errors(true);

        if ($tdom->loadHTML($htmldata) !== false){
            $tags = $tdom -> getElementsByTagName($tagname);
            foreach ($tags as $tag) {
                if ($property == "text") {
                    $tag_info = $tag->textContent;
                }
                else {
                    $tag_info = $tag->getAttribute($property);
                }
                $tag_info_array[] = $tag_info;
            }
        }

        libxml_use_internal_errors(false);

        return $tag_info_array;
    }

    function anythingExtract($htmldata, $tags, $property) {
        $fullinfoarray = array();
        for($i=0; $i<count($tags); $i++) {
            $fullinfoarray[] = getTagAnything($tags[$i], $htmldata, $property);
        }

        return $fullinfoarray;
    }

    function implodeon2Darr($arr) {
        $imp_arr = array();
        for ($i=0;$i<count($arr);$i++) {
            $imp_arr[] = implode(" ", $arr[$i]);
        }

        return $imp_arr;
    }

    function removetagsfromHTML($tagsarr, $htmldata) {
        $rdom =  createDOMDocwLoadedHTML($htmldata);
        $cleanedhtml = "";

        libxml_use_internal_errors(true);

        if($rdom->loadHTML($htmldata) !== false) {
            foreach ($tagsarr as $tagname) {
                $tags = $rdom->getElementsByTagName($tagname);
                foreach ($tags as $tag) {
                    $tag->parentNode->removeChild($tag);
                }
            }

            $cleanedhtml = $rdom->saveHTML();
        }

        libxml_use_internal_errors(false);
        return $cleanedhtml;
    }

    function removeSpaceFromText($text) {
        //This function removed all spaces in between text as well as the text starting at random lines
        // /\s\s+/ replaces all that random white space between text with just a single space
        $cleanstr = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $text)));

        return $cleanstr;
    }

    function getAnythingFromHTML($link, $removetags, $tagsforextraction, $property) {
        $html = getHTMLfromlink($link);
        if($html == "error" or $html == "timeout") {
            return "";
        }
        $html = removetagsfromHTML($removetags, $html);
        $extractedanything = anythingExtract($html, $tagsforextraction, $property);

        $out = removeSpaceFromText(implode(" ", implodeon2Darr($extractedanything)));

        return $out;
    }

    function getLinksFromHTML($link, $removetags, $tagsforextraction, $property) {
        $html = getHTMLfromlink($link);
        if($html == "error" or $html == "timeout") {
            return "error";
        }
        $html = removetagsfromHTML($removetags, $html);
        $extractedanything = anythingExtract($html, $tagsforextraction, $property);
        $out = implode("<br>", getAllElementsin1Dfrom2Darr($extractedanything));

        return $out;
    }

    function givetwoImportantResearchLinks($profname, $text, $link, $API_KEY) {
        $everylinks = getLinksFromHTML($link, ['script'], ['a', 'button'], 'href');
        $prompt = "Given this text data from a website (link:".$link."): ".$text." and also these are the links of the site: ".$everylinks." where they are separated by a '<br>' tag. ||| Recognize that these links separated by '<br>' tags are extracted hrefs from anchor tags on the website. Get two links that you seem will provide the most information on ".$profname."'s research and professional background from ".$everylinks.". If this seems like a lab website, obtain two links out of all the available links that will be most likely about the lab's research, the professor's professional background and research projects. Answer to this prompt MUST BE IN THE FORM OF: LINK1|.|LINK2. DO NOT INCLUDE ANYTHING ELSE. Also remember for each of the two links, make sure to provide the full url like https://www.whatever.com. If the link you found is like a page on the website (most likely) itself then just add the website link:".$link." to it so it is a full url that leads to that page.";
        $prompt = substr($prompt, 0, 50000);
        $twolinks = communicatetoOpenAILLM("gpt-3.5-turbo-0125", "Assistant on a web application that extracts important information from html data", $prompt, $API_KEY); 
        $twolinksarr = explode('|.|', $twolinks);
        if(count($twolinksarr) != 2) {
            return "error";
        }
        else {
            return $twolinksarr;
        }
    }

    function getIvfortheDataRowwithXVar($table, $conn, $key, $attributesarr, $uservaluearr) {
        $encdataarr = getDatafromSQLResponse(array_merge($attributesarr, ["iv"]), executeSQL($conn, "SELECT * FROM ".$table, "nothing", "nothing", "select", "nothing"));
        $decdataarr = decryptFullData($encdataarr, $key, count($attributesarr));
        for($i=0; $i<count($decdataarr); $i++) {
            if(array_slice($decdataarr[$i], 0, count($attributesarr)) == $uservaluearr) {
                return $decdataarr[$i][count($attributesarr)];
                break;
            }
        }

        return "not found";
    }

    function createResearchEmailRequestObject($researchemailrequestobj, $professorname, $profwebpage, $pubweb, $template, $resume) {
        $researchemailrequestobj->professorname = $professorname;
        $researchemailrequestobj->professorwebpage = $profwebpage;
        $researchemailrequestobj->publicationwebpage = $pubweb;
        $researchemailrequestobj->template = $template;
        $researchemailrequestobj->resume = $resume;

        return $researchemailrequestobj;
    }

//create a function for just generally getting important research text (two specific types --> professor webpage and publication page) with in-built error handling
//create a function for creating notes on a professor or publication even when it is above the memory window the LLM of 16,483 tokens (split it up and keep processing)
//have an if(isset($_POST["textinput")) for this so if it is not set put nothing
    function generalErrorforUnsuccessfulScrape($type, $professorname, $link, $otherlink, $template, $resume) {
        $researchemailrequestobj = new stdClass();

        if($type == "professorwebpage") {
            $_SESSION["researchemailrequestobj"] = createResearchEmailRequestObject($researchemailrequestobj, $professorname, $link, $otherlink, $template, $resume);
        }
        else {
            $_SESSION["researchemailrequestobj"] = createResearchEmailRequestObject($researchemailrequestobj, $professorname, $otherlink, $link, $template, $resume);
        }

        if($type == "professorwebpage") {
            $_SESSION["profwebextraction-error"] = "true";
        }
        if($type == "publication") {
            $_SESSION["publicationextraction-error"] = "true";
        }

        echo "true";
        exit();
    }

    function getImportantResearchText($conn, $key, $link, $otherlink, $type, $textinput, $professorname, $template, $resume) {
        $decwebpagelinks = getSpecificAttributeDecryptedinList("linktowebsite", "webpages", $conn, $key);

        if(in_array($link, $decwebpagelinks)) {
            $iv = getIvfortheDataRowwithXVar("webpages", $conn, $key, ["linktowebsite"], [$link]);
            $enctext = getDatafromSQLResponse(["webtext", "iv"], executeSQL($conn, "SELECT webtext, iv FROM webpages WHERE linktowebsite=?", ["s"], [encryptSingleDataGivenIv([$link], $key, $iv)], "select", "nothing"));
            $text = getAllElementsin1Dfrom2Darr(deleteIndexesfrom2DArray(decryptFullData($enctext, $key, 1), [1]))[0];
            
            if(strlen($text)<30) {
                $textupdatenum = 0;
                if($type=="professorwebpage") {
                    if(isset($_POST["profwebpagetextinput"])) {
                        $textupdatenum += 1;
                        $text = $textinput;
                        executeSQL($conn, "UPDATE webpages SET webtext=? WHERE linktowebsite=?", ["s", "s"], encryptDataGivenIv([$textinput, $link], $key, $iv), "update", "nothing");
                    }
                }
                else {
                    if(isset($_POST["publicationtextinput"])){
                        $textupdatenum += 1;
                        $text = $textinput;
                        executeSQL($conn, "UPDATE webpages SET webtext=? WHERE linktowebsite=?", ["s", "s"], encryptDataGivenIv([$textinput, $link], $key, $iv), "update", "nothing");
                    }
                }
                
                if($textupdatenum == 0) {
                    generalErrorforUnsuccessfulScrape($type, $professorname, $link, $otherlink, $template, $resume);
                }
            }
        }
        else {
            $text = getAnythingFromHTML($link, ['head', 'header', 'footer', 'script'], ['body'], 'text');

            if(strlen($text)<30) {
                $textinputnum = 0;
                if($type=="professorwebpage") {
                    if(isset($_POST["profwebpagetextinput"])) {
                        $textinputnum += 1;
                        $text = $textinput;
                        executeSQL($conn, "INSERT INTO webpages(linktowebsite, webtext, iv) VALUES(?, ?, ?)", ["s", "s", "s"], array_merge(encryptDataGivenIv([$link, $textinput], $key, $_SESSION["user"]->iv), [$_SESSION["user"]->iv]), "insert", 2);
                    }
                }
                else {
                    if(isset($_POST["publicationtextinput"])){
                        $textinputnum += 1;
                        $text = $textinput;
                        executeSQL($conn, "INSERT INTO webpages(linktowebsite, webtext, iv) VALUES(?, ?, ?)", ["s", "s", "s"], array_merge(encryptDataGivenIv([$link, $textinput], $key, $_SESSION["user"]->iv), [$_SESSION["user"]->iv]), "insert", 2);
                    }
                }

                if($textinputnum == 0) {
                    generalErrorforUnsuccessfulScrape($type, $professorname, $link, $otherlink, $template, $resume);
                }
            }
        }

        return $text;
    }

    function textSplit($text, $maxcharacterlength, $maxoveralllength) {
        $text = substr($text, 0, $maxoveralllength);
    
        if(strlen($text)>$maxcharacterlength) {
            $textsplitarr = [];
    
            for($i=0; $i<strlen($text); $i=($i+$maxcharacterlength)) {
                $textsection = substr($text, $i, $maxcharacterlength);
                $textsplitarr[]= $textsection;
            }
        }
    
        return $textsplitarr;
    }

    function createNotes($model, $roleofsystem, $qprompt, $text, $API_Key) {
        $notesarr = [];

        if(strlen($text) > 10000) {
            $textarr = explode("|\.uejd/|", $text);
            $notesarr = [];

            for($g=0;$g<count($textarr);$g=$g+2){
                if(strlen($textarr[$g+1])> 10000) {
                    $partialsectnotesarr = [];
                    $textsplitarr = textSplit($textarr[$g+1], 10000, 100000);

                    for($i=0; $i<count($textsplitarr); $i++) {
                        $actualprompt = $qprompt." ".$textarr[$g]." ".$textsplitarr[$i];
                        $partialsectnotes = communicatetoOpenAILLM($model, $roleofsystem, $actualprompt, $API_Key);
                        $partialsectnotesarr[] = $partialsectnotes;
                    }

                    $notesarr[] = implode(" ", $partialsectnotesarr);
                }
                else {
                    $actualprompt = $qprompt." ".$textarr[$g].": ".$textarr[$g+1];
                    $notesarr[] = communicatetoOpenAILLM($model, $roleofsystem, $actualprompt, $API_Key);
                }
            }

            $notes = implode(" ", $notesarr);
        }
        else {
            $actualprompt = $qprompt." ".$text;
            $notes = communicatetoOpenAILLM($model, $roleofsystem, $actualprompt, $API_Key);
        }

        return $notes;
    }

    function makeNotesOnProfessor($profname, $proftext, $API_Key) {
        $qprompt = "Make very detailed notes on ".$profname."'s research work and professional experience based on the information provided. Also make sure to detail the specific research projects and works that ".$profname." has conducted or is currently being done in the lab. Write in the most detail as possible and don't miss any important information to ".$profname."'s research and experience as a researcher. As output, please just provide the notes. Here is text containing information about the ".$profname."'s research work and professional experience: ";
        
        $profnotes = createNotes("gpt-3.5-turbo-0125", "Assistant on a web application that performs effective note-taking given information", $qprompt, $proftext, $API_Key);

        return $profnotes;
    }

    function makeNotesonPublication($profname, $pubtext, $API_Key) {
        $qprompt = "Make very detailed note on ".$profname."'s publication. Write the notes in such a way that they capture all the information discussed in the publication (don't miss anything) and remember to outline the conclusions. Here is the text of the publication: ";

        $pubnotes = createNotes("gpt-3.5-turbo-0125", "Assistant on a web application that performs effective note-taking given information", $qprompt, $pubtext, $API_Key);

        return $pubnotes;
    }


    //$out = getAnythingFromHTML("https://www.sickkids.ca/en/staff/k/joseph-kuzma/", ['head', 'header', 'footer', 'script'], [ 'body'], 'text');

    //someone can insert bad notes which can make the writing bad
    //We will look into this later, probably cleaning databases if the notes don't look good using

    //echo strlen($out);

    //$openairesp = communicatetoOpenAILLM('gpt-3.5-turbo-0125', 'you are an assitant on a web application', "Given the following html:".$out."Extract all the relevant information about Joseph Kuzma. Write in point form and talk about his research and professional experience. Write the notes in a detailed manner.", $Open_API_Key);
    //echo $openairesp;

    // the rough UIs (two UIs -one for research and the other for corporate cold emails) so you can paste links with generate button
    // create a webscraper script in php that gets html data given the links and then concises them using gpt 3.5 - one for research, one for corporate
    // for research script, get two other links from the page that are relevant and concise them; for corporate try to get the personal website of the person if available and get html, otherwise don't do anything else
    // for both corporate and research scripts, save all the information in a SQL database
    // Using curl from both corporate and research scripts, we send the information to a write.php script, it uses gpt-4 to write emails given the data (separate scripts for corporate cold email and research cold email)
    // Save the email generated to SQL database (add to the total number of emails that can be created) and then return the email to the php file that called it, from that webscraper.php, send it to the front-end on javascript to put it there with regenerate --> have to figure out from there

    //have to have a cookies for regenerate function and keeping track of your 1st or 2nd attempt
    // when its number two, reload the page in php and even if they click regenerate it won't be parsed (store 1st and second attempt in data base so that no processing occurs)

    //general database of link and information for company websites, professors, publications and or linkedins (have to have type)
    // then another database for just storing the emails

    //Prompt engineering: extract all the relevant information about Amra Saric and her lab and her research work. Organize your information in point form.
    //The following is the text from which you need to extract the information:
    //Make your notes more detailed

    function communicatetoOpenAILLM($model1, $roleofsystem, $prompt1, $API_Key) {
        $headers = [
            "Content-Type: application/json",
            'Authorization: Bearer '.$API_Key,
        ];

        $model = $model1;

        $prompt = [
            "model" => $model,
            "messages" => array(
                array(
                    "role" => "system",
                    "content" => $roleofsystem
                ),
                array(
                    "role" => "user",
                    "content" => $prompt1
                )
            )
        ];
        
        $encoded_prompt = json_encode($prompt);

        $actualresponse = "";


        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded_prompt);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = json_decode(curl_exec($ch));

            $choices = $response->choices;
            $actualresponse = $choices[0]->message->content;

            curl_close($ch);
        }
        catch (Exception $e) {
            echo "Error building connection with LLM. Please try again later or contact our support team if the issue persists: ", $e -> getMessage(), "<br>";
        }

        if($actualresponse == null) {
            echo "Error: LLM could not be connected to. Please try again or if the issue persists, contact our support team<br>";

            print_r($response);
            exit();
        }

        return $actualresponse;
        
    }



    # get all the text using HTMLDom --> feed into GPT-3 turbo to extract all relevant information 
    # Process links using https: --> send GPT-3 links and ask which ones seem relevant to the following information (information it extracted) and tell it to predict what this link is for
?>