<?php 
    //custom header
    include_once __DIR__.'/header-backened-code/start-backened.php';
    include_once __DIR__.'/header-backened-code/headerincludes-backened.php';
    include_once __DIR__.'/header-backened-code/loginprotection-backened.php';

    if(isset($_POST["verificationnum"])) {
        $verificationnum = $_POST["verificationnum"];

        $encverificationinfo =  getDatafromSQLResponse(["verificationnum", "attempts", "iv"], executeSQL($conn, "SELECT * FROM verification WHERE email=?;", ["s"], [$encemail], "select", "nothing"));
        if(count($encverificationinfo) > 1) {
            exit("Error occured in the database!");
        }
        $decverificationinfo = decryptSetofData($encverificationinfo[0], $key, 2);
        $numofattempts = (int)$decverificationinfo[1];

        if($numofattempts < 3) {
            $newattempts = $numofattempts + 1;
            $encnewattempts = encryptSingleDataGivenIv([$newattempts], $key, $_SESSION["user"]->iv);

            executeSQL($conn, "UPDATE verification SET attempts=? WHERE email=?;", ["s", "s"], [$encnewattempts, $encemail], "update", "nothing");

            if((int)$verificationnum == (int)$decverificationinfo[0]) {
                $_SESSION["user"]->verification = 'true';
                $encverificationresult = encryptSingleDataGivenIv(['true'], $key, $_SESSION["user"]->iv);

                executeSQL($conn, "UPDATE users SET verification=? WHERE email=?;", ["s", "s"], [$encverificationresult, $encemail], "update", "nothing");

                echo "true";
            }
            else {
                $attemptsremaining = 3 - $newattempts;
                if($attemptsremaining > 1) {
                    $attemptwordending = "s";
                }
                else if ($attemptsremaining == 0) {
                    $attemptwordending = "s";
                }
                else {
                    $attemptwordending = "";
                }
                echo "You have ".$attemptsremaining." attempt".$attemptwordending." remaining";
            }
            //now check verification and if error then log the attempt and say how many remain
        }   
        else {
            echo "You have 0 attempts remaining. Please click resend verification.";
        }



        // select data first then update data;
        // if there is more than one record then exit() -> error occured

    }

?>
