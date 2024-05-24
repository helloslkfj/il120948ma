<?php 
    include_once __DIR__.'/head-external.php';
?>
    <div class="generalspace">
        <div></div>
        <div class="grid">
            <form class="grid gap-r-15" method="POST" onsubmit="return false" enctype="multipart/form-data">
                <h1>Sign up</h1>
                <div class="grid">
                    <input name="firstname" type="text" placeholder="First Name">
                    <p id="fnameerror" class="highlight"></p>
                </div>

                <div class="grid">
                    <input name="signupemail" type="text" placeholder="Email">
                    <p id="signupemailerror" class="highlight"></p>
                </div>

                <div class="grid">
                    <input name="signuppassword" type="password" placeholder="Password">
                    <p id="signuppassworderror" class="highlight"></p>
                </div>

                <div class="grid">
                    <input name="signuprepassword" type="password" placeholder="Retype your Password">
                    <p id="signuprepassworderror" class="highlight"></p>
                </div>
                
                <div class="grid"> <!--Make this error text small as it is general error text, that is coming out of the system and non-formatted, its needed to tell the user when a fatal error occurs in the script -->
                    <text>General Error Text:</text>
                    <text id="totalsignuperror" class="highlight">...</text>
                </div>
                

                <button name="signupbutton" type="submit" class="center">Submit</button>
            </form>
            <div>
                <a href="login.php">Have an account?</a>
            </div>
        </div>
        <div></div>
    </div>

    <script type="text/javascript">

        $(document).ready(()=>{
            const url = "../PHP-backened/signup-backened.php";

            $("input[name='firstname']").keyup(()=>{
                errorHandle(url, createDataObject(["input[name='firstname']"], ["firstname"]) , "#fnameerror");
            })

            $("input[name='signupemail']").keyup(()=>{
                errorHandle(url, createDataObject(["input[name='signupemail']"], ["signupemail"]), "#signupemailerror");
            })

            $("input[name='signuppassword']").keyup(()=>{
                errorHandle(url, createDataObject(["input[name='signuppassword']"], ["signuppassword"]), "#signuppassworderror");
            })

            $("input[name='signuprepassword']").keyup(()=>{
                errorHandle(url, createDataObject(["input[name='signuppassword']", "input[name='signuprepassword']"], ["signuppassword", "signuprepassword"]), "#signuprepassworderror");
            })


            
            $("button[name='signupbutton']").click(() =>{
                var allinputelements = ["input[name='firstname']", "input[name='signupemail']", "input[name='signuppassword']", "input[name='signuprepassword']"];
                signupformdata = createFormDataObject(allinputelements, ["firstname","signupemail","signuppassword","signuprepassword"]);
                signupformdata.append('signupsubmit', true);
                keyUpAllElements(allinputelements);

                sendAJAXRequest2(url, signupformdata, reLoadandErrorHandle, "#totalsignuperror");
            })

        });
    </script>

<?php 
    include 'footer-frontend.php';
?>