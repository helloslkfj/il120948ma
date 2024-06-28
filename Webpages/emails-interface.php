<?php 
    include_once __DIR__.'/head-internal.php';
    include_once __DIR__.'/../PHP-backened/header-backened-code/headerincludes-backened.php';
    //finish the emails interface where you can edit them and search different ones
    //add pdf upload ability for writing emails
?>
<br>
<br>
<div class="container">
    <div></div>
    <div class="grid gap-r-15">
        <div class="poppins size50 center">Your Outreach</div>
        <br>
        <div class="searchgrid gap-c-10">
            <input class="generateinput" name="searchemailsandmessages" placeholder="Search..."></input>
            <div class="right">
                <button name="search" class="center searchbutton width90 weight200 white worksans size20">Search</button>
            </div>
        </div>
        <p id="searcherror" class="highlight"></p>
        <br>
        <div class="grid gap-r-25">
            <?php if(isset($_SESSION["search"])) { ?>
                <em>Results:</em> 
                
                <!-- loading emails when search query is set; put the results thing with the first loaded email-->
            <?php }

                if(isset($_SESSION["search"])) {
                    $totalemailarr = order2DArray_BasedOnValue($_SESSION["search"], 3, "DESC");
                }
                else {
                    $encresearchemaildata = getDatafromSQLResponse(["emailid", "resemailsubject", "resemailtext", "datentimeinteger", "iv"], executeSQL($conn, "SELECT * FROM researchemails WHERE useremail=?", ["s"], [$encemail], "select", "nothing"));
                    $decresearchemaildata = insertElementIntoTwoDarray(deleteIndexesfrom2DArray(decryptFullData($encresearchemaildata, $key, 4), [4]), 'Research Email');
                
                    //later on include corporate email data as well and combine the arrays (array_merge (have the same 4 indexes))
                    $enccorporateemaildata = getDatafromSQLResponse(["emailid", "corporateemailsubject", "corporateemailtext", "datentimeinteger", "iv"], executeSQL($conn, "SELECT * FROM corporateemails WHERE useremail=?", ["s"], [$encemail], "select", "nothing"));
                    $deccorporateemaildata = insertElementIntoTwoDarray(deleteIndexesfrom2DArray(decryptFullData($enccorporateemaildata, $key, 4), [4]), 'Corporate Email');

                    //load up the messages and then handle them differently in terms of the viewing and stuff -> have to then link saving to emails and messages (copying and viewing works --> no backened involved)
                    $enccorporatemessagedata = getDatafromSQLResponse(["messageid", "corporatemessagetext", "datentimeinteger", "iv"], executeSQL($conn, "SELECT * FROM corporatemessages WHERE useremail=?;", ["s"], [$encemail], "select", "nothing"));
                    $deccorporatemessagedata = deleteIndexesfrom2DArray(decryptFullData($enccorporatemessagedata, $key, 3), [3]);
                    $deccorporatemessagedata = insertElementIntoTwoDarray(insertnonStaticElementofTwodArrayIntoTwoDarray($deccorporatemessagedata, $deccorporatemessagedata, 2), 'LinkedIn Message');


                    //total email arr contains all the user's messages and emails
                    $totalemailarr = order2DArray_BasedOnValue(array_merge($decresearchemaildata, $deccorporateemaildata, $deccorporatemessagedata), 3, "DESC");
                }

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
            <?php for($i=0;$i<count($totalemailarr);$i++) { 
                if($totalemailarr[$i][4] == "Research Email" || $totalemailarr[$i][4] == "Corporate Email") {
            ?>
                <div class="grid greyborder roundcorner">
                    <div class="majoremailgrid">
                        <div></div>
                        <div class="grid gap-r-10">
                            <br>
                            <div class="grid marginbottom">
                                <text class="poppins size20">Subject</text>
                                <input class="generateinput" name="<?php echo "subject|".$totalemailarr[$i][0]; ?>" value="<?php echo $totalemailarr[$i][1]; ?>"></input>
                            </div>

                            <div class="left">
                                <button name="<?php echo "viewbtn|".$totalemailarr[$i][0]; ?>" class="center roundbutton">View</button>
                            </div>
                     

                            <div id="<?php echo "emailcover".$totalemailarr[$i][0]; ?>" class="grid none">
                                <br>
                                <text class="poppins size20">Email</text>
                                <textarea class="generateoutput" name="<?php echo "email|".$totalemailarr[$i][0]; ?>" rows=14 cols=1><?php echo $totalemailarr[$i][2]; ?></textarea>
                            </div>
                            <br>
                        </div>
                        <div class="right">
                            <br>
                            <img height="20">
                            <text class="center poppins weight200 purple">Type: <?php echo $totalemailarr[$i][4]?></text>
                            <br>
                        </div>
                        <div></div>
                    </div>
                    <div id="<?php echo "bottomsectioncover".$totalemailarr[$i][0]; ?>" class="saveandcopygrid none">
                        <div>
                            <div class="poppins sidetosidepadding">If the <em>Save</em> button is not popping up despite changes: 1. change a character 2. click <em>Save</em> 3. change that character back 4. click <em>Save</em> once again.</div>
                        </div>
                        <div class="grid">
                            <div class="generaltwocolumns">
                                <div id="<?php echo "saveemailholder".$totalemailarr[$i][0]; ?>" class="grid poppins size20">
                                    <text class="poppins size20">Saved</text>
                                    <!-- <button name="<?php // echo "saveemailbtn|".$totalemailarr[$i][0]; ?>" class="center sidetosidepadding dynamic linebutton">Save</button> -->
                                </div>
                                <button name="<?php echo 'copyemail|'.$totalemailarr[$i][0]; ?>" class="center linebutton size20">Copy</button>
                            </div>
                            <br>
                        </div>
                        <div></div>
                    </div>

                </div>

                <script type="text/javascript">
                    $(document).ready(()=>{
                        $("button[name='<?php echo 'viewbtn|'.$totalemailarr[$i][0]; ?>']").click(()=> {
                            $("<?php echo '#emailcover'.$totalemailarr[$i][0]; ?>").toggleClass('none');
                            $("<?php echo '#bottomsectioncover'.$totalemailarr[$i][0]; ?>").toggleClass('none');

                            if($("button[name='<?php echo 'viewbtn|'.$totalemailarr[$i][0]; ?>']").html() == "View") {
                                $("button[name='<?php echo 'viewbtn|'.$totalemailarr[$i][0]; ?>']").html("Close");
                            }
                            else {
                                $("button[name='<?php echo 'viewbtn|'.$totalemailarr[$i][0]; ?>']").html("View");
                            }
                        });

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

                <?php } else {?>
                    <div class="grid greyborder roundcorner">
                        <div class="majoremailgrid">
                            <div></div>
                            <div class="grid gap-r-10">
                                <br>
                                <div class="grid marginbottom">
                                    <text class="poppins size20">Message</text>
                                    <input class="generateinput" value="<?php echo substr($totalemailarr[$i][1], 0, 75)."...."; ?>" readonly></input>
                                </div>

                                <div class="left">
                                    <button name="<?php echo "viewbtn|".$totalemailarr[$i][0]; ?>" class="center roundbutton">View</button>
                                </div>
                        

                                <div id="<?php echo "messagecover".$totalemailarr[$i][0]; ?>" class="grid none">
                                    <br>
                                    <textarea class="generateoutput" name="<?php echo "message|".$totalemailarr[$i][0]; ?>" rows=14 cols=1><?php echo $totalemailarr[$i][1]; ?></textarea>
                                </div>
                                <br>
                            </div>
                            <div class="right">
                                <br>
                                <img height="20">
                                <text class="center poppins weight200 purple">Type: <?php echo $totalemailarr[$i][4]?></text>
                                <br>
                            </div>
                            <div></div>
                        </div>
                        <div id="<?php echo "bottomsectioncover".$totalemailarr[$i][0]; ?>" class="saveandcopygrid none">
                            <div>
                                <div class="poppins sidetosidepadding">If the <em>Save</em> button is not popping up despite changes: 1. change a character 2. click <em>Save</em> 3. change that character back 4. click <em>Save</em> once again.</div>
                            </div>
                            <div class="grid">
                                <div class="generaltwocolumns">
                                    <div id="<?php echo "savemessageholder".$totalemailarr[$i][0]; ?>" class="grid poppins size20">
                                        <text class="poppins size20">Saved</text>
                                        <!-- <button name="<?php // echo "savemessagebtn|".$totalemailarr[$i][0]; ?>" class="center sidetosidepadding dynamic linebutton">Save</button> -->
                                    </div>
                                    <button name="<?php echo 'copyemail|'.$totalemailarr[$i][0]; ?>" class="center linebutton size20">Copy</button>
                                </div>
                                <br>
                            </div>
                            <div></div>
                        </div>

                    </div>

                    <script type="text/javascript">
                        $(document).ready(()=>{
                            $("button[name='<?php echo 'viewbtn|'.$totalemailarr[$i][0]; ?>']").click(()=> {
                                $("<?php echo '#messagecover'.$totalemailarr[$i][0]; ?>").toggleClass('none');
                                $("<?php echo '#bottomsectioncover'.$totalemailarr[$i][0]; ?>").toggleClass('none');

                                if($("button[name='<?php echo 'viewbtn|'.$totalemailarr[$i][0]; ?>']").html() == "View") {
                                    $("button[name='<?php echo 'viewbtn|'.$totalemailarr[$i][0]; ?>']").html("Close");
                                }
                                else {
                                    $("button[name='<?php echo 'viewbtn|'.$totalemailarr[$i][0]; ?>']").html("View");
                                }
                            });

                            $("textarea[name='<?php echo "message|".$totalemailarr[$i][0]; ?>']").on('keyup', ()=>{
                                var messagedata = createFormDataObject(["textarea[name='<?php echo "message|".$totalemailarr[$i][0]; ?>']"], ["message"]);
                                messagedata.append('messageid', '<?php echo $totalemailarr[$i][0];?>');
                                messagedata.append('type', '<?php echo $totalemailarr[$i][4];?>');
                                messagedata.append('saveCheck', true);

                                sendAJAXRequest("../PHP-backened/emailsinterfacebackened.php", messagedata, function(input) {
                                    if(input == "notsaved") {
                                        $("<?php echo "#savemessageholder".$totalemailarr[$i][0]; ?>").html("<button name='<?php echo 'savemessagebtn|'.$totalemailarr[$i][0]; ?>' class='center sidetosidepadding dynamic'>Save</button>");
                                    } else {
                                        $("<?php echo "#savemessageholder".$totalemailarr[$i][0]; ?>").html("<text>Saved</text>");
                                    }
                                });
                            }); 

                            $("<?php echo "#savemessageholder".$totalemailarr[$i][0]; ?>").on('click', '.dynamic',()=>{
                                var messagedata = createFormDataObject(["textarea[name='<?php echo "message|".$totalemailarr[$i][0]; ?>']"], ["message"]);
                                messagedata.append('messageid', '<?php echo $totalemailarr[$i][0];?>');
                                messagedata.append('type', '<?php echo $totalemailarr[$i][4];?>');
                                messagedata.append('save', true);

                                sendAJAXRequest("../PHP-backened/emailsinterfacebackened.php", messagedata, function(input) {
                                    if(input == "true") {
                                        $("<?php echo "#savemessageholder".$totalemailarr[$i][0]; ?>").html("<text>Saved</text>");
                                    } else {
                                        $("<?php echo "#savemessageholder".$totalemailarr[$i][0]; ?>").html("<button name='<?php  echo 'savemessagebtn|'.$totalemailarr[$i][0]; ?>' class='center sidetosidepadding dyanmic'>Save</button>");
                                    }
                                });
                            });

                            $("button[name='<?php echo 'copyemail|'.$totalemailarr[$i][0]; ?>']").click(()=>{
                                var messagebody = $("textarea[name='<?php echo "message|".$totalemailarr[$i][0]; ?>']").val();

                                navigator.clipboard.writeText(messagebody);

                                $("button[name='<?php echo 'copyemail|'.$totalemailarr[$i][0]; ?>']").html("Copied");
                                makeAllOthersCopy(emailidsarr, "<?php echo $totalemailarr[$i][0]; ?>");

                            });

                        });
                    </script>
                <?php } ?>
            <?php }?>

            <script type="text/javascript">
                $(document).ready(()=> {
                    $("button[name='search']").click(()=>{
                        var searchdata = createFormDataObject(["input[name='searchemailsandmessages']"], ["searchqry"]);
                        searchdata.append("search", true);

                        sendAJAXRequest2("../PHP-backened/emailsinterfacebackened.php", searchdata, reLoadandErrorHandle, "#searcherror");

                    });
                });
            </script>


        </div>
    </div>
    <div></div>
    <br>
    <br>
</div>

<br>
<br>
<br>
<br>

<?php 
    include_once __DIR__.'/footer-frontend.php';
?>  