<?php 
    session_start();

    include 'signuploginfunctions.php';
    include 'secrets.php';


    if (isset($_SESSION["user"])){
        sendVerificationMail($_SESSION["user"], 'skfj3003', $sendgridapi_key);
    }

?>