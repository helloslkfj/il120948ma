<?php 
    include_once __DIR__.'/header-backened-code/start-backened.php';
    include_once __DIR__.'/header-backened-code/headerincludes-backened.php';
    include_once __DIR__.'/header-backened-code/loginprotection-backened.php';
    include_once __DIR__.'/header-backened-code/nonverificationprotection-backened.php';
    

    //delete the current verification number and attempts in table
    //create new verification number and send mail
    //insert that number into a SQL request that then adds it to the table as a verification number with 0 attempts under the users  enc email

    executeSQL($conn, "DELETE FROM verification WHERE email=?;", ["s"], [$encemail], "delete", "nothing");

    $verificationcode = rand(1000000, 9999999);

    sendVerificationMail($_SESSION["user"], $verificationcode, $sendgridapi_key);

    $encverificationcode = encryptDataGivenIv([$verificationcode], $key, $_SESSION["user"]->iv)[0];
    $verificationsql = "INSERT INTO verification(email, verificationnum, attempts, iv) VALUES(?, ?, ?, ?)";
    executeSQL($conn, $verificationsql, ["s", "s", "s", "s"], [$encemail, $encverificationcode, 0, $_SESSION["user"]->iv], "insert", 3);

    echo "true";

?>