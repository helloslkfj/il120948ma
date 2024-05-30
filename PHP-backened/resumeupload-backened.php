<?php 
    include_once __DIR__.'/header-backened-nonfriend.php';

    if(isset($_FILES['resume'])) {
        $allowedresumetypes = ['pdf', 'docx', 'doc', 'txt'];
        $sizeinmb = 5;

        $resumename = $_FILES['resume']['name'];
        $resumetmplocation = $_FILES['resume']['tmp_name'];

        $resumenameparts = explode('.', $resumename);
        $resumeext = strtolower(end($resumenameparts));

        $checkresume = fileChecker($allowedresumetypes, $sizeinmb, $_FILES['resume'], "resume");
        if($checkresume != "true") {
            exit($checkresume);
        }
        else {
            //The location will automatically be unique and there is no algorithm needed in order to assure of that as the chances are 10^20

            $encresumenames = getDatafromSQLResponse(["resumename", "iv"], executeSQL($conn, "SELECT resumename, iv FROM resumes WHERE email=?", ["s"], [$encemail], "select", "nothing"));
            $decresumenames = collapse2DArrayto1D(getDatafromSQLResponse([0], decryptFullData($encresumenames, $key, 1)));
            
            if(in_array($resumename, $decresumenames)) {
                echo "Change your resume file name as a resume with this name already exists";
                exit("<br>Problem!");
            }

            $resumeloc = "../WebFiles/Resumes/".generateRandomNum(20);

            $text = '';

            if ($resumeext == "pdf") {
                $text = pdftoText($resumetmplocation);
            }
            else if ($resumeext == "docx") {
                $text = extractTextWithFormatting($resumetmplocation, "nothing");
            }
            else if ($resumeext == "doc") {
                $text = extractTextWithFormatting($resumetmplocation, "MsDoc");
            }
            else if ($resumeext == "txt") {
                $text = file_get_contents($resumetmplocation);
            }

            $resumeinputarr = [$resumename, $resumeloc, $text, strtotime(date("Y-m-d H:i:s"))];
            if(gettype( $resumeinputarr[3]) == 'boolean') {
                exit("Error: Unix timestamp not obtained!");
            }
            $encresumeinputarr = encryptDataGivenIv($resumeinputarr, $key, $_SESSION["user"]->iv);

            executeSQL($conn, "INSERT INTO resumes(email, resumename, resumelocation, resumetext, datentimeinteger, iv) VALUES(?, ?, ?, ?, ?, ?);", ["s", "s", "s", "s", "s", "s"], [$encemail, $encresumeinputarr[0], $encresumeinputarr[1], $encresumeinputarr[2], $encresumeinputarr[3], $_SESSION["user"]->iv], "insert", 5);
            move_uploaded_file($resumetmplocation, $resumeloc);

            echo "true|.|".$text;
        }
    }
?>