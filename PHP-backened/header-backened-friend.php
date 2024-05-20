<?php 
    if(session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    include 'database.php';
    include 'commonfunctions.php';
    include 'secrets.php';
    include 'signuploginfunctions.php';
?>