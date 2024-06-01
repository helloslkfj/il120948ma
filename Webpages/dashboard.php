<?php 
    include_once __DIR__.'/head-internal.php';
?>

    <div class="grid">
        <div class="outerdashnav">
            <div class="innerdashnav">
                <div class="grid">
                    <text class="textlink" id="research">Research</text>
                </div>
                <div class="grid">
                    <text class="textlink" id="corporate">Corporate</text>
                </div>
                <div class="grid">
                    <text class="textlink" id="analytics">Analytics</text>
                </div>
            </div>
            <div></div>
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
        <div id="pageload">

        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(() => {
            $("#research").click(()=>{
                $("#pageload").load("research-interface.php");
            })
        })

    </script>

<?php
    include 'footer-frontend.php';
?>