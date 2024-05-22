<?php 
    //custom header
    include 'header-backened-code/start-backened.php';
    include 'header-backened-code/headerincludes-backened.php';
    include 'header-backened-code/loginprotection-backened.php';

    if(isset($_POST["verificationnum"])) {
        $encemailarr = encryptDataGivenIv([$_SESSION["user"]->email], $key, $_SESSION["user"]->iv);
        $encemail = $encemailarr[0];
        $verificationnum = mysqli_real_escape_string($conn, $_POST["verificationnum"]);

        $encverificationinfo =  getDatafromSQLResponse(["verificationnum", "attempts", "iv"], executeSQL($conn, "SELECT * FROM verification WHERE email='$encemail';", "nothing", "nothing", "select", "nothing"));
        if(count($encverificationinfo) > 1) {
            exit("Error occured in the database!");
        }
        $decverificationinfo = decryptSetofData($encverificationinfo[0], $key, 2);
        $numofattempts = (int)$decverificationinfo[1];

        if($numofattempts < 3) {
            $newattempts = $decverificationinfo[1] + 1;

            executeSQL($conn, "UPDATE verification SET attempts='$newattempts' WHERE email='$encemail';", "nothing", "nothing", "update", "nothing");
            //now check verification and if error then log the attempt and say how many remain
        }   
        else {
            echo "You have reached your maximum number of attempts. Please click resend verification.";
        }
        
        // select data first then update data;
        // if there is more than one record then exit() -> error occured

    }

?>
