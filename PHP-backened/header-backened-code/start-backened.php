<?php 
    if(session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    date_default_timezone_set('America/New_York');
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
?>