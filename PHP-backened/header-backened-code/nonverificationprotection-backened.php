<?php 
    //if you are not verified that is only when you can access this script

    if(isset($_SESSION["user"])){
        if($_SESSION["user"]->verification != 'false') {
            exit("You are already verified");
        }
    }
?>