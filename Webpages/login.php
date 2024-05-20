<?php 
    //set up login with login backened function

    include 'head-external.php';

    print_r($_SESSION["user"]);
?>

    <div class="grid generalspace">
        <div></div>
        <form class="grid gap-r-15" method="POST" onsubmit="return false" enctype="multipart/form-data">
            <h1>Log in</h1>
            <div class="grid">
                <input name="loginemail" type="text" placeholder="Email">
            </div>

            <div class="grid">
                <input name="loginpassword" type="password" placeholder="Password">
            </div>

            <p id="loginerror" class="highlight"></p>

            <button name="loginbutton" type="submit" class="center">Submit</button>
        </form>
        <div></div>
    </div>

    <script type="text/javascript">
        $("button[name='loginbutton']").click(()=> {
            sendAJAXRequest("../PHP-backened/login-backened.php", createFormDataObject(["input[name='loginemail']", "input[name='loginpassword']"], ["loginemail", "loginpassword"]), reLoadandErrorHandle);
        })
    </script>



<?php 
    include 'footer-frontend.php';
?>