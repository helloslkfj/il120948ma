<?php
    include 'head-inside-internal.php';
?>

    <!-- Here create the inputs for research and the generate button-->

    <div class="generaldashspace">
        <div></div>
        <div class="grid gap-r-15">
            <div class="research-inputgrid">
                <input name="professorwebpage" type="text" placeholder="Link to webpage that is dedicated to the professor">
                <div></div>
                <input name="publicationwebpage" type="text" placeholder="Link to one of the professor's publications">
            </div>
            <!-- payment integration with stripe will be done later we will just have saved, whether subscription of the user is active or not, thats all we need
            Stripe handles the reccuring billing by itself-->
            <button class="generatebutton center" name="generateresearch">Generate</button>

        </div>
        <div></div>
    </div>

<?php 
    include 'footer-frontend.php';
?>