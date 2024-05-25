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
                    ?>
                    <select name="templates">
                        <?php 
                            for($i=0;$i<count($dectemplates);$i++) {
                        ?>
                        <option value="<?php echo $dectemplates[$i][1]; ?>"><?php echo $dectemplates[$i][1]; ?></option>
                        <?php } ?>
                        <option value="Create New">Create New</option>
                    </select>
                </div>
                <div></div>
                <input name="publicationwebpage" type="text" placeholder="Link to one of the professor's publications">
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
        });
    </script>

<?php 
    include 'footer-frontend.php';
?>