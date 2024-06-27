<?php 
    include_once __DIR__.'/head-home.php';
?>

<!--Add the html code for the home page below (you are in the body tag of the homepage)-->

<div class="main1">

    <div class="slogan">
      Let us do the writing.
    </div>

    <br>
    <br>
    <br>

    <div class="grid2">
      <div class="blurb1">
        <span id="typing-text"></span>
        <span class="cursortext">|</span>
      </div>

      <div>
        <img class="paperairplane" src="../Images/paperairplanecoloured.png" height="300">
      </div>

    </div>

  </div>

  <div class="main2">
    
    <div class="grid2">

      <div class="forstudents">

        <div class="subtitle">
          For students, by students.
        </div>

        <div class="blurb2">
          Calliope was created to help you introduce yourself to employers.
          <br>
          <br>
          Cold emailing is part of the game. A long, unnecessarily tedious part.
          <br>
          <br>
          You have to spend hours looking into labs, firms, and people so you can convey genuine interest when you email them.
          <br>
          <br>
          We do all that for you.
        </div>
        

      </div>

      <div class="howwork">

        <div class="subtitle">
          How does Calliope work?
        </div>

        <div class="blurb2">
          All you have to do is input links to the websites of the places you're interested in.
          <br>
          <br>
          Calliope will comb those websites for data and use them to write detailed, organization-specific emails.
          <br>
          <br>
          <strong>Then, you send the email and get hired.</strong>

        </div>
        
      </div>

    </div>
    

  </div>

   

  </div>
 

</div>

<script>

// script.js
document.addEventListener("DOMContentLoaded", function() {
    const text = "Calliope drafts personalized cold emails so you can focus on school, work, and life.";
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