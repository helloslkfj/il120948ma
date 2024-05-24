<?php 
    include_once __DIR__.'/head-code/start-code.php';
    include_once __DIR__.'/head-code/loginprotection-code.php';
    include_once __DIR__.'/head-code/protectionforverificationpage-code.php';
    include_once __DIR__.'/head-html/head-internal-html.php';

    print_r($_SESSION["user"]);
?>

<div class="generalspace">
    <div></div>
    <div class="grid">
        <form class="grid gap-r-15" method="POST" onsubmit="return false" enctype="multipart/form-data">
            <h1>Verification</h1>
            <div class="grid">
                <p>You will recieve an email containing a verficiation number for your Calliope account.</p>
            </div>
            <div class="grid">
                <input name="verificationnumber" type="text" placeholder="1234567">
                <p id="verificationerror" class="highlight"></p>
            </div>
            <div>
                <a name="resendverification" href="">Resend Verification</a>
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

    $("a[name='resendverification']").click((e)=>{
        e.preventDefault();
        var emptydata = createFormDataObject([], []);
        sendAJAXRequest2("../PHP-backened/resend-verification.php", emptydata, reLoadandErrorHandle, "#verificationerror");
    })
</script>


<?php 
    include 'footer-frontend.php';
?>