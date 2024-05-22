<?php  
    if(isset($_SESSION["user"])) {
        header("Location: dashboard.php");
    }
?>