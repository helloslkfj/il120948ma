<?php  
    include_once __DIR__.'/head-internal.php'; 
    include_once __DIR__.'/../PHP-backened/header-backened-code/start-backened.php';
    include_once __DIR__.'/../PHP-backened/secrets.php'; //secrets includes the secrets code and the head-background-friend.php which has the commonfunctions and all other functions needed for programming
?>
<br>
<div class="container">
    <div></div>
    <div class="grid gap-r-15">
        <div class="grid gap-r-5">
            <div class="poppins center size50">Templates</div>
            <form class="grid gap-r-10" method="POST" onsubmit="return false" enctype="multipart/form-data">
                <div class="grid"> 
                    <?php 
                        if(isset($_SESSION["template"])) {
                    ?> 
                        <div class="generaltwocolumns center gap-c-10">
                            <em class="generictext"><?php echo $_SESSION["template"]->title; ?></em>
                            <button name="exittemplate" class="thinbutton">Exit</button>
                        </div>

                        <script>
                            $(document).ready(()=> {
                                $("button[name='exittemplate']").click(()=>{
                                    var exittemplatedata = new FormData();

                                    exittemplatedata.append('exit', true);

                                    var exitconfirmation = confirm("Are you sure you want to exit the template? Any changes made will not be saved unless you click the update button.")

                                    if(exitconfirmation == true) {
                                        sendAJAXRequest('../PHP-backened/deletetemplates-backened.php', exittemplatedata, reLoad);
                                    }
                                });
                            });
                        </script>
                    <?php } ?>
                    <br>

                    <text class="poppins size30">Template Title</text>
                    <?php 
                        if(isset($_SESSION["template"])) {
                    ?>
                    <input class="generateinput" name="templatetitle" type="text" placeholder="Ex. Professor outreach" value="<?php echo $_SESSION["template"]->title; ?>">
                    <?php 
                        } else {
                    ?>
                    <input class="generateinput" name="templatetitle" type="text" placeholder="Ex. Professor outreach">
                    <?php  } ?>
                    <text id="templatetitleerror" class="highlight"></text>
                </div>
                <br>
                <div class="grid">
                    <text class="poppins size30">Text of the Template</text>
                    <?php if(isset($_SESSION["template"])) { ?>
                    <textarea class="generateoutput" name="templatetext" type="text" placeholder="Ex. blah blah blah" rows=15 cols=1><?php echo $_SESSION["template"]->text; ?></textarea>
                    <?php } else { ?>
                        <textarea class="generateoutput" name="templatetext" type="text" placeholder="Ex. blah blah blah" rows=15 cols=1></textarea>
                    <?php } ?>
                    <p id="templatetexterror" class="highlight"></p>
                </div>
                <?php 
                    if(isset($_SESSION["template"])) {
                ?>
                    <button name="updatetemplate" type="submit" class="squarebutton">Update</button>
                <?php 
                    } else {
                ?>
                    <button name="createnewtemplate" type="submit" class="squarebutton">Create New</button>
                <?php } ?>
            </form>
            <div class="grid">
                <br>
                <text>General Error:</text>
                <p id="templategeneralerror" class="highlight"></p>
            </div>
        </div>
        <br>
        <br>
        <?php 
            $enctemplates = getDatafromSQLResponse(["email", "title", "textt", "datentimeinteger", "iv"], executeSQL($conn, "SELECT * FROM templates WHERE email=? ORDER BY datentimeinteger DESC;", ["s"], [$encemail], "select", "nothing"));
            // have exit feature wehn updating

            //make sure to unset the session variable of templates when deleting templates, have a delete button (trash icon) --> it should make sure before deleting
            $dectemplates = decryptFullData($enctemplates, $key, 4);
            $dectemplates = order2DArray_BasedOnValue($dectemplates, 3, "DESC");
        ?>
        <div class="grid gap-r-5">
            <div class="poppins size30 marginbottom">Your Templates</div>
            <div class="templateslayout">
                <div class="grid gap-r-15">
                    <?php 
                        for($i=0;$i<count($dectemplates);$i++) {
                    ?>  
                        <div class="individualtemprow">
                            <text class="underline poppins size20"><?php echo $dectemplates[$i][1]; ?></text>
                            <em class="poppins size20 weight200"> <?php echo "Last updated ".date("F d, Y", (int)$dectemplates[$i][3])." at ".date("h:i A", (int)$dectemplates[$i][3])." EST"; ?></em>
                        </div>
                    <?php } ?>
                </div>
                <div class="grid gap-r-15">
                    <?php 
                        for($i=0;$i<count($dectemplates);$i++) {
                    ?>       
                        <div class="individualtemprow">
                            <i name="deletetemplate" value="<?php echo $dectemplates[$i][1]; ?>" class="fa-sharp fa-solid fa-circle-trash center fa-2xl trashicon"></i>
                            <button id="<?php echo $dectemplates[$i][1]; ?>" name="templateviewbutton" class="linebutton poppins size20">Edit/View</button>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

    </div>
    <div></div>
    
    
</div>

<br>
<br>
<br>

<div class="width90 auto">
<a>
    <button name="backbuttonforresumesandtemplates" class="backbutton worksans size20">Back</button>
</a>
</div>

<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br><br>
<br>
<br>
<br>
<br>

<script type="text/javascript">
    $(document).ready(() => {
        var url = "../PHP-backened/templates-backened.php";

        $("button[name='backbuttonforresumesandtemplates']").click(()=>{
            window.location.assign(document.referrer);
        });

        $("input[name='templatetitle']").keyup(()=> {
            var templatetitledata = createDataObject(["input[name='templatetitle']"], ["templatetitle"]);
            errorHandle(url, templatetitledata, "#templatetitleerror");
        });

        $("textarea[name='templatetext']").keyup(()=> {
            var templatetextdata = createDataObject(["textarea[name='templatetext']"], ["templatetext"]);
            errorHandle(url, templatetextdata, "#templatetexterror");
        });

        $("button[name='createnewtemplate']").click(() => {
            var templateformdata = createFormDataObject(["input[name='templatetitle']", "textarea[name='templatetext']"], ["templatetitle", "templatetext"]);
            keyUpAllElements(["input[name='templatetitle']", "textarea[name='templatetext']"]);
            sendAJAXRequest2(url, templateformdata, reLoadandErrorHandle, "#templategeneralerror");
        });

        $("button[name='updatetemplate']").click(() => {
            var templateformdata = createFormDataObject(["input[name='templatetitle']", "textarea[name='templatetext']"], ["templatetitle", "templatetext"]);
            keyUpAllElements(["input[name='templatetitle']", "textarea[name='templatetext']"]);
            sendAJAXRequest2(url, templateformdata, reLoadandErrorHandle, "#templategeneralerror");
        });

        $("button[name='templateviewbutton']").click(function() { //arrow functions get the this value from just the environment and how it was created, while the normal function looks at what object called it to get this
            var viewformdata = new FormData();
            var templatetitle = $(this).attr('id');

            viewformdata.append('view', true);
            viewformdata.append('templatetitle', templatetitle);

            sendAJAXRequest('../PHP-backened/viewtemplate-backened.php', viewformdata, reLoad);
        });

        $("i[name='deletetemplate']").click(function() {
            var deleteformdata = new FormData();
            var templatetitle = $(this).attr('value');

            deleteformdata.append('delete', true);
            deleteformdata.append('templatetitle', templatetitle);

            var confirmation = confirm('Are you sure you want to delete this template?');

            if(confirmation == true) {
                sendAJAXRequest('../PHP-backened/deletetemplates-backened.php', deleteformdata, reLoad);
            }
        });

    });
</script>

<?php 
    include 'footer-frontend.php';
?>