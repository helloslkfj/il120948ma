<?php 
    include_once __DIR__.'/header-backened-friend.php';

    $error = 0;

    if(isset($_POST['firstname'])) {
        $fname = mysqli_escape_string($conn, $_POST['firstname']);
        $fnamelen = strlen($fname);
        if($fnamelen < 2) {
            $error += 1;
            echo "You must input a first name that is at least two characters in length";
        }
    }
    else {
        $error += 1;
    }

    if(isset($_POST['signupemail'])) {
        $signupemail = mysqli_escape_string($conn, $_POST['signupemail']);

        if(filter_var($signupemail, FILTER_VALIDATE_EMAIL) != true) {
            echo "Email is invalid";
            $error += 1;
        }
        else {
            $email_arr = getSpecificAttributeDecryptedinList("email", "users", $conn, $key);
            
            if(in_array($signupemail, $email_arr)) {
                echo "This email is already signed up";
                $error +=1;
            }
        }
    }
    else {
        $error += 1;
    }

    if(isset($_POST['signuppassword'])) {
        $passerror = 0;
        $passerrormess = "Minimum length is 8<br> Need 1 symbol: #,-,?,),(,*,@<br>Need 1 capital letter<br>";
        $matcherrormess = "The passwords do not match";
        $signuppassword = mysqli_real_escape_string($conn, $_POST['signuppassword']);

        if(strlen($signuppassword) < 8) {
            $passerror += 1;
        }
        
        $snum = 0;
        $symbolarr = ['#','_','?',')','(','*','@', ','];
        foreach ($symbolarr as $symbol) {
            if(str_contains($signuppassword, $symbol) == true) {
                $snum += 1;
            }
        }
        if($snum < 1) {
            $passerror +=1;
        }

        if(preg_match('/[A-Z]/', $signuppassword) != true) {
            $passerror += 1;
        }

        if(isset($_POST['signuprepassword'])) {
            $signuprepassword = mysqli_real_escape_string($conn, $_POST['signuprepassword']);
            if($signuprepassword != $signuppassword) {
                $error += 1;
                echo $matcherrormess;
            }

            if ($passerror > 0) {
                $error += 1;
            }
        }
        else {
            $error +=1;
            if ($passerror > 0) {
                echo $passerrormess;
            }

        }
    }
    else {
        $error +=1;
    }

    if(isset($_POST['signupsubmit'])) {
        $signupsubmit = mysqli_escape_string($conn, $_POST['signupsubmit']);
        if($signupsubmit == true) {
            if ($error < 1) {
                echo "true";
                $signuparr = [$signupemail, $fname, $signuppassword, 'false', 'none', 'not active', date("Y-m-d")];
                $tencryptedsignuparr = encryptSetofData($signuparr, $key);
                
                $signupsql = "INSERT INTO users(email, fname, pass, verification, typofsubscription, subscriptionstat, dateandtime, iv) VALUES(?, ?, ?, ?, ?, ?, ?, ?);";
                executeSQL($conn, $signupsql, ["s", "s", "s", "s", "s", "s", "s", "s"], array_merge($tencryptedsignuparr[1], [$tencryptedsignuparr[0]]), "insert", 7);

                $userobject = new stdClass();
                $_SESSION["user"] = createUserObject($userobject, $fname, $signupemail, $signuppassword, 'false', 'none', 'not active', $tencryptedsignuparr[0]);
                

                $verificationcode = rand(1000000, 9999999);
                sendVerificationMail($_SESSION["user"], $verificationcode, $sendgridapi_key);

                $verificationsql = "INSERT INTO verification(email, verificationnum, attempts, iv) VALUES(?, ?, ?, ?);";
                $verificationarr = [$signupemail, $verificationcode, 0];

                $encverificationarr = encryptDataGivenIv($verificationarr, $key, $tencryptedsignuparr[0]);
                executeSQL($conn, $verificationsql, ["s", "s", "s", "s"], array_merge($encverificationarr, [$tencryptedsignuparr[0]]), "insert", 3);

                $basictemplatetext = "- Say hello to the researcher/professor

                1st Paragraph:
                - Discuss how I am very interested in their general area of research (1 sentence)
                - Then mention their publication and start discussing specifically what I found interesting (2-3 sentences)

                2nd Paragraph:
                - Introduce me and where I am currently studying (if in university include my degree major/specialization or if in high school, just say that I am a high school student then the name of the high school) (1 sentence)
                - Talk about 1 or 2 specific experiences which are aligned  with the researcher/professor's research work (if there is no direct aligned experience, write about experience that shows you will be helpful to the professor) (1-2 sentences)
                - Indicate what I want to work on in the future (just choose one of the main focuses of the lab so I seem aligned with the researcher and professor)

                3rd Paragraph:
                - Ask for a volunteer position at the lab and ask if they are free to meet to discuss this further

                4th Paragraph:
                - End it off with Regards, myname ";
                $templatearr = [$signupemail, "Basic Research Email Template", $basictemplatetext, strtotime(date("Y-m-d H:i:s"))]; //need to make this more of a proper template, but this is the basic research template inititalization
                $enctemplatearr = encryptDataGivenIv($templatearr,$key, $tencryptedsignuparr[0]);
                executeSQL($conn, "INSERT INTO templates(email, title, textt, datentimeinteger, iv) VALUES(?,?,?,?,?)", ["s","s","s","s","s"], array_merge($enctemplatearr, [$tencryptedsignuparr[0]]), "insert", 4);

                //error handling is such that the fatal errors are posted on the signup screen notifying user that something has gone wrong; if insertion sql for user data failed then, they will see that they have to sign up again; for verfication insertion or mail fail then they will just resend verification as they will realize their number does not work or they didn't recieve it and they will know it has something to do with the error 
                
            }
            else {
                echo "false";
            }
        }
    }

?>