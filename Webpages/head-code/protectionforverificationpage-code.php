<?php 
    if(isset($_SESSION["user"])){
        if($_SESSION["user"]->verification == 'true') {
            header("Location: dashboard.php?verification=You have been verified");
        }   
    }
?>