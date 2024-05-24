<?php 
    include_once __DIR__.'/head-code/start-code.php';

    if(isset($_SESSION["user"])) {
        include_once 'head-code/loginprotection-code.php';
        include_once 'head-html/head-internal-html.php';
    }
    else {
        include_once 'head-external.php';
    }
?>