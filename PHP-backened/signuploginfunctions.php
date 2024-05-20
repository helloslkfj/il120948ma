<?php 

    function createUserObject($userobject, $fname, $email, $signuppassword, $subscription, $subscriptionstat) {
        $userobject->fname = $fname;
        $userobject->email = $email;
        $userobject->signuppassword = $signuppassword;
        $userobject->subscription = $subscription;
        $userobject->subscriptionstat = $subscriptionstat;
        return $userobject;
    }

    function loginFailure() {
        echo "Email or password is incorrect";
    }
?>