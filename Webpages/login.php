<?php 
    //set up login with login backened function

    include_once __DIR__.'/head-external.php';

?>




    <div class="box">

        <div class="signup">
            Log In
        </div>

        <form method="POST" onsubmit="return false" enctype="multipart/form-data">

                <input class="epass" name="loginemail" type="text" placeholder="Email">

                <input class="epass" name="loginpassword" type="password" placeholder="Password">

                <p id="loginerror" class="highlight"></p>


                <button name="loginbutton" type="submit" class="caccbutton">Submit</button>

        </form>

        <div class="line2"></div>


        <div class="fineprint">

                <a href="signup.php">Don't have an account yet?</a>
            
        </div>

    </div>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    




    <script type="text/javascript">
        $("button[name='loginbutton']").click(()=> {
            sendAJAXRequest2("../PHP-backened/login-backened.php", createFormDataObject(["input[name='loginemail']", "input[name='loginpassword']"], ["loginemail", "loginpassword"]), reLoadandErrorHandle, "#loginerror");
        })
    </script>



<?php 
    include 'footer-frontend.php';
?>