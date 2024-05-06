<?php 

    function getHTMLfromlink($link) { 
        $scrapeCurl = curl_init();
        curl_setopt($scrapeCurl, CURLOPT_URL, value:$link);
        curl_setopt($scrapeCurl, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec($scrapeCurl);
    
        curl_close($scrapeCurl);

        return $response;
    }


    $html = getHTMLfromlink("https://www.uwo.ca/fhs/shs/about/faculty/smith_m.html");

    echo $html;

    $htmldom = new DOMDocument();
    $htmldom -> loadHTML($html);
    
    $script_tags = $htmldom -> getElementsByTagName('script');
    foreach ($script_tags as $script) {
        $script->parentNode->removeChild($script);
    }
    $newhtml = $htmldom->saveHTML();

    echo "|||||";

    echo $htmldom;



    $htmltextarray = str_split($html, 4000);

    #$Open_API_Key = "sk-proj-et1jVOi2bUBq59UJEuhjT3BlbkFJdvvCfjPPICZeRO1l97Gk";

    $headers = [
        "Content-Type: application/json",
        'Authorization: Bearer '.$Open_API_Key,
    ];

    $model = "gpt-4";

    $htmlprompt = [
        "model" => $model,
        "messages" => array(
            array(
                "role" => "system",
                "content" => "An assistant designed to extract text from HTML"
            ),
            array(
                "role" => "user",
                "content" => "Given the following HTML: {".$htmltextarray[0]."} Extract the text from HTML"
            )
        )
    ];

    $htmlprompt = json_encode($htmlprompt);

    $textextract = curl_init();

    curl_setopt($textextract, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
    curl_setopt($textextract, CURLOPT_POST, 1);
    curl_setopt($textextract, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($textextract, CURLOPT_POSTFIELDS, $htmlprompt);
    curl_setopt($textextract, CURLOPT_RETURNTRANSFER, true);

    $textofhtmlarray = curl_exec($textextract);
    curl_close($textextract);

    print_r($textofhtmlarray);


    for ($i; $i<count($htmltextarray); $i++) {

    };

    function getActrespfromarr ($response) {

    }



    # feed the response into some tool for extracting text from html 
    # after this, feed the text into chatgpt or azure for analyzing the person

    #Open AI key: sk-proj-et1jVOi2bUBq59UJEuhjT3BlbkFJdvvCfjPPICZeRO1l97Gk
?>