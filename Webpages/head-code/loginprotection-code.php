<?php 
    if(isset($_SESSION["user"]) == false) {
        header("Location: login.php");
    }
?>