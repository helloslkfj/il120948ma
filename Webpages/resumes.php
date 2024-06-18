<?php
    include_once __DIR__.'/head-internal.php';
?>
<br>
<div class="container">
    <div></div>
    <div class="grid gap-r-15">
        <h1 class="center gentitle">Resumes</h1>
        <div class="grid gap-r-15">
            <div class="grid">
                <i class="center fa-regular fa-cloud-arrow-up fa-9x cloudicon"></i>
                <input type="file" name="resumeupload" id="resumeupload">
                <label for="resumeupload" class="center toppadding bottompadding sidetosidepadding fineprint">Select Resume</label>
            </div>
            <div class="grid gap-r-5">
                <p id="resumeuploadfile" class="center generictext">No resume selected</p>
                <p id="resumeuploaderror" class="center highlight"></p>
            </div>
            <button name="resumeuplaodsubmit" class="center roundbutton">Upload</button>
        </div>
        <div id="resumetextfeedbackmain" class="grid gap-r-10 none">
            <div class="grid gap-r-5">
                <text class="greensuccess">Success! Below is the text we extracted from your document:</text>
                <text>If we missed some of the text in your documen, please delete the document and add your document in the format of a text file. This will ensure we get all the information in your document allowing our AI algorithm to write the best cold emails. </text>
            </div>
            <div class="grid gap-r-5">
                <textarea id="resumetextobtained" readonly class="center generaltextarea" rows=14 cols=1></textarea>
                <button name="resumetextdone" class="center">Done</button>
            </div>
        </div>
        <?php 
            $encresumes = getDatafromSQLResponse(["resumename", "resumelocation", "resumetext", "datentimeinteger", "iv"], executeSQL($conn, "SELECT * FROM resumes WHERE email=? ORDER BY datentimeinteger DESC;", ["s"], [$encemail], "select", "nothing"));
            $decresumes = decryptFullData($encresumes, $key, 4);
        ?>
        <br>
        <div class="grid">
            <h3 class="gensubtitle2 marginbottom">Your Resumes</h3>
            <div class="templateslayout">
                <div class="grid gap-r-15">
                    <?php for($i=0;$i<count($decresumes);$i++) { ?>
                        <div class="individualtemprow">
                            <text class="underline generictext"><?php echo $decresumes[$i][0]; ?></text>
                            <em class="generictext"> <?php echo "Last updated ".date("F d, Y", (int)$decresumes[$i][3])." at ".date("h:i A", (int)$decresumes[$i][3])." EST"; ?></em>
                        </div>
                    <?php } ?>
                </div>
                <div class="grid gap-r-15">
                    <?php for($i=0;$i<count($decresumes);$i++) {?>
                        <div class="individualtemprow">
                            <i name="deleteresume" value="<?php echo $decresumes[$i][0]; ?>" class="fa-sharp fa-solid fa-circle-trash center fa-2xl trashicon"></i>
                            <button id="<?php echo $decresumes[$i][0]."||dasf-kkx.kk-afasf||".$decresumes[$i][1]; ?>" name="resumeviewbutton" class="roundbutton">View</button>
                        </div>
                    <?php }?>
                </div>
            </div>
        </div>
    </div>
    <div></div>
</div>

<br>
<br>
<br>
<br>
<br>
<br>

<!-- have a feature where we send the text back and then it shows it and you click done, just to let the user know what we extracted -->
<script type="text/javascript">
    function showTextExtract (response) {
        var responsearr = response.split("|.|");
        if(responsearr[0] == 'true') {
            $("#resumetextfeedbackmain").removeClass("none");
            $("#resumeuploaderror").html("");
            console.log(responsearr[1]);
            $("#resumetextobtained").html(responsearr[1]);
        }
        else {
            $("#resumetextfeedbackmain").addClass("none");
            $("#resumeuploaderror").html(response);
        }
    }

    $(document).ready(()=> {
        $("input[name='resumeupload']").change(()=>{
            var resume_file = $("input[name='resumeupload']").prop('files')[0];
            $("#resumeuploadfile").html(resume_file.name);
        });

        $("button[name='resumeuplaodsubmit']").click(()=>{
            var resume_file = $("input[name='resumeupload']").prop('files')[0];
            var resumeformdata = new FormData();
            resumeformdata.append('resume', resume_file);

            //have a big AJAX request here and show the text extracted with done button
            sendAJAXRequest('../PHP-backened/resumeupload-backened.php', resumeformdata, showTextExtract);
        });

        $("button[name='resumetextdone']").click(() => {
            reLoad('true');
        });

        $("i[name='deleteresume']").click(function() {
            var deleteformdata = new FormData();

            var resumetitle = $(this).attr("value");

            deleteformdata.append("delete", true);
            deleteformdata.append("resumetitle", resumetitle);

            var confirmation = confirm('Are you sure you want to delete this resume?');

            if(confirmation == true) {
                sendAJAXRequest('../PHP-backened/deleteresumes-backened.php', deleteformdata, reLoad);
            }
        });

        $("button[name='resumeviewbutton']").click(function() {

            var resumeinfoarr = $(this).attr("id").split("||dasf-kkx.kk-afasf||");

            var resumename = resumeinfoarr[0];
            var resumelocation = resumeinfoarr[1];

            

            window.open(resumelocation, '_blank'); //need to figure out later how to add title to the document that you are navigating to
        });
    });
</script>

<?php 
    include_once __DIR__.'/footer-frontend.php';
?>