<?php 
    //using select command in sql to select data so you fetch it and return it; designed for select commands
    // also designed for executing sql command on database; for insert commands and delete commands
    function executeSQL($conn, $sql, $typearray, $vararray, $typeofexecution, $ivindex) {
        if($conn->connect_error) {
            return "Connection failed";
        }

        $response = ""; 

        // when fundemental assumption of try block is not maintained then an exception is thrown.
        try {
            $stmt = $conn->prepare($sql);
            if ($typearray != "nothing" or $vararray != "nothing"){
                $params_array = array_merge([implode("", $typearray)], $vararray);
                $params_ref = [];
                foreach ($params_array as $key => &$value) {
                    $params_ref[$key] = &$value;
                }
                call_user_func_array(array($stmt, 'bind_param'), $params_ref);
            }
            if($ivindex != "nothing") {
                $stmt ->send_long_data($ivindex, $vararray[$ivindex]);
            }

            $stmt->execute();
            if($typeofexecution == "select") {
                $result = $stmt->get_result();
                $response = $result->fetch_all(MYSQLI_ASSOC); //numerical for each row and then associate inside each row
            }
        }
        catch (Exception $e) {
            echo "Could not execute sql", $e -> getMessage();
            exit("<br>Problem!");
        }

        return $response;
    }

    //obtains any amount of attributes from your SQL response of the database
    function getDatafromSQLResponse($attributes, $response) {
        $data_array = [];
        foreach ($response as $res) {
            $input_array = [];
            for($i=0; $i<count($attributes); $i++) {
                $input_array[] = $res[$attributes[$i]];
            }
            $data_array[] = $input_array;
        }

        return $data_array;
    }

    function collapse2DArrayto1D($twoDarray) {
        $oned_array = [];

        for ($i=0;$i<count($twoDarray);$i++) {
            for ($g=0;$g<count($twoDarray[$i]);$g++) {
                $oned_array[] = $twoDarray[$i][$g];
            }
        }

        return $oned_array;
    }

    function encryptSetofData($data, $key) {
        $encryptedset = [];
        $iv = random_bytes(16);
        for($i=0;$i<count($data);$i++) {
            try {
                $encryptedset[] = openssl_encrypt($data[$i], "AES-128-CTR", $key, 0, $iv);
            }
            catch (Exception $e) {
                echo "Could not encrypt the set of data", $e -> getMessage();
                exit("<br>Problem!");
            }
        }

        $totalencryptionset= [base64_encode($iv), $encryptedset];
        return $totalencryptionset;
    }

    //Encrypts an array of data and returns an encrypted array; Encryption is done based on provided key and iv
    //iv passed in is of type base64
    function encryptDataGivenIv($data, $key, $iv) {
        $encryptedset = [];
        for($i=0; $i<count($data);$i++) {
            try {
                $encryptedset[] = openssl_encrypt($data[$i], "AES-128-CTR", $key, 0, base64_decode($iv));
            }
            catch (Exception $e) {
                echo "Could not encrypt the set of data", $e -> getMessage();
                exit("<br>Problem!");
            }
        }

        return $encryptedset;
    }

    //when the count of the data array is one
    function encryptSingleDataGivenIv($data, $key, $iv) {
        $encdataarr = encryptDataGivenIv($data, $key, $iv);
        $encdata = $encdataarr[0];
        return $encdata;
    }

    //takes a single array of data (one input in a database) -> iv is always at the end
    function decryptSetofData($data, $key, $indexofIV) {
        $unencryptedset = [];

        for($i=0;$i<count($data);$i++) {
            if($i != $indexofIV) {
                try {
                    $unencryptedset[] = openssl_decrypt($data[$i], "AES-128-CTR", $key, 0, base64_decode($data[$indexofIV]));
                }
                catch (Exception $e) {
                    echo "Could not decrypt the set of data", $e -> getMessage();
                    exit("<br>Problem!");
                }
            }
        }

        $unencryptedset[] = $data[$indexofIV]; //the iv is always there in base64 at the last index

        return $unencryptedset;
    }

    //takes the full 2D array of data (many inputs of database), and unencrypts all of them
    function decryptFullData($data, $key, $indexofIV) {
        $fullunencryptedset = [];

        for($i=0;$i<count($data);$i++) {
            $fullunencryptedset[] = decryptSetofData($data[$i], $key, $indexofIV);
        }

        return $fullunencryptedset;
    }

    //function that orders a 2d array based on a specific index in the single arrays of the 2d array
    function order2DArray_BasedOnValue($twodarray, $indexoforder, $typeofordering) {
        $ordered2darr = [];

        for($i=0;$i<count($twodarray); $i++) {
            for($g=$i+1; $g<count($twodarray); $g++) {
                if($typeofordering == "ASC") {
                    if ($twodarray[$i][$indexoforder] > $twodarray[$g][$indexoforder]) {
                        $informationholder = $twodarray[$g];
                        $twodarray[$g] = $twodarray[$i];
                        $twodarray[$i] = $informationholder;
                    }
                }
                else if($typeofordering == "DESC") {
                    if ($twodarray[$i][$indexoforder] < $twodarray[$g][$indexoforder]) {
                        $informationholder = $twodarray[$g];
                        $twodarray[$g] = $twodarray[$i];
                        $twodarray[$i] = $informationholder;
                    }
                }
            }
        }

        $ordered2darr =  $twodarray;

        return $ordered2darr;
    }

    function generateRandomNum($length) {
        $randomnum = [];
        for ($i=0; $i<$length; $i++) {
            $randomnum[] = rand(0, 9);
        }

        $actrandnum = implode("", $randomnum);

        return $actrandnum;
    }

    function deleteIndexesfrom2DArray($twodarray, $indexesofdeletion) {
        $new2darray = [];
        for($i=0; $i<count($twodarray); $i++) {
            $new1darray = [];
            for($z=0; $z<count($twodarray[$i]); $z++) {
                if(in_array($z, $indexesofdeletion) != true) {
                    $new1darray[] = $twodarray[$i][$z];
                }
            }
            $new2darray[] = $new1darray;
        }

        return $new2darray;
    }

    function getAllElementsin1Dfrom2Darr($arr) {
        $oneD_arr = array();
        for($i=0;$i<count($arr);$i++) {
            for($g=0; $g<count($arr[$i]);$g++) {
                $oneD_arr[] = $arr[$i][$g];
            }
        }

        return $oneD_arr;
    }

    function getSpecificAttributeDecryptedinList($attribute, $table, $conn, $key) {
        $encattributearr = getDatafromSQLResponse([$attribute, "iv"], executeSQL($conn, "SELECT * FROM ".$table, "nothing", "nothing", "select", "nothing"));
        $decattributearr = getAllElementsin1Dfrom2Darr(deleteIndexesfrom2DArray(decryptFullData($encattributearr, $key, 1), [1]));
        return $decattributearr;
    }

    function insertElementIntoTwoDarray($twodarray, $newelementvalue) {
        for($i=0;$i<count($twodarray);$i++) {
            $twodarray[$i][] = $newelementvalue;
        }

        return $twodarray;
    }

    //use this function when conducting string comparision
    function normalizeString($str) {
        // Trim leading and trailing whitespace
        $str = trim($str);

        // Replace multiple whitespace characters with a single space
        $str = preg_replace('/\s+/', ' ', $str);

        // Normalize newlines to "\n"
        $str = str_replace(["\r\n", "\r"], "\n", $str);

        // Remove non-printable control characters (except newline, carriage return, and tab)
        $str = preg_replace('/[^\P{C}\n\r\t]/u', '', $str);

        // Convert encoding to UTF-8

        return $str;
    }
    

?>