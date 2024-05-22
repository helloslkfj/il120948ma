<?php 
    if(isset($_SESSION["user"])) {
        if($_SESSION["user"]->verification == 'false') {
            exit("Error: Not Verified!");
        }
    }
?>