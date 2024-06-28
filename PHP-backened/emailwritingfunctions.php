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

    function createCorporateEmailInfo($obj, $personname, $industryofinterest, $personnotes, $companynotes, $template, $resume, $emailid, $corporateemail, $corporateemailsubject) {
        $obj->personname = $personname;
        $obj->industryofinterest = $industryofinterest;
        $obj->personnotes = $personnotes;
        $obj->companynotes = $companynotes;
        $obj->template = $template;
        $obj->resume = $resume;
        $obj->emailid = $emailid;
        $obj->corporateemail = $corporateemail;
        $obj->corporateemailsubject = $corporateemailsubject;
        $obj->attempts=1;

        return $obj;
    }

    function createCorporateMessageInfo($obj, $personname, $industryofinterest, $personnotes, $companynotes, $template, $resume, $messageid, $corporatemessage) {
        $obj->personname = $personname;
        $obj->industryofinterest = $industryofinterest;
        $obj->personnotes = $personnotes; 
        $obj->companynotes = $companynotes;
        $obj->template = $template;
        $obj->resume = $resume;
        $obj->messageid = $messageid;
        $obj->corporatemessage = $corporatemessage;
        $obj->attempts=1;

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
        In the cold email, remember to be very detailed discussing the publication of ".$professorname." to show interest of the student in ".$professorname."'s work. Include the student's own experience involving research if present. If the research conducted by ".$userfname." is 
        related to ".$professorname."'s work then tie his/her/they work to the ".$professorname."'s work. If not, then talk about the skills the student acquired from that role and how that will help them when working under ".$professorname.". When writing the email, structure it using the template as a rough guideline 
        (be creative and make the email in a formal tone). In the template, 'I' refers to the student (".$userfname."). Make the email detailed and pretty long to show interest of the student. Whenever you write previous research experience, never say 'the University Name's lab' when 
        refering to the student working at a specific lab, always say the professor followed by the word 'lab' like 'Dr. Professor Name's lab'. DO NOT EVER ASSUME THAT A STUDENT'S RESEARCH WORK IS RELATED TO A PROFESSOR'S WORK UNLESS IT ACTUALLY IS!!! PAY ATTENTION TO THE DETAILS PRESENTED IN THE INFORMATION BELOW AND DON'T FORGET ABOUT THOSE DETAILS. 
        Only return the research email (do NOT include the subject). Make sure to NOT make any grammatical errors like RUN-ON sentences! Make sure to 
        keep your sentences clear and to the point as well as grammatically sound. 

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

    //regenerates full email without subject
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
        In the cold email, remember to be very detailed discussing the publication of ".$professorname." to show interest of the student in ".$professorname."'s work. Include the student's own experience involving research if present. If the research conducted by ".$userfname." is 
        related to ".$professorname."'s work then tie his/her/they work to the ".$professorname."'s work. If not, then talk about the skills the student acquired from that role and how that will help them when working under ".$professorname.". When writing the email, structure it using the template as a rough guideline 
        (be creative and make the email in a formal tone). In the template, 'I' refers to the student (".$userfname."). Make the email detailed and pretty long to show interest of the student. Whenever you write previous research experience, never say 'the University Name's lab' when 
        refering to the student working at a specific lab, always say the professor followed by the word 'lab' like 'Dr. Professor Name's lab'. DO NOT EVER ASSUME THAT A STUDENT'S RESEARCH WORK IS RELATED TO A PROFESSOR'S WORK UNLESS IT ACTUALLY IS!!! PAY ATTENTION TO THE DETAILS PRESENTED IN THE INFORMATION BELOW AND DON'T FORGET ABOUT THOSE DETAILS. 
        Only return the research email (do NOT include the subject). Make sure to NOT make any grammatical errors like RUN-ON sentences! Make sure to 
        keep your sentences clear and to the point as well as grammatically sound. 

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

    //creates the research emailsubject line
    function createResearchEmailSubject($userfname, $professorname, $researchemail, $API_Key) {
        $prompt = "Given the cold email from ".$userfname." to ".$professorname.", write a unique, perfect subject line for the cold email so that ".$userfname." can grab the attention of ".$professorname." and accomplish his/her/they goal. Make the subject line informative, concise and formal (but make it a little general as well like don't focus on a very specific research area, but rather the general research focus of the lab (the specific enzyme, specific disease, etc they study [if the lab has many general research focuses then don't refer to a research focus of the lab in the subject] as the lab might be working on other stuff than the specific subject in their publication). Just write the subject, do NOT explicitly state 'Subject'. Also no need to include '*'. Here is the cold email ".$researchemail;
        $roleofllm = "An assistant on a web application that analyzes personalized, detailed cold emails to researchers/professors from students and gives the perfect subject for the email so that the student catches the attention of the professor/researcher and accomplishes his/her task.";

        $researchemailsubject = communicatetoOpenAILLM("gpt-4o", $roleofllm, $prompt, $API_Key);

        return $researchemailsubject;
    }

    //creates the corporate email
    function createCorporateEmail($conn, $key, $encemail, $userfname, $personname, $industryofinterest, $personnotes, $companynotes, $template, $resume, $API_Key) {
        $exampleemail = "Hello Sachin,\n\n I hope you are doing well. My name is Jacob Osler and I am a second year student at the University of Western Ontario studying computer science. I am very passionate about generative AI, specifically LLMs, and how we can leverage this technology to create more productive enterprises. In the past, I have worked at a Machine Learning Research Intern at Dr. Tyrrell’s Lab where I developed a GAN that could generate realistic pediatric knee ultrasound images to make the lab’s training data for a knee recess distension classifier to be more balanced creating less bias. \n\n As a result of my previous experience and passion, I am very interested in working on Amazon’s generative AI product, Amazon Q. I found you on LinkedIn and saw that you are part of the Amazon Q team as a Software Development Engineer, taking a unique path of self-learning to get there. I would love to learn more about your journey and how to break into the field of generative AI. \n\n I am free anytime for an online meeting. Let me know what time and day works for you. \n\n Attached is my resume so you can learn more about my experiences and who I am.\n\n Sincerely, \n\n Jacob Osler";
        //get resume and template text

        $enctemplatetext = getDatafromSQLResponse(["textt", "iv"], executeSQL($conn, "SELECT textt, iv FROM templates WHERE email=? AND title=?", ["s", "s"], [$encemail, encryptSingleDataGivenIv([$template], $key, $_SESSION["user"]->iv)], "select", "nothing"));
        $templatetext = deleteIndexesfrom2DArray(decryptFullData($enctemplatetext, $key, 1), [1])[0][0];

        $encresumetext = getDatafromSQLResponse(["resumetext", "iv"], executeSQL($conn, "SELECT resumetext, iv FROM resumes WHERE email=? AND resumename=?", ["s","s"], [$encemail, encryptSingleDataGivenIv([$resume], $key, $_SESSION["user"]->iv)], "select", "nothing"));
        $resumetext = deleteIndexesfrom2DArray(decryptFullData($encresumetext, $key, 1), [1])[0][0];

        $prompt ="Given notes on ".$personname."'s professional/work experience, information about what a company that ".$personname." works/is involved with does, a resume containing information on ".$userfname."'s work experience, a template outlining how to structure a cold email and
        an example of a corporate cold email, write a personalized corporate cold email to ".$personname." from the perspective of ".$userfname." for the purpose outlined in the template (it will mostly either be networking or looking for opportunities at the company ,etc). 
        Remember to use the template for a guideline of how to structure the cold email and what the specific purpose is of the cold email (it will mostly either be networking or looking for opportunities at the company, etc). The corporate cold email should be concise, informative and clear. VERY IMPORTANT: Make sure 
        to show how ".$userfname." is very interested in the industry/market of ".$industryofinterest." and how that relates to the work that the company does. Moreover, make sure to include relevant experience of ".$userfname." in relation to ".$industryofinterest." if possible and then connect that to the company. If ".$userfname." 
        has no experience in relation to ".$industryofinterest." then just say what they have worked in (DO NOT relate this to the company's operations or ".$industryofinterest.") and how they want to get into ".$industryofinterest.". 
        Make the email concise (not too long). Only return the corporate email (do NOT include the subject). DO NOT JUST ASSUME THE INDUSTRY/FIELD that ".$userfname." wants to work in based on the resume. The industry/field that ".$userfname." wants to work in is ".$industryofinterest."! and this is what you should mention (don't add anything to this industry/field).
        
        Information:
        
        Notes on ".$personname."'s professional/work experience: 
        ".$personnotes."

        Notes on the company (gives the company name, what they do and what they are about):
        ".$companynotes."

        Resume of ".$userfname.":
        ".$resumetext."
        
        Template:
        The term 'I' in the template refers to the student/individual you are writing the email for (".$userfname."):
        ".$templatetext."

        Example corporate cold email:
        (do not use any of the information in this email to write the cold email for ".$userfname." as it is just an example and the information is not relevant to ".$userfname.", just use it for inspiration and appropriate guidance on how you should write).
        ".$exampleemail."

        "; //start writing the prompts for each of the functions and then you have bascially the generate corporate email and message good
        
        $roleofllm = "An assistant on a web application that writes personalized cold emails for users (mostly students) to help them build connections in the corporate world and land their dream jobs.";

        $corporateemailtext = communicatetoOpenAILLM("gpt-4o", $roleofllm, $prompt, $API_Key);

        return $corporateemailtext;
    }
    
    //function for regenerating the corporate cold email
    function regenerateCorporateEmail($conn, $key, $encemail, $userfname, $personname, $industryofinterest, $personnotes, $companynotes, $template, $resume, $oldcorporateemail, $API_Key) {
        $exampleemail = "Hello Sachin,\n\n I hope you are doing well. My name is Jacob Osler and I am a second year student at the University of Western Ontario studying computer science. I am very passionate about generative AI, specifically LLMs, and how we can leverage this technology to create more productive enterprises. In the past, I have worked at a Machine Learning Research Intern at Dr. Tyrrell’s Lab where I developed a GAN that could generate realistic pediatric knee ultrasound images to make the lab’s training data for a knee recess distension classifier to be more balanced creating less bias. \n\n As a result of my previous experience and passion, I am very interested in working on Amazon’s generative AI product, Amazon Q. I found you on LinkedIn and saw that you are part of the Amazon Q team as a Software Development Engineer, taking a unique path of self-learning to get there. I would love to learn more about your journey and how to break into the field of generative AI. \n\n I am free anytime for an online meeting. Let me know what time and day works for you. \n\n Attached is my resume so you can learn more about my experiences and who I am.\n\n Sincerely, \n\n Jacob Osler";

        $enctemplatetext = getDatafromSQLResponse(["textt", "iv"], executeSQL($conn, "SELECT textt, iv FROM templates WHERE email=? AND title=?", ["s", "s"], [$encemail, encryptSingleDataGivenIv([$template], $key, $_SESSION["user"]->iv)], "select", "nothing"));
        $templatetext = deleteIndexesfrom2DArray(decryptFullData($enctemplatetext, $key, 1), [1])[0][0];

        $encresumetext = getDatafromSQLResponse(["resumetext", "iv"], executeSQL($conn, "SELECT resumetext, iv FROM resumes WHERE email=? AND resumename=?", ["s","s"], [$encemail, encryptSingleDataGivenIv([$resume], $key, $_SESSION["user"]->iv)], "select", "nothing"));
        $resumetext = deleteIndexesfrom2DArray(decryptFullData($encresumetext, $key, 1), [1])[0][0];

        $prompt ="Given notes on ".$personname."'s professional/work experience, information about what a company that ".$personname." works/is involved with does, a resume containing information on ".$userfname."'s work experience, a template outlining how to structure a cold email and
        an example of a corporate cold email, write a personalized corporate cold email to ".$personname." from the perspective of ".$userfname." for the purpose outlined in the template (it will mostly either be networking or looking for opportunities at the company ,etc). 
        Remember to use the template for a guideline of how to structure the cold email and what the specific purpose is of the cold email (it will mostly either be networking or looking for opportunities at the company, etc). The corporate cold email should be concise, informative and clear. VERY IMPORTANT: Make sure 
        to show how ".$userfname." is very interested in the industry/market of ".$industryofinterest." and how that relates to the work that the company does. Moreover, make sure to include relevant experience of ".$userfname." in relation to ".$industryofinterest." if possible and then connect that to the company. If ".$userfname." 
        has no experience in relation to ".$industryofinterest." then just say what they have worked in (DO NOT relate this to the company's operations or ".$industryofinterest.") and how they want to get into ".$industryofinterest.". 
        Make the email concise (not too long). Only return the corporate email (do NOT include the subject). DO NOT JUST ASSUME THE INDUSTRY that ".$userfname." wants to work in based on the resume. The industry that ".$userfname." wants to work in is ".$industryofinterest."!
        
        Information:
        
        Notes on ".$personname."'s professional/work experience: 
        ".$personnotes."

        Notes on the company (gives the company name, what they do and what they are about):
        ".$companynotes."

        Resume of ".$userfname.":
        ".$resumetext."
        
        Template:
        The term 'I' in the template refers to the student/individual you are writing the email for (".$userfname."):
        ".$templatetext."

        Example corporate cold email:
        (do not use any of the information in this email to write the cold email for ".$userfname." as it is just an example and the information is not relevant to ".$userfname.", just use it for inspiration and appropriate guidance on how you should write).
        ".$exampleemail."
        
        ".$userfname." did not like this email you created: ".$oldcorporateemail."\n\n Please make this email much better than the one above.
        "; //start writing the prompts for each of the functions and then you have bascially the generate corporate email and message good
        
        $roleofllm = "An assistant on a web application that writes personalized cold emails for users (mostly students) to help them build connections in the corporate world and land their dream jobs.";

        $regeneratedcorporateemailtext = communicatetoOpenAILLM("gpt-4o", $roleofllm, $prompt, $API_Key);

        return $regeneratedcorporateemailtext;
    }

    //creates the corporate email subject
    function createCorporateEmailSubject($userfname, $personname, $corporateemail, $API_Key){
        $prompt = "Given the cold email from ".$userfname." to ".$personname.", write a unique, perfect subject line for the cold email so that ".$userfname." can grab the attention of ".$personname." and accomplish his/her/they goal. Make the subject line informative, concise and formal. Just write the subject, do NOT explicitly state 'Subject'. Also no need to include '*'. Here is the cold email ".$corporateemail;

        $roleofllm = "An assistant on a web application that writes personalized cold emails for users (mostly students) to help them build connections in the corporate world and land their dream jobs.";

        $corporateemailsubjectline = communicatetoOpenAILLM("gpt-4o", $roleofllm, $prompt, $API_Key);

        return $corporateemailsubjectline;
    } 

    //creates linkedin message 
    function createLinkedInMessage($conn, $key, $encemail, $userfname, $personname, $industryofinterest, $personnotes, $companynotes, $template, $resume, $API_Key) {
        $examplemessage = "Hello Sachin, \n\n I am a second year student at the University of Western Ontario, studying computer science. I am passionate about generative AI, specifically LLMs, and have worked as a ML research intern at a University of Toronto laboratory to generate realistic pediatric knee ultrasound images. \n\n I saw that you work as SDE at Amazon Q and was wondering if we could have a meeting to discuss your journey and how you broke into the field of generative AI.";

        $roleofllm = "An assistant on a web application that writes personalized linkedin messages for users (mostly students) to help them build connections in the corporate world and land their dream jobs.";

        $enctemplatetext = getDatafromSQLResponse(["textt", "iv"], executeSQL($conn, "SELECT textt, iv FROM templates WHERE email=? AND title=?", ["s", "s"], [$encemail, encryptSingleDataGivenIv([$template], $key, $_SESSION["user"]->iv)], "select", "nothing"));
        $templatetext = deleteIndexesfrom2DArray(decryptFullData($enctemplatetext, $key, 1), [1])[0][0];

        $encresumetext = getDatafromSQLResponse(["resumetext", "iv"], executeSQL($conn, "SELECT resumetext, iv FROM resumes WHERE email=? AND resumename=?", ["s","s"], [$encemail, encryptSingleDataGivenIv([$resume], $key, $_SESSION["user"]->iv)], "select", "nothing"));
        $resumetext = deleteIndexesfrom2DArray(decryptFullData($encresumetext, $key, 1), [1])[0][0];

        $prompt = "Given notes on ".$personname."'s professional/work experience, information about what a company that ".$personname." works/is involved with does, a resume containing information on ".$userfname."'s work experience, a template outlining how to structure a LinkedIn message and
        an example of a corporate cold email, write a personalized LinkedIn message to ".$personname." from the perspective of ".$userfname." for the purpose outlined in the template (it will mostly either be networking or looking for opportunities at the company ,etc). 
        Remember to use the template for a guideline of how to structure the LinkedIn message and what the specific purpose is of the LinkedIn message (it will mostly either be networking or looking for opportunities at the company, etc). The LinkedIn message should be very concise (300-400 characters thats it!), informative and clear. VERY IMPORTANT: Make sure 
        to show how ".$userfname." is very interested in the industry/market of ".$industryofinterest." and how that relates to the work that the company does. Moreover, make sure to include relevant experience of ".$userfname." in relation to ".$industryofinterest." if possible and then connect that to the company. If ".$userfname." 
        has no experience in relation to ".$industryofinterest." then just say what they have worked in (DO NOT relate this to the company's operations or ".$industryofinterest.") and how they want to get into ".$industryofinterest.". 
        Make the LinkedIn message concise (short [300 TO 400 CHARACTERS!]). DO NOT add regards, sincerely, etc for ending, just end it off unless stated in the template. Make it very short but engaging and personable so ".$personname." is more likely to respond back to ".$userfname.". Make the message extremly personalized by doing things like mentioning ".$personname."'s position at the company, etc. 
        DO NOT JUST ASSUME THE INDUSTRY that ".$userfname." wants to work in based on the resume. The industry that ".$userfname." wants to work in is ".$industryofinterest."! Do not include that many adjectives, keep them limited and get to the point quickly. Also when mentioning the experience or 
        profile of ".$personname.", remember to list their specific position and what they work in and how that is interesting to ".$userfname.". Do not assume what ".$personname." has done or accomplished, make sure to ONLY use the information provided!
        
        Information:
        
        Notes on ".$personname."'s professional/work experience: 
        ".$personnotes."

        Notes on the company (gives the company name, what they do and what they are about):
        ".$companynotes."

        Resume of ".$userfname.":
        ".$resumetext."
        
        Template:
        The term 'I' in the template refers to the student/individual you are writing the email for (".$userfname."):
        ".$templatetext."

        Example of a LinkedIn Message:
        (do not use any of the information in this message to write the message for ".$userfname." as it is just an example and the information is not relevant to ".$userfname.", just use it for inspiration and appropriate guidance on how you should write).
        ".$examplemessage;

        $linkedInMessagetext = communicatetoOpenAILLM("gpt-4o", $roleofllm, $prompt, $API_Key); 

        return $linkedInMessagetext;
    }

    //function for regenerating corporate/linkedin message
    function regenerateLinkedInMessage($conn, $key, $encemail, $userfname, $personname, $industryofinterest, $personnotes, $companynotes, $template, $resume, $oldlinkedinmessage, $API_Key) {
        $examplemessage = "Hello Sachin, \n\n I am a second year student at the University of Western Ontario, studying computer science. I am passionate about generative AI, specifically LLMs, and have worked as a ML research intern at a University of Toronto laboratory to generate realistic pediatric knee ultrasound images. \n\n I saw that you work as SDE at Amazon Q and was wondering if we could have a meeting to discuss your journey and how you broke into the field of generative AI.";

        $roleofllm = "An assistant on a web application that writes personalized linkedin messages for users (mostly students) to help them build connections in the corporate world and land their dream jobs.";

        $enctemplatetext = getDatafromSQLResponse(["textt", "iv"], executeSQL($conn, "SELECT textt, iv FROM templates WHERE email=? AND title=?", ["s", "s"], [$encemail, encryptSingleDataGivenIv([$template], $key, $_SESSION["user"]->iv)], "select", "nothing"));
        $templatetext = deleteIndexesfrom2DArray(decryptFullData($enctemplatetext, $key, 1), [1])[0][0];

        $encresumetext = getDatafromSQLResponse(["resumetext", "iv"], executeSQL($conn, "SELECT resumetext, iv FROM resumes WHERE email=? AND resumename=?", ["s","s"], [$encemail, encryptSingleDataGivenIv([$resume], $key, $_SESSION["user"]->iv)], "select", "nothing"));
        $resumetext = deleteIndexesfrom2DArray(decryptFullData($encresumetext, $key, 1), [1])[0][0];

        $prompt = "Given notes on ".$personname."'s professional/work experience, information about what a company that ".$personname." works/is involved with does, a resume containing information on ".$userfname."'s work experience, a template outlining how to structure a LinkedIn message and
        an example of a corporate cold email, write a personalized LinkedIn message to ".$personname." from the perspective of ".$userfname." for the purpose outlined in the template (it will mostly either be networking or looking for opportunities at the company ,etc). 
        Remember to use the template for a guideline of how to structure the LinkedIn message and what the specific purpose is of the LinkedIn message (it will mostly either be networking or looking for opportunities at the company, etc). The LinkedIn message should be very concise (300-400 characters thats it!), informative and clear. VERY IMPORTANT: Make sure 
        to show how ".$userfname." is very interested in the industry/market of ".$industryofinterest." and how that relates to the work that the company does. Moreover, make sure to include relevant experience of ".$userfname." in relation to ".$industryofinterest." if possible and then connect that to the company. If ".$userfname." 
        has no experience in relation to ".$industryofinterest." then just say what they have worked in (DO NOT relate this to the company's operations or ".$industryofinterest.") and how they want to get into ".$industryofinterest.". 
        Make the LinkedIn message concise (short [300 TO 400 CHARACTERS!]). DO NOT add regards, sincerely, etc for ending, just end it off unless stated in the template. Make it very short but engaging and personable so ".$personname." is more likely to respond back to ".$userfname.". Make the message extremly personalized by doing things like mentioning ".$personname."'s position at the company, etc. 
        DO NOT JUST ASSUME THE INDUSTRY that ".$userfname." wants to work in based on the resume. The industry that ".$userfname." wants to work in is ".$industryofinterest."! Do not include that many adjectives, keep them limited and get to the point quickly. Also when mentioning the experience or 
        profile of ".$personname.", remember to list their specific position and what they work in and how that is interesting to ".$userfname.". Do not assume what ".$personname." has done or accomplished, make sure to ONLY use the information provided!
        
        Information:
        
        Notes on ".$personname."'s professional/work experience: 
        ".$personnotes."

        Notes on the company (gives the company name, what they do and what they are about):
        ".$companynotes."

        Resume of ".$userfname.":
        ".$resumetext."
        
        Template:
        The term 'I' in the template refers to the student/individual you are writing the email for (".$userfname."):
        ".$templatetext."

        Example of a LinkedIn Message:
        (do not use any of the information in this message to write the message for ".$userfname." as it is just an example and the information is not relevant to ".$userfname.", just use it for inspiration and appropriate guidance on how you should write).
        ".$examplemessage."
        
        ".$userfname." did not like this LinkedIn message you created: ".$oldlinkedinmessage." 

        Please try to write this linkedin message much better than one above.";

        $linkedInMessagetext = communicatetoOpenAILLM("gpt-4o", $roleofllm, $prompt, $API_Key); 

        return $linkedInMessagetext;
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