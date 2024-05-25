<?php 
    if(isset($_SESSION["user"]) == false) {
        header("Location: login.php");
    } 
    else {
        include_once __DIR__.'/../../PHP-backened/secrets.php';
        include_once __DIR__.'/../../PHP-backened/commonfunctions.php';
        $encemail = encryptSingleDataGivenIv([$_SESSION["user"]->email], $key, $_SESSION["user"]->iv);
    }
?>