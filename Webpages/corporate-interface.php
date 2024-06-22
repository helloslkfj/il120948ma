<?php
    include_once __DIR__.'/head-internal.php';
    include_once __DIR__.'/../PHP-backened/header-backened-code/headerincludes-backened.php';

?>
<br>
<br>

    <div>
        <div class="container">
            <div class="grid gap-r-15">
                <div class="poppins size50 marginbottom center">Corporate Email Generator</div>
                <?php if(isset($_SESSION["corporateemailrequestobj"])) {?>
                        <div>
                            <a id="exitcurrentemail" class="underline textlink">Exit</a><text> current email generation process if you want to change your links</text>
                        </div>
                <?php } ?>
                <div class="grid marginbottom">
                        <?php if(isset($_SESSION["corporateemailrequestobj"])) {?>
                            <input class="generateinput" name="personname" type="text" placeholder="The full name of the person you want to outreach to" value="<?php echo $_SESSION["corporateemailrequestobj"]->personname; ?>">
                        <?php } else { ?>
                                <input class="generateinput" name="personname" type="text" placeholder="The full name of the person you want to outreach to">
                        <?php } ?>
                        <p id="personnameerror" class="highlight"></p>
                </div>

                <div class="poppins size30">Links</div>

                <div class="grid">
                    <?php if(isset($_SESSION["corporateemailrequestobj"])) {?>
                        <input class="generateinput" name="personwebpage" type="text" placeholder="Link to linkedin/website of the person your outreaching to" value="<?php echo $_SESSION["corporateemailrequestobj"]->personwebpage; ?>">
                    <?php } else {?>
                        <input class="generateinput" name="personwebpage" type="text" placeholder="Link to linkedin/website of the person your outreaching to">
                    <?php }?>
                    <p id="personweberror" class="highlight"></p>
                </div>
                <div class="grid marginbottom">
                    <?php if(isset($_SESSION["corporateemailrequestobj"])) { ?>
                        <input class="generateinput" name="companywebpage" type="text" placeholder="Link to the company's website that you are interested in specifically the about/what we do page" value="<?php echo $_SESSION["corporateemailrequestobj"]->corporatepage; ?>">
                    <?php } else {?>
                        <input class="generateinput" name="companywebpage" type="text" placeholder="Link to the company's website that you are interested in specifically the about/what we do page">
                    <?php } ?>
                        <p id="companyweberror" class="highlight"></p>
                </div>

                <div class="grid marginbottom">
                    <div class="poppins size30">Select template</div>
                    <?php 
                        $enctemplates = getDatafromSQLResponse(["email", "title", "textt", "datentimeinteger", "iv"], executeSQL($conn, "SELECT * FROM templates WHERE email=?;", ["s"], [$encemail], "select", "nothing"));
                        $dectemplates = decryptFullData($enctemplates, $key, 4);

                        if(count($dectemplates) > 0) {
                    ?>
                    <select class="generateinput" name="templates"> 
                        <?php 
                            for($i=0;$i<count($dectemplates);$i++) {
                                if(isset($_SESSION["corporateemailrequestobj"]) == true && $_SESSION["corporateemailrequestobj"]->template == $dectemplates[$i][1]) {
                        ?>
                                    <option value="<?php echo $dectemplates[$i][1]; ?>" selected><?php echo $dectemplates[$i][1]; ?></option>
                                <?php } else { ?>
                                    <option value="<?php echo $dectemplates[$i][1]; ?>"><?php echo $dectemplates[$i][1]; ?></option>
                                <?php } ?>
                        <?php } ?>
                        <option value="Create New">Create New</option>
                    </select>
                    <?php } else { ?>
                        <button class="center" name="createtemplate">Create a Template</button> 
                    <?php } ?>
                    <p id="templateerror" class="highlight"></p>
                </div>

                <div class="grid">
                    <div class="poppins size30">Select a resume</div>
                    <?php 
                        $encresumes = getDatafromSQLResponse(["resumename", "resumelocation", "iv"], executeSQL($conn, "SELECT * FROM resumes WHERE email=?", ["s"], [$encemail], "select", "nothing"));
                        $decresumes = decryptFullData($encresumes, $key, 2);
                        if(count($decresumes) > 0) {
                    ?>
                    <select class="generateinput" name="resumes">
                        <?php 
                            for($i=0;$i<count($decresumes);$i++) {
                                if(isset($_SESSION["corporateemailrequestobj"]) == true && $_SESSION["corporateemailrequestobj"]->resume == $decresumes[$i][0]) {
                        ?>
                                    <option value="<?php echo $decresumes[$i][0]; ?>" selected><?php echo $decresumes[$i][0]; ?></option>
                                <?php } else { ?>
                                    <option value="<?php echo $decresumes[$i][0]; ?>"><?php echo $decresumes[$i][0]; ?></option>
                                <?php } ?>
                        <?php } ?>
                        <option value="Add More">Add More</option>
                    </select>
                    <?php } else {?>
                        <button class="center" name="addresume">Add a Resume</button> 
                    <?php } ?>
                    <p id="resumeerror" class="highlight"></p>
                </div>

                <!-- person webpage text input form once the session of error for  person webpage is set (errorhandling) -->
                <?php if(isset($_SESSION["personwebextraction-error"]) == true and $_SESSION["personwebextraction-error"] == true) {?>
                    <div class="grid gap-r-5">
                        <text class="highlight">Outreach Person Webpage Error</text>
                        <div class="grid">
                            <textarea name="personwebpagetextinput" type="text" placeholder="Error has occured where the person's (who you are outreaching to) website cannot be accessed. Command A (MacOS) or CTRL A (Microsoft Windows) or CTRL Shift A (Linux) on the person's webpage to select everything. Then, Command C (MacOS) or CTRL C (Microsoft Windows) or CTRL Shift C (Linux) the webpage, followed by a Command V (MacOS) or CTRL V (Microsoft Windows) or CTRL Shift V (Linux) into this box. This will allow you to copy and paste all the text of the person's webpage. Once you are done, just click generate again and we will now use this provided person webpage text for getting context on the person. Do not worry about it including unnecessary text pieces, etc as our model will automatically take out the relevant information." rows=8 cols=1></textarea>
                            <p id="personwebpagetextinputerror" class="highlight"></p>
                        </div>
                    </div>
                <?php } ?>

                <!-- company webpage text input form once the session of error for company webpage is set (errorhandling) -->
                <?php if(isset($_SESSION["companywebextraction-error"]) == true and $_SESSION["companywebextraction-error"] == true) {?>
                    <div class="grid gap-r-5">
                        <text class="highlight">Company Webpage Error</text>
                        <div class="grid">
                            <textarea name="companywebpagetextinput" type="text" placeholder="Error has occured where the company's website cannot be accessed. Command A (MacOS) or CTRL A (Microsoft Windows) or CTRL Shift A (Linux) on the company's webpage to select everything. Then, Command C (MacOS) or CTRL C (Microsoft Windows) or CTRL Shift C (Linux) the webpage, followed by a Command V (MacOS) or CTRL V (Microsoft Windows) or CTRL Shift V (Linux) into this box. This will allow you to copy and paste all the text of the company's webpage. Once you are done, just click generate again and we will now use this provided company webpage text for getting context on the company. Do not worry about it including unnecessary text pieces, etc as our model will automatically take out the relevant information." rows=8 cols=1></textarea>
                            <p id="companywebpagetextinputerror" class="highlight"></p>
                        </div>
                    </div>
                <?php } ?>

                <!-- payment integration with stripe will be done later we will just have saved, whether subscription of the user is active or not, thats all we need
                Stripe handles the reccuring billing by itself-->

                <div class="grid gap-r-5">
                    <text>All error:</text>
                    <p id="corporateemailerror" class="highlight"></p>
                </div>
                    
                <div class="generaltwocolumns">

                    <div class="right">
                        <button class="roundbutton center" name="generatecorporate">Generate</button>
                    </div>

                    <div>
                        <div id="loader" class="left">

                        </div>
                    </div>

                </div>
                <br>
                <br>
                <br>
                
                <?php if(isset($_SESSION["corporateemailinfo"])) { ?>
                    <div id="generatedemailgrid" class="grid gap-r-10">
                        
                
                        <div class="grid">
                            <div class="poppins;">Subject</div>
                            <input class="generateinput" name="emailsubject" value="<?php echo $_SESSION["corporateemailinfo"]->corporateemailsubject; ?>" readonly></input>
                        </div>
                        <div class="grid">
                            <textarea class="generateoutput" name="emailbody" cols=1 rows=10 readonly><?php echo $_SESSION["corporateemailinfo"]->corporateemailtext; ?></textarea>
                        </div>
                            
                        <div class="generalthreecolumns gap-c-10 auto fit">
                            <div>
                                <button name="doneemail" class="center linebutton poppins size20">Done</button>
                            </div>

                            <div>
                                <button name="copyemail" class="center linebutton poppins size20">Copy</button>
                            </div>

                            <?php if($_SESSION["corporateemailinfo"]->attempts < 2) {?>
                                <div>
                                    <div id="regenerateloader"></div> <button name="regeneratebutton" class="center linebutton poppins size20">Regenerate</button>
                                </div>

                            <?php } else { ?>
                                <div></div>
                            <?php }?>
                        </div>

                    </div>
                <?php } ?>



            </div>
            <br>
            <br>
        </div>
    </div>
    <br>
    <br>
    <br>
    <br>
    <br>

    <script type="text/javascript">
        $(document).ready(function() {
            const url = "../PHP-backened/corporate-scrape.php";
            $("input[name='personname']").keyup(()=>{
                var personnamedata = createFormDataObject(["input[name='personname']"], ["personname"]);
                sendAJAXRequest2(url, personnamedata, reLoadandErrorHandle, "#personnameerror");
            });
            $("input[name='personwebpage']").keyup(()=>{
                var personwebdata = createFormDataObject(["input[name='personwebpage']"], ["personwebpage"]);
                sendAJAXRequest2(url, personwebdata, reLoadandErrorHandle, "#personweberror");
            });
            $("input[name='companywebpage']").keyup(()=>{
                var companywebdata = createFormDataObject(["input[name='companywebpage']"], ["companywebpage"]);
                sendAJAXRequest2(url, companywebdata, reLoadandErrorHandle, "#companyweberror");
            });
            $("textarea[name='personwebpagetextinput']").keyup(()=>{
                var personwebpagetextdata = createFormDataObject(["textarea[name='personwebpagetextinput']", "personwebpagetextinput"]);
                sendAJAXRequest2(url, personwebpagetextdata, reLoadandErrorHandle, "personwebpagetextinputerror");
            });
            $("textarea[name='companywebpagetextinput']").keyup(()=>{
                var companywebpagetextdata = createFormDataObject(["textarea[name='companywebpagetextinput']"], ["companywebpagetextinput"]);
                sendAJAXRequest2(url, companywebpagetextdata, reLoadandErrorHandle, "companywebpagetextinputerror");
            });

            $("body").on("change", "select[name='templates']", function() {
                if($("select[name='templates']").find(":selected").val() == "Create New") {
                    window.location.assign('templates.php');
                } else {
                    var templatedata = createFormDataObject1([$("select[name='templates']").find(":selected")], ["template"]);
                    sendAJAXRequest2('../PHP-backened/research-scrape.php', templatedata, reLoadandErrorHandle, "#templateerror");
                }
            });
            $("body").on("change", "select[name='resumes']", function() {
                if($("select[name='resumes']").find(":selected").val() == "Add More") {
                    window.location.assign('resumes.php');
                } else {
                    var resumedata = createFormDataObject([$("select[name='resumes']").find(":selected")], ["resume"]);
                    sendAJAXRequest2('../PHP-backened/research-scrape.php', resumedata, reLoadandErrorHandle, "#resumeerror");
                }
            });

            $("button[name='createtemplate']").click(()=>{
                window.location.assign('templates.php');
            });
            $("button[name='addresume']").click(()=>{
                window.location.assign('resumes.php');
            });

            $("button[name='generatecorporate']").click(()=>{
                var inputnamesarr = ["personname", "personwebpage", "companywebpage", "template", "resume"];
                var inputsarr = [$("input[name='personname']"), $("input[name='personwebpage']"), $("input[name='companywebpage']"), $("select[name='templates']").find(":selected"), $("select[name='resumes']").find(":selected")];
                var keyupelementsarr = ["input[name='personname']", "input[name='personwebpage']", "input[name='companywebpage']"];

                if($("textarea[name='personwebpagetextinput']").val() != undefined) {
                    inputnamesarr.push("personwebpagetextinput");
                    inputsarr.push($("textarea[name='personwebpagetextinput']"));
                    keyupelementsarr.push("textarea[name='personwebpagetextinput']");
                }

                if($("textarea[name='companywebpagetextinput']").val() != undefined) {
                    inputnamesarr.push("companywebpagetextinput");
                    inputsarr.push($("textarea[name='companywebpagetextinput']"));
                    keyupelementsarr.push("textarea[name='companywebpagetextinput']");
                }

                corporateemailinfo = createFormDataObject1(inputsarr, inputnamesarr);
                corporateemailinfo.append('generatecorporateemail', true);

                var instanterror = 0;
                if(corporateemailinfo.get("template") == 'null' || corporateemailinfo.get("template") == 'undefined' || corporateemailinfo.get("template") == "") {
                    $("#templateerror").html('Please attach a template.');
                    instanterror += 1;
                }
                if(corporateemailinfo.get('resume') == 'null' || corporateemailinfo.get('resume') == 'undefined' || corporateemailinfo.get('resume') == "") {
                    $("#resumeerror").html('Please attach a resume.');
                    instanterror += 1;
                }

                keyUpAllElements(keyupelementsarr);

                if(instanterror > 0) {
                    return;
                }

                changeUpAllElements(["select[name='templates']", "select[name='resumes']"]);
                $("#loader").html("<div><i class='center purple fa-regular fa-gear fa-spin sidetosidepadding fa-2xl'></i></div>");
                sendAJAXRequest2('../PHP-backened/corporate-scrape.php', corporateemailinfo, function(input, varname) {
                    $("#loader").html("");
                    //the generate grid is just nothing if there is no reload as only reload reload indicated email
                    $("#generatedemailgrid").html("");
                    reLoadandErrorHandle(input, varname); 
                }, "#corporateemailerror");

            });

        });
    </script>

<?php 
    include __DIR__.'/footer-frontend.php';
?>