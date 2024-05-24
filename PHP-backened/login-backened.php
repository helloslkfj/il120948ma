<?php 
    include_once __DIR__.'/header-backened-friend.php';

    if(isset($_POST['loginemail'], $_POST['loginpassword'])) {
        $loginemail = mysqli_real_escape_string($conn, $_POST['loginemail']);
        $loginpassword = mysqli_real_escape_string($conn, $_POST['loginpassword']);

        $encuserresp = getDatafromSQLResponse(["fname", "email", "pass", "verification", "typofsubscription", "subscriptionstat", "iv"], executeSQL($conn, "SELECT * from users ORDER BY id ASC", "nothing", "nothing", "select", "nothing"));
        $decuserinfos = decryptFullData($encuserresp, $key, 6);

        $loginoccurance = 0;
        foreach($decuserinfos as $decuserinfo) {
            if($loginemail == $decuserinfo[1]){
                if($loginpassword == $decuserinfo[2]) {
                    echo "true";
                    $loginoccurance += 1;

                    
                    $userobj = new stdClass();
                    $_SESSION["user"] = createUserObject($userobj, $decuserinfo[0], $decuserinfo[1], $decuserinfo[2], $decuserinfo[3], $decuserinfo[4], $decuserinfo[5], $decuserinfo[6]);
                    break;
                }
            }
        }

        if($loginoccurance == 0) {
            loginFailure();
        } 
        
    }
    else {
        loginFailure();
    }
?>