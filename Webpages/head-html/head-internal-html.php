<!DOCTYPE html>

<html>
    <head>
        <?php include_once __DIR__.'/links.php';?>

        <!--include the html code for the header of the external pages like signup and login where the user is not supposed to be logged in -->
        <!-- the code below just has a signout button and displays the persons first name -->
        
        <div class="header">

        <div class="templateslayout width97">

            <div class="title">

                <div class="logo">
                    <img src="../Images/Lyrethin.png" height="60">
                </div>

                <div class="webname">

                    <!--I wanna link the name to the homepage-->
                    <a href="homepage.php">

                        <button class="titlebutton">Calliope</button>

                    </a>

                </div>

            </div>


            <div>
                <br>
                <br>
                <br>

           
               <!-- <div class="worksans weight300 size30 white"><?php echo substr($_SESSION["user"]->fname, 0, 10) ?></div> -->
      
                <div class="inline">

                    <div>
                        <a href="dashboard.php">
                            <button class="gsbutton">Dashboard</button>
                        </a>
                    </div>

                    <form method="POST" onsubmit="return false" enctype="multipart/form-data">


            
                        <button class="gsbutton" name="logout" type="submit">Logout</button>
        
                    

                    </form>

                </div>
             

  

            </div>
            

        </div>

        </div>

        
        


        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>

    <body style="margin: 0; background-color: #3a138d;">
        <script type="text/javascript">
            $("button[name='logout']").click(() => {
                var totalemptydata = giveEmptyData();
                sendAJAXRequest("../PHP-backened/logout.php", totalemptydata, doNothing);
                window.location.replace("login.php");
            })
        </script>