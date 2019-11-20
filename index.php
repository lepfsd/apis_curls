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
        'country_code' => "us",
        'start_time' => "2016-08-11T22:03:58.869Z",
        'campaign_id' => "08f8c50f-e37a-4757-899c-6fd47c398f46",
        'name' => "Ad Squad Uno",
        'type' => "SNAP_ADS",
        'placement' => "SNAP_ADS",
        'optimization_goal' => "IMPRESSIONS",
        'bid_micro' => 1000000,
        'daily_budget_micro' => 1000000000,
        'billing_event' => "IMPRESSION",
    );
    $data = file_get_contents('php://input');
    $json = json_decode(file_get_contents('php://input'), true);
    print("<pre>".print_r($json)."</pre>");
   
   //$res =  creative_crear_snapchat("", $access_token, "", "", $add_account_id, $rowdata);
   //$res = adgroup_crear_snapchat("", $access_token, "", "08f8c50f-e37a-4757-899c-6fd47c398f46", $rowdata);
   
  // print("<pre>".print_r($json)."</pre>");die;
   
    //f2d1a164-f9b9-41df-9ce4-da167a391255 
?>

