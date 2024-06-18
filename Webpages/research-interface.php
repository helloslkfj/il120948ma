<?php
    include_once __DIR__.'/head-internal.php';
    include_once __DIR__.'/../PHP-backened/header-backened-code/headerincludes-backened.php';

?>

    <!-- Here create the inputs for research and the generate button-->
    <br>
    <br>

    <div>
    <div class="container">
        <div></div>
        <div class="grid gap-r-15">
            <div class="poppins size50 marginbottom center">Research Email Generator</div>
            <?php if(isset($_SESSION["researchemailrequestobj"])) {?>
                <div>
                    <a id="exitcurrentemail" class="underline textlink">Exit</a><text> current email generation process if you want to change your links</text>
                </div>
            <?php } ?>

            <div class="marginbottom">
                <div class="grid marginbottom">
                    <?php if(isset($_SESSION["researchemailrequestobj"])) {?>
                        <input class="generateinput" name="professorname" type="text" placeholder="The full name of the professor/researcher" value="<?php echo $_SESSION["researchemailrequestobj"]->professorname; ?>">
                    <?php } else { ?>
                        <input class="generateinput" name="professorname" type="text" placeholder="The full name of the professor/researcher">
                    <?php } ?>
                    <p id="professornameerror" class="highlight"></p>
                </div>
                <div class="poppins size30">Links</div>
                <div class="grid">
                    <?php if(isset($_SESSION["researchemailrequestobj"])) {?>
                        <input class="generateinput" name="professorwebpage" type="text" placeholder="Link to webpage that is dedicated to the professor/researcher" value="<?php echo $_SESSION["researchemailrequestobj"]->professorwebpage; ?>">
                    <?php } else {?>
                        <input class="generateinput" name="professorwebpage" type="text" placeholder="Link to webpage that is dedicated to the professor/researcher">
                    <?php }?>
                    <p id="professorweberror" class="highlight"></p>
                </div>
            </div>

            <div class="marginbottom">
                <div class="grid marginbottom">
                    <?php if(isset($_SESSION["researchemailrequestobj"])) { ?>
                        <input class="generateinput" name="publicationwebpage" type="text" placeholder="Link to one of the professor's/researcher's publications" value="<?php echo $_SESSION["researchemailrequestobj"]->publicationwebpage; ?>">
                    <?php } else {?>
                        <input class="generateinput" name="publicationwebpage" type="text" placeholder="Link to one of the professor's/researcher's publications">
                    <?php } ?>
                    <p id="publicationweberror" class="highlight"></p>
                </div>
                <div></div>
                <div class="grid">
                    <div class="poppins size30">Select template</div>
                    <?php 
                        $enctemplates = getDatafromSQLResponse(["email", "title", "textt", "datentimeinteger", "iv"], executeSQL($conn, "SELECT * FROM templates WHERE email=?;", ["s"], [$encemail], "select", "nothing"));
                        $dectemplates = decryptFullData($enctemplates, $key, 4);

                        if(count($dectemplates) > 0) {
                    ?>
                    <select class="generateinput" name="templates"> 
                        <?php 
                            for($i=0;$i<count($dectemplates);$i++) {
                                if(isset($_SESSION["researchemailrequestobj"]) == true && $_SESSION["researchemailrequestobj"]->template == $dectemplates[$i][1]) {
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
            </div>
            <div>
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
                                if(isset($_SESSION["researchemailrequestobj"]) == true && $_SESSION["researchemailrequestobj"]->resume == $decresumes[$i][0]) {
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
                <div></div>
                <div></div>
            </div>
            
            <!-- professor webpage text input form once the session of error for professor webpage is set (errorhandling) -->
            <?php if(isset($_SESSION["profwebextraction-error"]) == true and $_SESSION["profwebextraction-error"] == true) {?>
                <div class="grid gap-r-5">
                    <text class="highlight">Professor Webpage Error</text>
                    <div class="grid">
                        <textarea name="profwebpagetextinput" type="text" placeholder="Error has occured where the professor's/researcher's website cannot be accessed. Command A (MacOS) or CTRL A (Microsoft Windows) or CTRL Shift A (Linux) on the professor's webpage to select everything. Then, Command C (MacOS) or CTRL C (Microsoft Windows) or CTRL Shift C (Linux) the webpage, followed by a Command V (MacOS) or CTRL V (Microsoft Windows) or CTRL Shift V (Linux) into this box. This will allow you to copy and paste all the text of the professor's/researcher's webpage. Once you are done, just click generate again and we will now use this provided professor/researcher webpage text for getting context on the professor/researcher. Do not worry about it including unnecessary text pieces, etc as our model will automatically take out the relevant information." rows=8 cols=1></textarea>
                        <p id="profwebpagetextinputerror" class="highlight"></p>
                    </div>
                </div>
            <?php } ?>
            
            <!-- publication webpage text input form once the session of error for publication webpage is set (errorhandling) -->
             <?php if(isset($_SESSION["publicationextraction-error"]) == true and $_SESSION["publicationextraction-error"] == true) {?>
                <div class="grid gap-r-5">
                    <text class="highlight">Publication Error</text>
                    <div class="grid">
                        <textarea name="publicationtextinput" type="text" placeholder="Error has occured where the professor's/researcher's website cannot be accessed. Command A (MacOS) or CTRL A (Microsoft Windows) or CTRL Shift A (Linux) on the professor's webpage to select everything. Then, Command C (MacOS) or CTRL C (Microsoft Windows) or CTRL Shift C (Linux) the webpage, followed by a Command V (MacOS) or CTRL V (Microsoft Windows) or CTRL Shift V (Linux) into this box. This will allow you to copy and paste all the text of the professor's/researcher's webpage. Once you are done, just click generate again and we will now use this provided professor/researcher webpage text for getting context on the professor/researcher. Do not worry about it including unnecessary text pieces, etc as our model will automatically take out the relevant information." rows=8 cols=1></textarea>
                        <p id="publicationtextinputerror" class="highlight"></p>
                    </div>
                </div>
            <?php } ?>
            <!-- payment integration with stripe will be done later we will just have saved, whether subscription of the user is active or not, thats all we need
            Stripe handles the reccuring billing by itself-->
            <div class="grid gap-r-5">
                <text>All error:</text>
                <p id="researchemailerror" class="highlight"></p>
            </div>
            
            <div class="generaltwocolumns">
                <div class="right">
                    <button class="roundbutton center" name="generateresearch">Generate</button>
                </div>
                <div id="loader" class="left">

                </div>
            </div>
            <br>
            <br>
            <br>
            <?php if(isset($_SESSION["researchemailinfo"])) { ?>
                <div id="generatedemailgrid" class="grid gap-r-10">
                 
          
                    <div class="grid">
                        <div class="poppins;">Subject</div>
                        <input class="generateinput" name="emailsubject" value="<?php echo $_SESSION["researchemailinfo"]->researchemailsubject; ?>" readonly></input>
                    </div>
                    <div class="grid">
                        <textarea class="generateoutput" name="emailbody" cols=1 rows=10 readonly><?php echo $_SESSION["researchemailinfo"]->researchemail; ?></textarea>
                    </div>
                    
                    <div class="generalthreecolumns gap-c-10 auto fit">
                        <div>
                            <button name="doneemail" class="center roundbutton">Done</button>
                        </div>

                        <div>
                            <button name="copyemail" class="center linebutton">Copy</button>
                        </div>

                        <?php if($_SESSION["researchemailinfo"]->attempts < 2) {?>
                            <div>
                                <div id="regenerateloader"></div> <button name="regeneratebutton" class="center linebutton">Regenerate</button>
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
            $("#exitcurrentemail").click(()=>{
                var exitemaildata = createFormDataObject([], []);
                exitemaildata.append('exitemail', 'true');
                sendAJAXRequest('../PHP-backened/researchinterfacebackened.php',exitemaildata,reLoad);
            });

            $("button[name='copyemail']").click(()=>{
                var emailsubject = $("input[name='emailsubject']").val();
                var emailbody = $("textarea[name='emailbody']").val();

                var totalemailtext = emailsubject+"\n\n"+emailbody;

                navigator.clipboard.writeText(totalemailtext);
                $("button[name='copyemail']").html("Copied");
            });

            $("button[name='doneemail']").click(()=>{
                var doneemaildata = createFormDataObject([], []);
                doneemaildata.append('doneemail', 'true');
                sendAJAXRequest("../PHP-backened/researchinterfacebackened.php",doneemaildata, reLoad);
            });

            $("button[name='regeneratebutton']").click(()=>{
                var regeneratebuttondata = createFormDataObject([], []);
                regeneratebuttondata.append('regenerate', 'true');
                $("#regenerateloader").html("<i class='center fa-regular fa-gear fa-spin sidetosidepadding fa-lg'></i>");
                sendAJAXRequest("../PHP-backened/researchinterfacebackened.php", regeneratebuttondata, reLoad);
            });

            $("input[name='professorname']").keyup(()=>{
                var professornamedata = createFormDataObject(["input[name='professorname']"], ["professorname"]);
                sendAJAXRequest2('../PHP-backened/research-scrape.php', professornamedata, reLoadandErrorHandle, "#professornameerror");
            });
            $("input[name='professorwebpage']").keyup(()=>{
                var professorwebdata = createFormDataObject(["input[name='professorwebpage']"], ["professorwebpage"]);
                sendAJAXRequest2('../PHP-backened/research-scrape.php', professorwebdata, reLoadandErrorHandle, "#professorweberror");
            });
            $("input[name='publicationwebpage']").keyup(()=>{
                var publicationpubdata = createFormDataObject(["input[name='publicationwebpage']"], ["publicationwebpage"]);
                sendAJAXRequest2('../PHP-backened/research-scrape.php', publicationpubdata, reLoadandErrorHandle, "#publicationweberror")
            });
            $("textarea[name='profwebpagetextinput']").keyup(()=> {
                var profwebpagetextdata = createFormDataObject(["textarea[name='profwebpagetextinput']"], ["profwebpagetextinput"]);
                sendAJAXRequest2('../PHP-backened/research-scrape.php', profwebpagetextdata, reLoadandErrorHandle, "#profwebpagetextinputerror");
            });
            $("textarea[name='publicationtextinput']").keyup(()=>{
                var publicationtextdata = createFormDataObject(["textarea[name='publicationtextinput']"], ["publicationtextinput"]);
                sendAJAXRequest2('../PHP-backened/research-scrape.php', publicationtextdata, reLoadandErrorHandle, "#publicationtextinputerror");
            });

            $("body").on("change", "select[name='templates']", function() {
                if($("select[name='templates']").find(":selected").val() == "Create New") {
                    window.location.replace('templates.php');
                } else {
                    var templatedata = createFormDataObject1([$("select[name='templates']").find(":selected")], ["template"]);
                    sendAJAXRequest2('../PHP-backened/research-scrape.php', templatedata, reLoadandErrorHandle, "#templateerror");
                }
            });
            $("body").on("change", "select[name='resumes']", function() {
                if($("select[name='resumes']").find(":selected").val() == "Add More") {
                    window.location.replace('resumes.php');
                } else {
                    var resumedata = createFormDataObject([$("select[name='resumes']").find(":selected")], ["resume"]);
                    sendAJAXRequest2('../PHP-backened/research-scrape.php', resumedata, reLoadandErrorHandle, "#resumeerror");
                }
            });

            $("button[name='createtemplate']").click(()=>{
                window.location.replace('templates.php');
            });
            $("button[name='addresume']").click(()=>{
                window.location.replace('resumes.php');
            });

            $("button[name='generateresearch']").click(()=>{
                var inputnamesarr = ["professorname", "professorwebpage", "publicationwebpage", "template", "resume"];
                var inputsarr = [$("input[name='professorname']"), $("input[name='professorwebpage']"), $("input[name='publicationwebpage']"), $("select[name='templates']").find(":selected"), $("select[name='resumes']").find(":selected")];
                var keyupelementsarr = ["input[name='professorname']", "input[name='professorwebpage']", "input[name='publicationwebpage']"];

                if($("textarea[name='profwebpagetextinput']").val() != undefined) {
                    inputnamesarr.push("profwebpagetextinput");
                    inputsarr.push($("textarea[name='profwebpagetextinput']"));
                    keyupelementsarr.push("textarea[name='profwebpagetextinput']");
                }

                if($("textarea[name='publicationtextinput']").val() != undefined) {
                    inputnamesarr.push("publicationtextinput");
                    inputsarr.push($("textarea[name='publicationtextinput']"));
                    keyupelementsarr.push("textarea[name='publicationtextinput']");
                }

                researchemailinfo = createFormDataObject1(inputsarr, inputnamesarr);
                researchemailinfo.append('generateresemail', true);

                var instanterror = 0;
                if(researchemailinfo.get("template") == 'null' || researchemailinfo.get("template") == 'undefined' || researchemailinfo.get("template") == "") {
                    $("#templateerror").html('Please attach a template.');
                    instanterror += 1;
                }
                if(researchemailinfo.get("resume") == 'null' || researchemailinfo.get("resume") == 'undefined' || researchemailinfo.get("resume") == "") {
                    console.log(researchemailinfo.get("resume"));
                    $("#resumeerror").html('Please attach a resume.');
                    instanterror += 1;
                }

                keyUpAllElements(keyupelementsarr);

                if(instanterror > 0) {
                    return;
                }

                changeUpAllElements(["select[name='templates']", "select[name='resumes']"]);
                $("#loader").html("<i class='center fa-regular fa-gear fa-spin sidetosidepadding fa-lg'></i>");
                sendAJAXRequest2('../PHP-backened/research-scrape.php', researchemailinfo, function(input, varname) {
                    $("#loader").html("");
                    //the generate grid is just nothing if there is no reload as only reload reload indicated email
                    $("#generatedemailgrid").html("");
                    reLoadandErrorHandle(input, varname); 
                }, "#researchemailerror");

            });

        });
    </script>

<?php 
    include __DIR__.'/footer-frontend.php';
?>