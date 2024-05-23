<?php 
    //set up login with login backened function

    include 'head-external.php';

?>

<div class="main1">

    <div class="header">

        <div class="grid1">

            <div class="title">

                <div class="logo">
                    <img src="../Images/Lyrethin.png" height="60">
                </div>

                <div class="webname">

                    <!--I wanna link the name to the homepage-->
                    <a href="dashboard.php">

                        <button class="titlebutton">Calliope</button>

                    </a>

                </div>

            </div>

        </div>

    </div>

    <div class="box">

        <div class="signup">
            Log In
        </div>

        <div class="grid">

            <form class="grid gap-r-15" method="POST" onsubmit="return false" enctype="multipart/form-data">

                <input class="epass" name="loginemail" type="text" placeholder="Email">

                <input class="epass" name="loginpassword" type="password" placeholder="Password">

                <p id="loginerror" class="highlight"></p>

                <button name="loginbutton" type="submit" class="caccbutton">Submit</button>

            </form>
            
            <div class="fineprint">

                <a href="signup.php">Don't have an account yet?</a>
            
            </div>

        </div>


    </div>

</div>

    <script type="text/javascript">
        $("button[name='loginbutton']").click(()=> {
            sendAJAXRequest2("../PHP-backened/login-backened.php", createFormDataObject(["input[name='loginemail']", "input[name='loginpassword']"], ["loginemail", "loginpassword"]), reLoadandErrorHandle, "#loginerror");
        })
    </script>



<?php 
    include 'footer-frontend.php';
?>