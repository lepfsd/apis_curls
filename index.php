<?php
    error_reporting(0);
    require_once "./SnapchatApi.php";
    require_once "./tokens.php";

    $access_token = $snapchat['access_token'];
    $add_account_id = $snapchat['adacc_account_id'];

    /*$rowdata = array(
        'name' => "Cool Campaign 33",
        'ad_account_id' => "959649dc-29e2-40ac-b1b8-934cd5764741",
        'status' => "PAUSED",
        'start_time' => "2016-08-11T22:03:58.869Z",
        'end_time' => "2016-08-11T22:03:58.869Z",
        'id_en_platform' => "7e63722b-4c3c-4456-93f7-a02e9cf04c09" 
    ); */

    $rowdata = array(
        'name' => "Media A - Video",
        'ad_account_id' => "959649dc-29e2-40ac-b1b8-934cd5764741",
        'type' => "VIDEO",
    );

   //$res =  creative_crear_snapchat("", $access_token, "", "", $add_account_id, $rowdata);
   $res = upload_image_snapchat($access_token, "b349116e-9d4e-4bf2-8825-5d33d9509b03");
   print("<pre>".print_r($res)."</pre>");die;
   
    //f2d1a164-f9b9-41df-9ce4-da167a391255 
?>

