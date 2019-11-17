<?php
    require_once "./SnapchatApi.php";
    require_once "./tokens.php";

    $access_token = $snapchat['access_token'];
    $add_account_id = $snapchat['adacc_account_id'];

    $rowdata = array(
        'name' => "Cool Campaign",
        'ad_account_id' => "3b0fbace-04b4-4f04-a425-33b5e0af1d0d",
        'status' => "PAUSED",
        'start_time' => "2016-08-11T22:03:58.869Z",
        'id' => 1
    );

    campaign_crear_snapchat("", "",$access_token,"", $add_account_id,$rowdata);
    //print_r($res);die;
?>
