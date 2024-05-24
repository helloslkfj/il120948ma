<?php 
    include_once __DIR__.'/header-backened-friend.php';

    $key = collapse2DArrayto1D(getDatafromSQLResponse(["actualkey"], executeSQL($conn, "SELECT actualkey FROM secrets WHERE keyname='key';", "nothing", "nothing", "select", "nothing")))[0];
    $sendgridapi_key = collapse2DArrayto1D(getDatafromSQLResponse(["actualkey"], executeSQL($conn, "SELECT actualkey FROM secrets WHERE keyname='sendgridapikey';", "nothing", "nothing", "select", "nothing")))[0];
    $Open_API_Key = collapse2DArrayto1D(getDatafromSQLResponse(["actualkey"], executeSQL($conn, "SELECT actualkey FROM secrets WHERE keyname='openaiapikey';", "nothing", "nothing", "select", "nothing")))[0];
?>