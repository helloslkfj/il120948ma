<?php 
    if(session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if(isset($_SESSION["user"]) == false) {
        header("Location: login.php");
    }

?>
<!DOCTYPE html>

<html>
    <head>
        <?php include 'links.php';?>

        <!--include the html code for the header of the external pages like signup and login where the user is not supposed to be logged in -->
        <!-- the code below just has a signout button and displays the persons first name -->
        <div class="grid">
            <text><?php echo substr($_SESSION["user"]->fname, 0, 10) ?></text>
            <form method="POST" onsubmit="return false" enctype="multipart/form-data">
                <button class="center" name="logout" type="submit">Logout</button>
            </form>
        <div>

        
        <br><br>
    </head>

    <body>
        <script type="text/javascript">
            $("button[name='logout']").click(() => {
                var totalemptydata = giveEmptyData();
                sendAJAXRequest("../PHP-backened/logout.php", totalemptydata, doNothing);
                window.location.replace("login.php");
            })
        </script>