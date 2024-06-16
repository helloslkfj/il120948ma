<?php 
    
    function createResearchEmailInfo($obj, $professorname, $profnotes, $pubnotes, $template, $resume, $emailid, $researchemail, $researchemailsubject) {
        $obj->professorname = $professorname;
        $obj->professornotes = $profnotes;
        $obj->publicationnotes = $pubnotes;
        $obj->template = $template;
        $obj->resume = $resume;
        $obj->emailid = $emailid;
        $obj->researchemail = $researchemail;
        $obj->researchemailsubject = $researchemailsubject;
        $obj->attempts = 1;

        return $obj;
    }

    //creates the full research email without the subject
    function createResearchEmail($conn, $key, $encemail, $userfname, $professorname, $profnotes, $pubnotes, $template, $resume, $API_Key) {
        $exampleemail = "Hello Dr. Oram Cardy, I hope you are doing well. I find the Autism Spectrum & Language Disorders Lab's focus on the neural, perceptual and cognitive markers of language ability and disability in children very interesting. 
        \nI am a medical sciences and scholar's electives student at the University of Western Ontario as well as a national scholar (Beryl Ivey National Scholarship holder). Personally, I am very passionate about learning more about the factors 
        underlying language disability within children. \nI was wondering if we could have a meeting to discuss the possibility of you being my supervisor in the summer for the USRI, NSERC USRA and DUROP awards. Moreover, I am also interested in 
        volunteering at the Autism Spectrum & Language Disorders Lab, if there are any positions available. I am open to having the meeting either online or in-person. \nI am available every day after 1:30 pm except Monday and Tuesday where I am available after 6:30 pm. 
        Attached is my resume as well as a published chapter of a book that I worked on. Best, Student Name";
        //can possibly add functions for this where you have a WHERE statement
        $enctemplatetext = getDatafromSQLResponse(["textt", "iv"], executeSQL($conn, "SELECT textt, iv FROM templates WHERE email=? AND title=?", ["s", "s"], [$encemail, encryptSingleDataGivenIv([$template], $key, $_SESSION["user"]->iv)], "select", "nothing"));
        $templatetext = deleteIndexesfrom2DArray(decryptFullData($enctemplatetext, $key, 1), [1])[0][0];

        $encresumetext = getDatafromSQLResponse(["resumetext", "iv"], executeSQL($conn, "SELECT resumetext, iv FROM resumes WHERE email=? AND resumename=?", ["s","s"], [$encemail, encryptSingleDataGivenIv([$resume], $key, $_SESSION["user"]->iv)], "select", "nothing"));
        $resumetext = deleteIndexesfrom2DArray(decryptFullData($encresumetext, $key, 1), [1])[0][0];

        $prompt = "Given notes on ".$professorname."'s research/work experience, information on his/her/they publication, 
        a template for how the cold email should be structured and the".$userfname."'s resume text (everything given below), write a personalized cold email from the perspective 
        of ".$userfname."(student) to ".$professorname."(researcher) with the purpose of getting a research position at ".$professorname."'s lab. In the cold email, since it 
        is from the perspective of a student remember to use an appropriate honorific like (Dr, Professor, etc) along with the name when refering to ".$professorname.". 
        In the cold email, remember to be very detailed discussing the publication of ".$professorname." to show interest of the student in ".$professorname."'s work. Include the student's own experience and how that ties into ".$professorname."'s work. 
        When writing the email, structure it using the template as a rough guideline (be creative and make the email in a formal tone). In the template, 'I' refers to the student (".$userfname."). Make the email detailed and pretty long to show 
        interest of the student. Whenever you write previous research experience, never say 'the University Name's lab' when refering to the student working at a specific lab, always say the professor followed by the word 'lab' like 'Dr. Professor Name's lab'. 
        Only return the research email (do NOT include the subject). 

        Information:

        Notes on".$professorname."'s research/work experience:
        ".$profnotes."
        
        Notes on ".$professorname."'s publication:
        ".$pubnotes."
        
        Resume of ".$userfname.":
        ".$resumetext."
        
        Template of Cold Email:
        ".$templatetext." Use this as an example email for how you should write it 
        (do not use any of the information in this email to write the cold email for the student as it is just an example and the information is not relevant to 
        the student, just use it for inspiration and appropriate guidance on how you should write): ".$exampleemail ; 

        $roleofllm = "An assistant on a web application that writes personalized, detailed cold emails to researchers/professors for students";

        $researchemailtext = communicatetoOpenAILLM("gpt-4o", $roleofllm, $prompt, $API_Key);

        return $researchemailtext;
    }

    function regenerateResearchEmail($conn, $key, $encemail, $userfname, $professorname, $profnotes, $pubnotes, $template, $resume, $oldemail, $API_Key) {
        $exampleemail = "Hello Dr. Oram Cardy, I hope you are doing well. I find the Autism Spectrum & Language Disorders Lab's focus on the neural, perceptual and cognitive markers of language ability and disability in children very interesting. 
        \nI am a medical sciences and scholar's electives student at the University of Western Ontario as well as a national scholar (Beryl Ivey National Scholarship holder). Personally, I am very passionate about learning more about the factors 
        underlying language disability within children. \nI was wondering if we could have a meeting to discuss the possibility of you being my supervisor in the summer for the USRI, NSERC USRA and DUROP awards. Moreover, I am also interested in 
        volunteering at the Autism Spectrum & Language Disorders Lab, if there are any positions available. I am open to having the meeting either online or in-person. \nI am available every day after 1:30 pm except Monday and Tuesday where I am available after 6:30 pm. 
        Attached is my resume as well as a published chapter of a book that I worked on. Best, Student Name";
        //can possibly add functions for this where you have a WHERE statement
        $enctemplatetext = getDatafromSQLResponse(["textt", "iv"], executeSQL($conn, "SELECT textt, iv FROM templates WHERE email=? AND title=?", ["s", "s"], [$encemail, encryptSingleDataGivenIv([$template], $key, $_SESSION["user"]->iv)], "select", "nothing"));
        $templatetext = deleteIndexesfrom2DArray(decryptFullData($enctemplatetext, $key, 1), [1])[0][0];

        $encresumetext = getDatafromSQLResponse(["resumetext", "iv"], executeSQL($conn, "SELECT resumetext, iv FROM resumes WHERE email=? AND resumename=?", ["s","s"], [$encemail, encryptSingleDataGivenIv([$resume], $key, $_SESSION["user"]->iv)], "select", "nothing"));
        $resumetext = deleteIndexesfrom2DArray(decryptFullData($encresumetext, $key, 1), [1])[0][0];

        $prompt = "Given notes on ".$professorname."'s research/work experience, information on his/her/they publication, 
        a template for how the cold email should be structured and the".$userfname."'s resume text (everything given below), write a personalized cold email from the perspective 
        of ".$userfname."(student) to ".$professorname."(researcher) with the purpose of getting a research position at ".$professorname."'s lab. In the cold email, since it 
        is from the perspective of a student remember to use an appropriate honorific like (Dr, Professor, etc) along with the name when refering to ".$professorname.". 
        In the cold email, remember to be very detailed discussing the publication of ".$professorname." to show interest of the student in ".$professorname."'s work. Include the student's own experience and how that ties into ".$professorname."'s work. 
        When writing the email, structure it using the template as a rough guideline (be creative and make the email in a formal tone). In the template, 'I' refers to the student (".$userfname."). Make the email detailed and pretty long to show 
        interest of the student. Whenever you write previous research experience, never say 'the University Name's lab' when refering to the student working at a specific lab, always say the professor followed by the word 'lab' like 'Dr. Professor Name's lab'. 
        Only return the research email (do NOT include the subject). 

        Information:

        Notes on".$professorname."'s research/work experience:
        ".$profnotes."
        
        Notes on ".$professorname."'s publication:
        ".$pubnotes."
        
        Resume of ".$userfname.":
        ".$resumetext."
        
        Template of Cold Email:
        ".$templatetext." Use this as an example email for how you should write it 
        (do not use any of the information in this email to write the cold email for the student as it is just an example and the information is not relevant to 
        the student, just use it for inspiration and appropriate guidance on how you should write): ".$exampleemail. "\n The user did not like this email you created:".$oldemail."\n Try to make this email much better than the one above." ; 

        $roleofllm = "An assistant on a web application that writes personalized, detailed cold emails to researchers/professors for students";

        $researchemailtext = communicatetoOpenAILLM("gpt-4o", $roleofllm, $prompt, $API_Key);

        return $researchemailtext;
    }

    function createResearchEmailSubject($userfname, $professorname, $researchemail, $API_Key) {
        $prompt = "Given the cold email from ".$userfname." to ".$professorname.", write a unique, perfect subject line for the cold email so that ".$userfname." can grab the attention of ".$professorname." and accomplish his/her/they goal. Make the subject line informative, concise and formal (but make it a little general as well like don't focus on a very specific research area, but rather the general research focus of the lab (the specific enzyme, specific disease, etc they study [if the lab has many general research focuses then don't refer to a research focus of the lab in the subject] as the lab might be working on other stuff than the specific subject in their publication). Just write the subject, do NOT explicitly state 'Subject'. Also no need to include '*'. Here is the cold email ".$researchemail;
        $roleofllm = "An assistant on a web application that analyzes personalized, detailed cold emails to researchers/professors from students and gives the perfect subject for the email so that the student catches the attention of the professor/researcher and accomplishes his/her task.";

        $researchemailsubject = communicatetoOpenAILLM("gpt-4o", $roleofllm, $prompt, $API_Key);

        return $researchemailsubject;
    }

    function getAlphabet($index) {
        $alphabetarr = range('a', 'z');
        return $alphabetarr[$index];
    }

    function generateEmailId() {
        $emailidarr = [];
        for($i=0;$i<30;$i++) {
            $decide = rand(0, 9);
            if($decide < 5) {
                $emailidarr[] = getAlphabet(rand(0,25));
            } else {
                $emailidarr[] = rand(0,9);
            }
        }

        $emailid = implode("", $emailidarr);
        return $emailid;
    }
?>