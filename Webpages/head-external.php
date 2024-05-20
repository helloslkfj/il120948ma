
<?php 
    if(session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if(isset($_SESSION["user"])) {
        header("Location: dashboard.php");
    }
?>
<!DOCTYPE html>

<html>
    <head>
        <?php include 'links.php';?>

        <!--include the html code for the header of the external pages like signup and login where the user is not supposed to be logged in -->
    </head>

    <body>



