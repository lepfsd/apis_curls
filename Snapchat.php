<?php
   require_once "./Helper.php";

   $rowdata = array(
        'name' => "Cool Campaign",
        'ad_account_id' => "3b0fbace-04b4-4f04-a425-33b5e0af1d0d",
        'status' => "PAUSED",
        'start_time' => "2016-08-11T22:03:58.869Z",
    );

    campaign_crear_snapchat("", "", "", "", "", $rowdata);
    
    /* 
     *  Crear campaña 
     */
    function campaign_crear_snapchat($app_id, $app_secret,$access_token,$userid, $add_account_id,$rowdata)
    {
        $campaign = array(
            'name' => $rowdata['name'],
            'ad_account_id' => $rowdata['ad_account_id'],
            'status' => $rowdata['status'],
            'start_time' => $rowdata['start_time'],
        );

        $typeData = array('string', 'string', 'string', 'string');

        $validate = validate_type($campaign, $typeData);

        if($validate != "OK") {
            return json_response($message = $validate, $code = 409);
        }
        
        $campaign['start_time'] = get_format_time(new DateTime($rowdata['start_time']));
        
        $postFields['campaigns'] = $campaign;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://adsapi.snapchat.com/v1/adaccounts/' . $add_account_id . '/campaigns');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postFields));

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $access_token;
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
            return json_response($message = curl_error($ch), $code = 409);
            
        }

        if($result['error'] === "invalid_token") {
            $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
            $headers[] = 'Authorization: Bearer ' . $access_token;
            $result = curl_exec($ch);
        } 

        curl_close($ch);

        return json_response($message = $result['campaigns']['campaign']['id'], $code = 200);
    }

    

    

    


    

    
