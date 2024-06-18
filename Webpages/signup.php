<?php 
    include_once __DIR__.'/head-external.php';
?>





    <div class="box">

        <div class="signup">Sign Up</div>



            <form method="POST" onsubmit="return false" enctype="multipart/form-data">
   
                <div>
                    <input name="firstname" type="text" placeholder="First Name" class="epass">
                    <p id="fnameerror" class="highlight"></p>
                </div>

                <div>
                    <input name="signupemail" type="text" placeholder="Email" class="epass">
                    <p id="signupemailerror" class="highlight"></p>
                </div>

                <div>
                    <input name="signuppassword" type="password" placeholder="Password" class="epass">
                    <p id="signuppassworderror" class="highlight"></p>
                </div>

                <div>
                    <input name="signuprepassword" type="password" placeholder="Retype your Password" class="epass">
                    <p id="signuprepassworderror" class="highlight"></p>
                </div>
                
                <div> <!--Make this error text small as it is general error text, that is coming out of the system and non-formatted, its needed to tell the user when a fatal error occurs in the script -->
                    <text>General Error Text:</text>
                    <text id="totalsignuperror" class="highlight">...</text>
                </div>
                

                <button name="signupbutton" type="submit" class="caccbutton">Submit</button>
            </form>


            <div class="line2"></div>


            <div class="fineprint">
                <a href="login.php">Have an account?</a>
            </div>




    </div>
    <br>
    <br>
    <br>
    <br>
    <br>
    





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