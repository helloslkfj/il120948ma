<?php 
    include_once __DIR__.'/head-home.php';
?>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

<div class="width90 auto">
  <div class="white center worksans weight700 size70">
      <span id="typing-text"></span>
      <span class="cursortext">|</span>
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
<br>
<br>

<script>

// script.js
document.addEventListener("DOMContentLoaded", function() {
    const text = "Our mission is to help you get the job you deserve.";
    const typingSpeed = 80; // Milliseconds per character
    let index = 0;

    function type() {
        if (index < text.length) {
            document.getElementById("typing-text").innerHTML += text.charAt(index);
            index++;
            setTimeout(type, typingSpeed);
        } else {
            document.querySelector(".cursortext").style.display = 'none'; // Remove cursor after typing
        }
    }

    type();
});



</script>

<?php 
    include 'footer-frontend.php';
?>