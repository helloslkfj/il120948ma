<?php 
    if(session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if(isset($_SESSION["user"])) {
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
    
        include 'database.php';
        include 'commonfunctions.php';
        include 'secrets.php';
        include 'signuploginfunctions.php';
        include 'webscraperfunctions.php';
    }
    else {
        exit("Not Authorized! We have reported you to the federal police regarding your attack attempt!");
    }
?>