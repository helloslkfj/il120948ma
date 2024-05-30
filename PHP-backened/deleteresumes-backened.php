<?php
    include_once __DIR__.'/header-backened-nonfriend.php';

    if(isset($_POST['delete'])) {
        if(isset($_POST['resumetitle'])) { 
            $resumetitle = $_POST['resumetitle'];
            
            $encresumetitle = encryptSingleDataGivenIv([$_POST['resumetitle']], $key, $_SESSION["user"]->iv);

            $encresumelocation = getDatafromSQLResponse(["resumelocation", "iv"], executeSQL($conn, "SELECT resumelocation, iv FROM resumes WHERE email=? AND resumename=?;", ["s", "s"], [$encemail, $encresumetitle], "select", "nothing"));
            if(count($encresumelocation) != 1) {
                exit("Error: Multiple locations found for the resume");
            }

            $decresumelocation = decryptFullData($encresumelocation, $key, 1)[0][0];

            unlink($decresumelocation);

            executeSQL($conn, "DELETE FROM resumes WHERE email=? AND resumename=?", ["s", "s"], [$encemail, $encresumetitle], "delete", "nothing");

            echo "true";
        }
    }
?>