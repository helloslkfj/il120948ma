<?php 
    include_once __DIR__.'/head-internal.php';
    include_once __DIR__.'/../PHP-backened/header-backened-code/headerincludes-backened.php';
    //finish the emails interface where you can edit them and search different ones
    //add pdf upload ability for writing emails
?>
<br>
<br>
<div class="generaldashspace">
    <div></div>
    <div class="grid gap-r-15">
        <h2>Your Emails</h2>
        <div class="searchgrid gap-c-10">
            <input name="searchemails" placeholder="Search..."></input>
            <div class="left">
                <button name="" class="center sidetosidepadding">Search</button>
            </div>
        </div>
        <br>
        <div class="grid gap-r-25">
            <?php if(isset($_SESSION["search"])) { ?>
                <em>Results:</em> 
                <!-- loading emails when search query is set; put the results thing with the first loaded email-->
            <?php } else {
                $encresearchemaildata = getDatafromSQLResponse(["emailid", "resemailsubject", "resemailtext", "datentimeinteger", "iv"], executeSQL($conn, "SELECT * FROM researchemails WHERE useremail=?", ["s"], [$encemail], "select", "nothing"));
                $decresearchemaildata = insertElementIntoTwoDarray(deleteIndexesfrom2DArray(decryptFullData($encresearchemaildata, $key, 4), [4]), 'Research');
                
                //later on include corporate email data as well and combine the arrays (array_merge (have the same 4 indexes))

                $totalemailarr = order2DArray_BasedOnValue($decresearchemaildata, 3, "DESC");
                $emailidsarr = [];

                for($i=0;$i<count($totalemailarr);$i++) {
                    $emailidsarr[] = $totalemailarr[$i][0];
                }

                $emailidstext = implode("|.|", $emailidsarr);
            ?>  
            <script class="text/javascript">
                function makeAllOthersCopy(emailidsarr, uniqueid) {
                    for(let i=0; i<emailidsarr.length; i++) {
                        if(emailidsarr[i] != uniqueid) {
                            var buttonname = `copyemail|${emailidsarr[i]}`;
                            $(`button[name='${buttonname}']`).html("Copy");
                        }
                    }
                }

                var emailidstext = "<?php echo $emailidstext; ?>"
                var emailidsarr = emailidstext.split("|.|");

            </script>
            <?php for($i=0;$i<count($totalemailarr);$i++) { ?>
                <div class="grid boxshadow">
                    <div class="majoremailgrid">
                        <div></div>
                        <div class="grid gap-r-10">
                            <br>
                            <div class="grid">
                                <h4>Subject</h4>
                                <input name="<?php echo "subject|".$totalemailarr[$i][0]; ?>" value="<?php echo $totalemailarr[$i][1]; ?>"></input>
                            </div>
                            <div class="grid">
                                <h4>Email</h4>
                                <textarea name="<?php echo "email|".$totalemailarr[$i][0]; ?>" rows=14 cols=1><?php echo $totalemailarr[$i][2]; ?></textarea>
                            </div>
                            <br>
                        </div>
                        <div class="right">
                            <br>
                            <img height="20">
                            <text class="center purple">Type: <?php echo $totalemailarr[$i][4]?></text>
                            <br>
                        </div>
                        <div></div>
                    </div>
                    <div class="saveandcopygrid">
                        <div>
                            <text class="sidetosidepadding">If the <em>Save</em> button is not popping up despite changes: 1. change a character 2. click <em>Save</em> 3. change that character back 4. click <em>Save</em> once again.</text>
                        </div>
                        <div class="grid">
                            <div class="generaltwocolumns">
                                <div id="<?php echo "saveemailholder".$totalemailarr[$i][0]; ?>" class="grid">
                                    <text>Saved</text>
                                    <!-- <button name="<?php // echo "saveemailbtn|".$totalemailarr[$i][0]; ?>" class="center sidetosidepadding dynamic">Save</button> -->
                                </div>
                                <button name="<?php echo 'copyemail|'.$totalemailarr[$i][0]; ?>" class="center sidetosidepadding">Copy</button>
                            </div>
                            <br>
                        </div>
                        <div></div>
                    </div>

                </div>

                <script type="text/javascript">
                    $(document).ready(()=>{
                        $("input[name='<?php echo "subject|".$totalemailarr[$i][0]; ?>'], textarea[name='<?php echo "email|".$totalemailarr[$i][0]; ?>']").on('keyup', ()=>{
                            var emaildata = createFormDataObject(["input[name='<?php echo "subject|".$totalemailarr[$i][0]; ?>']", "textarea[name='<?php echo "email|".$totalemailarr[$i][0]; ?>']"], ["emailsubject", "emailbody"]);
                            emaildata.append('emailid', '<?php echo $totalemailarr[$i][0];?>');
                            emaildata.append('type', '<?php echo $totalemailarr[$i][4];?>');
                            emaildata.append('saveCheck', true);

                            sendAJAXRequest("../PHP-backened/emailsinterfacebackened.php", emaildata, function(input) {
                                if(input == "notsaved") {
                                    $("<?php echo "#saveemailholder".$totalemailarr[$i][0]; ?>").html("<button name='<?php echo 'saveemailbtn|'.$totalemailarr[$i][0]; ?>' class='center sidetosidepadding dynamic'>Save</button>");
                                } else {
                                    $("<?php echo "#saveemailholder".$totalemailarr[$i][0]; ?>").html("<text>Saved</text>");
                                }
                            });
                        }); 

                        $("<?php echo "#saveemailholder".$totalemailarr[$i][0]; ?>").on('click', '.dynamic',()=>{
                            var emaildata = createFormDataObject(["input[name='<?php echo "subject|".$totalemailarr[$i][0]; ?>']", "textarea[name='<?php echo "email|".$totalemailarr[$i][0]; ?>']"], ["emailsubject", "emailbody"]);
                            emaildata.append('emailid', '<?php echo $totalemailarr[$i][0];?>');
                            emaildata.append('type', '<?php echo $totalemailarr[$i][4];?>');
                            emaildata.append('save', true);

                            sendAJAXRequest("../PHP-backened/emailsinterfacebackened.php", emaildata, function(input) {
                                if(input == "true") {
                                    $("<?php echo "#saveemailholder".$totalemailarr[$i][0]; ?>").html("<text>Saved</text>");
                                } else {
                                    $("<?php echo "#saveemailholder".$totalemailarr[$i][0]; ?>").html("<button name='<?php  echo 'saveemailbtn|'.$totalemailarr[$i][0]; ?>' class='center sidetosidepadding dyanmic'>Save</button>");
                                }
                            });
                        });

                        $("button[name='<?php echo 'copyemail|'.$totalemailarr[$i][0]; ?>']").click(()=>{
                            var emailsubject = $("input[name='<?php echo "subject|".$totalemailarr[$i][0]; ?>']").val();
                            var emailbody = $("textarea[name='<?php echo "email|".$totalemailarr[$i][0]; ?>']").val();

                            var totalemailtext= emailsubject+"\n\n"+emailbody;
                            navigator.clipboard.writeText(totalemailtext);

                            $("button[name='<?php echo 'copyemail|'.$totalemailarr[$i][0]; ?>']").html("Copied");
                            makeAllOthersCopy(emailidsarr, "<?php echo $totalemailarr[$i][0]; ?>");

                        });

                    });
                </script>

                    
            <?php } } ?>
        </div>
    </div>
    <div></div>
</div>

<br>
<?php 
    include_once __DIR__.'/footer-frontend.php';
?>  