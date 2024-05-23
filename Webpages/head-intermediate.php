<?php 
    include 'head-code/start-code.php';

    if(isset($_SESSION["user"])) {
        include 'head-code/loginprotection-code.php';
        include 'head-html/head-internal-html.php';
    }
    else {
        include 'head-external.php';
    }
?>