<?php 
    if(isset($_SESSION["user"])) {
        if($_SESSION["user"]->verification == 'false') {
            header("Location: verification.php");
        }
    }
?>