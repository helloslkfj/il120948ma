<?php 

    function createUserObject($userobject, $fname, $email, $signuppassword, $verification, $subscription, $subscriptionstat, $iv) {
        $userobject->fname = $fname;
        $userobject->email = $email;
        $userobject->signuppassword = $signuppassword;
        $userobject->verification = $verification;
        $userobject->subscription = $subscription;
        $userobject->subscriptionstat = $subscriptionstat;
        $userobject->iv = $iv;
        return $userobject;
    }

    function loginFailure() {
        echo "Email or password is incorrect";
    }

    function sendVerificationMail($userinfo, $verificationnumber, $sendgridapi_key) {
        $email = $userinfo->email;
        $nameofrecepient = $userinfo->fname;

        $subject = "Your Calliope Verification";
        $body = "Hello ".$nameofrecepient.", <br><br>Welcome to Calliope! The following is your verification code: ".$verificationnumber;
        
        $headers = [
            'Authorization: Bearer '.$sendgridapi_key,
            'Content-Type: application/json'
        ];

        $data = [
            "personalizations" => [[
                    "to" => [[
                        "email" => $email,
                        "name" => $nameofrecepient
                    ]]
                ]],
            "from" => [
                "email" => "team@calliopeai.ca"
            ],
            "subject" => $subject,
            "content" => [[
                    "type" => "text/html",
                    "value" => $body
            ]]
        ];

        $resp = "k"; 

        try {
            $vmail = curl_init();
            curl_setopt($vmail, CURLOPT_URL, 'https://api.sendgrid.com/v3/mail/send');
            curl_setopt($vmail, CURLOPT_POST, true);
            curl_setopt($vmail, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($vmail, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($vmail, CURLOPT_RETURNTRANSFER, true);
    
            $resp = curl_exec($vmail);
            curl_close($vmail);
        }
        catch (Exception $e) {
            echo "Error:", $e->getMessage();
            exit("Verification email failed to send! Refresh and try to resend!");
        }

        return $resp;
    }
?>