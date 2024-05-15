<?php 
    include 'header.php';
?>

        <script type="text/javascript">
            var empty = new FormData();
            sendAJAXRequest("PHP-backened/webscraperfunctions.php", empty, doNothing);
        </script>

<?php 
    include 'footer.php';
?>