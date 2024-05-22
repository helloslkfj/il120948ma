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
            exit("Problem!");
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
                exit("Problem!");
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
                exit("Problem!");
            }
        }

        return $encryptedset;
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
                    exit("Problem!");
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



?>