<?php 
    //set up login with login backened function

    include 'head-external.php';

?>

    <div class="grid generalspace">
        <div></div>
        <div class="grid">
            <form class="grid gap-r-15" method="POST" onsubmit="return false" enctype="multipart/form-data">
                <h1>Log in</h1>

                <input name="loginemail" type="text" placeholder="Email">

                <input name="loginpassword" type="password" placeholder="Password">

                <p id="loginerror" class="highlight"></p>

                <button name="loginbutton" type="submit" class="center">Submit</button>
            </form>
            <div>
                <a href="signup.php">Don't have an account yet?</a>
            </div>
        </div>
        <div></div>
    </div>

    <script type="text/javascript">
        $("button[name='loginbutton']").click(()=> {
            sendAJAXRequest2("../PHP-backened/login-backened.php", createFormDataObject(["input[name='loginemail']", "input[name='loginpassword']"], ["loginemail", "loginpassword"]), reLoadandErrorHandle, "#loginerror");
        })
    </script>



<?php 
    include 'footer-frontend.php';
?>