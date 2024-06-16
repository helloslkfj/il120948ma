<?php 
    include_once __DIR__.'/head-internal.php';
?>

    <div class="grid">
        <div class="outerdashnav">
            <div class="innerdashnav">
                <div class="grid">
                    <a class="textlink" href="research-interface.php">Research</a>
                </div>
                <div class="grid">
                    <a class="textlink" href="">Corporate</a>
                </div>
                <div class="grid">
                    <a class="textlink" href="emails-interface.php">Your Emails</a>
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
    
    </div>

<?php
    include __DIR__.'/footer-frontend.php';
?>