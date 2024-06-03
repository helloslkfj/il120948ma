<?php
    include_once __DIR__.'/head-code/headinternal-code.php';
    include_once __DIR__.'/../PHP-backened/header-backened-code/headerincludes-backened.php';
?>

    <!-- Here create the inputs for research and the generate button-->

    <div class="generaldashspace">
        <div></div>
        <div class="grid gap-r-15">
            <div class="research-inputgrid">
                <div class="grid">
                    <input name="professorwebpage" type="text" placeholder="Link to webpage that is dedicated to the professor">
                    <p id="professorweberror" class="highlight"></p>
                </div>
                <div></div>
                <div class="grid">
                    <input name="publicationwebpage" type="text" placeholder="Link to one of the professor's publications">
                    <p id="publicationweberror" class="highlight"></p>
                </div>
            </div>
            <div class="research-inputgrid">
                <div class="grid">
                    <text>Select template</text>
                    <?php 
                        $enctemplates = getDatafromSQLResponse(["email", "title", "textt", "datentimeinteger", "iv"], executeSQL($conn, "SELECT * FROM templates WHERE email=?;", ["s"], [$encemail], "select", "nothing"));
                        $dectemplates = decryptFullData($enctemplates, $key, 4);

                        if(count($dectemplates) > 0) {
                    ?>
                    <select name="templates"> 
                        <?php 
                            for($i=0;$i<count($dectemplates);$i++) {
                        ?>
                        <option value="<?php echo $dectemplates[$i][1]; ?>"><?php echo $dectemplates[$i][1]; ?></option>
                        <?php } ?>
                        <option value="Create New">Create New</option>
                    </select>
                    <?php } else { ?>
                        <button class="center" name="createtemplate">Create a Template</button> 
                    <?php } ?>
                    <p id="templateerror" class="highlight"></p>
                </div>
                <div></div>
                <div class="grid">
                    <text>Select a resume</text>
                    <?php 
                        $encresumes = getDatafromSQLResponse(["resumename", "resumelocation", "iv"], executeSQL($conn, "SELECT * FROM resumes WHERE email=?", ["s"], [$encemail], "select", "nothing"));
                        $decresumes = decryptFullData($encresumes, $key, 2);
                        if(count($decresumes) > 0) {
                    ?>
                    <select name="resumes">
                        <?php 
                            for($i=0;$i<count($decresumes);$i++) {
                        ?>
                        <option value="<?php echo $decresumes[$i][0]; ?>"><?php echo $decresumes[$i][0]; ?></option>
                        <?php } ?>
                        <option value="Add More">Add More</option>
                    </select>
                    <?php } else {?>
                        <button class="center" name="addresume">Add a Resume</button> 
                    <?php } ?>
                    <p id="resumeerror" class="highlight"></p>
                </div>
            </div>

            <!-- payment integration with stripe will be done later we will just have saved, whether subscription of the user is active or not, thats all we need
            Stripe handles the reccuring billing by itself-->
            <p id="researchemailerror" class="highlight"></p>

            <button class="generatebutton center" name="generateresearch">Generate</button>

        </div>
        <div></div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $("input[name='professorwebpage']").keyup(()=>{
                var professorwebdata = createFormDataObject([$("input[name='professorwebpage']")], ["professorwebpage"]);
                console.log(professorwebdata);
                sendAJAXRequest2('../PHP-backened/research-scrape.php', professorwebdata, reLoadandErrorHandle, "#professorweberror");
            });
            $("input[name='publicationwebpage']").keyup(()=>{
                var publicationwebdata = createFormDataObject([$("input[name='publicationwebpage']")], ["publicationwebpage"]);
                sendAJAXRequest2('../PHP-backened/research-scrape.php', publicationwebdata, reLoadandErrorHandle, "#publicationweberror")
            });

            $("body").on("change", "select[name='templates']", function() {
                if($("select[name='templates']").find(":selected").val() == "Create New") {
                    window.location.replace('templates.php');
                } else {
                    var templatedata = createFormDataObject([$("select[name='templates']").find(":selected")], ["template"]);
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
                window.location.replace('templates.php')
            });
            $("button[name='addresume']").click(()=>{
                window.location.replace('resumes.php')
            });

            $("button[name='generateresearch']").click(()=>{
                
                var researchemailinfo = createFormDataObject([$("input[name='professorwebpage']"), $("input[name='publicationwebpage']"), $("select[name='templates']").find(":selected"), $("select[name='resumes']").find(":selected")], ["professorwebpage", "publicationwebpage", "template", "resume"]);
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
                keyUpAllElements(["input[name='professorwebpage']", "input[name='publicationwebpage']"]);
                changeUpAllElements(["select[name='templates']", "select[name='resumes']"]);

                if(instanterror > 0) {
                    return;
                }

                sendAJAXRequest2('../PHP-backened/research-scrape.php', researchemailinfo, reLoadandErrorHandle, "#researchemailerror");




            });

        });
    </script>

<?php 
    include 'footer-frontend.php';
?>