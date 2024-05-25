<?php 
    if(isset($_SESSION["user"]) == false) {
        exit("Error: Not logged in!"); 
    } 
    else {
        $encemail = encryptSingleDataGivenIv([$_SESSION["user"]->email], $key, $_SESSION["user"]->iv);
    }
?>