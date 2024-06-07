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
        if (strpos(curl_error($firstScrape), "Operation timed out") !== false) {
            $fresponse = "timeout";
        }

        curl_close($firstScrape);

        if ($fresponse != "timeout" or $fresponse != "error") {
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
            return "Error: Could not obtain HTML data";
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

    function makeNotesOnProfessor($profname, $proftext, $API_KEY) {
        $prompt = "Make very detailed notes on ".$profname."'s research work and professional experience. Also make sure to detail the specific research projects and works that ".$profname." has conducted or is currently being done in the lab. Write in the most detail as possible and don't miss any important information to ".$profname."'s research and experience as a researcher. As output, please just provide the notes.";
        $profnotes = communicatetoOpenAILLM("gpt-3.5-turbo-0125", "Assistant on a web application that performs effective note-taking given information", $prompt, $API_KEY);
        if($profnotes == null) {
            echo "Error: LLM could not be connected to. Please try again or if the issue persists, contact our support team";
            exit();
        }

        return $profnotes;
    }

    //$out = getAnythingFromHTML("https://www.sickkids.ca/en/staff/k/joseph-kuzma/", ['head', 'header', 'footer', 'script'], [ 'body'], 'text');


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
            echo "Error building connection with LLM. Please try again later or contact our support team if the issue persists: ", $e -> getMessage();
        }

        return $actualresponse;
        
    }



    # get all the text using HTMLDom --> feed into GPT-3 turbo to extract all relevant information 
    # Process links using https: --> send GPT-3 links and ask which ones seem relevant to the following information (information it extracted) and tell it to predict what this link is for
?>