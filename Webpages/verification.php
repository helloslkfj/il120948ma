<?php 
    include 'head-code/start-code.php';
    include 'head-code/loginprotection-code.php';
    include 'head-code/protectionforverificationpage-code.php';
    include 'head-html/head-internal-html.php';

    print_r($_SESSION["user"]);
?>

<div class="generalspace">
    <div></div>
    <div class="grid">
        <form class="grid gap-r-15" method="POST" onsubmit="return false" enctype="multipart/form-data">
            <h1>Verification</h1>
            <div class="grid">
                <input name="verificationnumber" type="text" placeholder="1234567">
                <p id="verificationerror" class="highlight"></p>
            </div>
            <div>
                <a href="">Resend Verification</a>
            </div>
            <button name="verificationbutton" type="submit" class="center">Verify</button>
        </form>
    </div>
    <div></div>
</div>

<script type="text/javascript">
    $("button[name='verificationbutton']").click(() => {
        var verificationdata = createFormDataObject(["input[name='verificationnumber']"], ["verificationnum"]);
        sendAJAXRequest2("../PHP-backened/verification-backened.php", verificationdata, reLoadandErrorHandle, "#verificationerror");
    });
</script>


<?php 
    include 'footer-frontend.php';
?>