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
            $emailivdata_encrypted = getDatafromSQLResponse(["email", "iv"], executeSQL($conn, "SELECT email, iv FROM users", "nothing", "nothing", "select", "nothing"));
            $email_arr = collapse2DArrayto1D(decryptFullData($emailivdata_encrypted, $key, 1));
            

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


                //error handling is such that the fatal errors are posted on the signup screen notifying user that something has gone wrong; if insertion sql for user data failed then, they will see that they have to sign up again; for verfication insertion or mail fail then they will just resend verification as they will realize their number does not work or they didn't recieve it and they will know it has something to do with the error 
                
            }
            else {
                echo "false";
            }
        }
    }

?>