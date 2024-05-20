<?php 
    include 'header-backened-friend.php';

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
                $signuparr = [$signupemail, $fname, $signuppassword, 'none', 'not active', date("Y-m-d")];
                $tencryptedsignuparr = encryptSetofData($signuparr, $key);
                $signupiv = $tencryptedsignuparr[0];
                $encryptedsignuparr = $tencryptedsignuparr[1];
                
                $signupsql = "INSERT INTO users(email, fname, pass, typofsubscription, subscriptionstat, dateandtime, iv) VALUES(?, ?, ?, ?, ?, ?, ?);";
                executeSQL($conn, $signupsql, ["s", "s", "s", "s", "s", "s", "s"], array_merge($encryptedsignuparr, [$signupiv]), "insert", 5);

                $userobject = new stdClass();
                $_SESSION["user"] = createUserObject($userobject, $fname, $signupemail, $signuppassword, 'none', 'not active');
            }
            else {
                echo "false";
            }
        }
    }

?>