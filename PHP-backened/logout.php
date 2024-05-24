<?php 
    //custom header
    include_once __DIR__.'/header-backened-code/start-backened.php';
    include_once __DIR__.'/header-backened-code/headerincludes-backened.php';
    include_once __DIR__.'/header-backened-code/loginprotection-backened.php';

    unset($_SESSION["user"]);
    unset($_SESSION["template"]);
?>

