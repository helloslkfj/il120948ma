<?php
    include_once __DIR__.'/head-code/headinternal-code.php';
    include_once __DIR__.'/../PHP-backened/header-backened-code/headerincludes-backened.php';
?>

    <!-- Here create the inputs for research and the generate button-->

    <div class="generaldashspace">
        <div></div>
        <div class="grid gap-r-15">
            <div class="research-inputgrid">
                <input name="professorwebpage" type="text" placeholder="Link to webpage that is dedicated to the professor">
                <div></div>
                <input name="publicationwebpage" type="text" placeholder="Link to one of the professor's publications">
            </div>
            <div class="research-inputgrid">
                <div class="grid">
                    <text>Select template</text>
                    <?php 
                        $enctemplates = getDatafromSQLResponse(["email", "title", "textt", "datentimeinteger", "iv"], executeSQL($conn, "SELECT * FROM templates WHERE email=?;", ["s"], [$encemail], "select", "nothing"));
                        $dectemplates = decryptFullData($enctemplates, $key, 4);

                        if(count($dectemplates) > 0) {
                    ?>
                    <select name="templates"> <!-- what to do when you get stuck  on just create new; also make sure that when a user signs up you intialize certain templates to him/her-->
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
                </div>
            </div>

            <!-- payment integration with stripe will be done later we will just have saved, whether subscription of the user is active or not, thats all we need
            Stripe handles the reccuring billing by itself-->
            <button class="generatebutton center" name="generateresearch">Generate</button>

        </div>
        <div></div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $("body").on("change", "select[name='templates']", function() {
                if($("select[name='templates']").find(":selected").val() == "Create New") {
                    window.location.replace('templates.php');
                }  
            });
            $("body").on("change", "select[name='resumes']", function() {
                if($("select[name='resumes']").find(":selected").val() == "Add More") {
                    window.location.replace('resumes.php');
                }  
            });
            $("button[name='createtemplate']").click(()=>{
                window.location.replace('templates.php')
            });
            $("button[name='addresume']").click(()=>{
                window.location.replace('resumes.php')
            });
        });
    </script>

<?php 
    include 'footer-frontend.php';
?>