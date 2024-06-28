<?php 
    include_once __DIR__.'/head-internal.php';
?>

    <div class="purplefill">
        <br>
        <br>
        <br>
        <br>
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
                            <br>

                            <div class="worksans weight200 white">
                                Generate emails tailored to specific professors and labs.
                            </div>

                            <br>
                            <br>

                        </button>
                    </a>
                </div>

                <div>
                    <a href="corporate-interface.php">
                        <button class="roundborder purplefill pointer">
                            
                            <div class="worksans weight300 white size20">Corporate</div>
                            <br>

                            <div class="worksans weight200 white">
                                Generate emails & messages to network and get the role at your dream company.
                            </div>

                            <br>

                        </button>
                    </a>
                </div>

                <div>
                    <a href="emails-interface.php">
                        <button class="roundborder purplefill pointer">
                            <div class="worksans weight300 white size20">
                                Your Outreach

                            </div>
                            <br>

                            <div class="worksans weight200 white">
                                Your comprehensive record of emails & messages.
                            </div>
                            <br>
                            <br>
                        </button>
                    </a>
                </div>



            </div>

            
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

   

<?php
    include __DIR__.'/footer-frontend.php';
?>