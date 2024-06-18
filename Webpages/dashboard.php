<?php 
    include_once __DIR__.'/head-internal.php';
?>

    <div>
        <br>
        <br>
        <br>
        <div>

            <div class="generalthreecolumns fit auto centernongrid">

     

                <div>
                    <a href="research-interface.php">
                        <button class="roundborder purplefill pointer">
                           <div class="worksans weight300 white size20">
                                Research
                            </div>

                            <div class="worksans weight200 white">
                                Generate emails tailored to specific professors and labs.
                            </div>

                        </button>
                    </a>
                </div>

                <div>
                    <a href="">
                        <button class="roundborder purplefill worksans weight300 white pointer size20">Corporate</button>
                    </a>
                </div>

                <div>
                    <a href="emails-interface.php">
                        <button class="roundborder purplefill pointer">
                            <div class="worksans weight300 white size20">
                                Your Emails

                            </div>

                            <div class="worksans weight200 white">
                                Your comprehensive record of research and corporate emails.
                            </div>
                        </button>
                    </a>
                </div>



            </div>

            
        </div>
        <br>

        <?php 
            if(isset($_GET['verification'])) {
        ?>
            <text class="greensuccess"><?php echo $_GET['verification']; ?></text>
            <script type="text/javascript">
                $(document).ready(()=>{
                    setTimeout(function() {
                        window.location.replace('dashboard.php');
                    }, 5000);
                });
            </script>
        <?php } ?>
        <br>
    
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
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>

<?php
    include __DIR__.'/footer-frontend.php';
?>