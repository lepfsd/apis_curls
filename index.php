<?php
    require_once "./Snapchat.php";
    
    $rowdata = array(
        'name' => "Cool Campaign",
        'ad_account_id' => "3b0fbace-04b4-4f04-a425-33b5e0af1d0d",
        'status' => "PAUSED",
        'start_time' => "2016-08-11T22:03:58.869Z",
        'id' => 1
    );

    //campaign_borrar_snapchat("", "", "", "", "", $rowdata);

    
    $a1=array("a"=>"red","b"=>"green","c"=>"blue","d"=>"yellow");
    $a2=array("a"=>"red","b"=>"green","c"=>"blue");
    
    $result = array_intersect($a1, $a2);
    print_r($result);

?>
