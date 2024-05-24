<?php 

    function createTemplateObject($obj, $email, $title, $text, $datentimeinteger, $iv) {
        $obj->email = $email;
        $obj->title = $title;
        $obj->text = $text;
        $obj->datentimeinteger = $datentimeinteger;
        $obj->iv = $iv;

        return $obj;
    }
?>