<?php
   require_once "./Helper.php";
   require_once "./tokens.php";
   
 
    /* 
     *  Crear campaña 
     */
    function campaign_crear_snapchat($app_id, $app_secret,$access_token,$userid, $add_account_id,$rowdata)
    {
        
        $campaign = array(
            'name' => $rowdata['name'],
            'ad_account_id' => $add_account_id,
            'status' => $rowdata['status'],
            'start_time' => $rowdata['start_time'],
        );
        
        $typeData = array('string', 'string', 'string', 'string');

        $validate = validate_type($campaign, $typeData);

        if($validate != "OK") {
            return array('error' => $validate);
        }
        
        $campaign['start_time'] = get_format_time(new DateTime($rowdata['start_time']));
        //te recomiendo usas el codigo que te envie, la campana ya estaba ok
        $postFields['campaigns'] = [$campaign];
        
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
        $json = json_decode($result, true);
        
        if((curl_errno($ch)) && ($json['request_status'] == "ERROR"))  {
            switch($json['error']) {
                case 'invalidtoken':
                    $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
                    $headers[] = 'Authorization: Bearer ' . $access_token;
                    $result = curl_exec($ch);
                default:
                    return procesaerrores_snapchat($result['error']);
            }
        }

        curl_close($ch);

        $response = array();
        
        if($json['request_status'] == "ERROR") {
            $response = array(
                'request_status' => $json['request_status'],
                'request_id' => $json['request_id'],
                'debug_message' => isset($json['debug_message']) ? $json['debug_message'] : "",
                'display_message' => isset($json['display_message']) ? $json['display_message'] : "",
                'error_code' => isset($json['error_code']) ? $json['error_code'] : "",
                'error_code' => isset($json['campaigns'][0]['sub_request_error_reason']) ? $json['campaigns'][0]['sub_request_error_reason'] : "",
            );
        } else if($json['request_status'] == "SUCCESS") {
            $response = array(
                'request_status' => $json['request_status'],
                'request_id' => $json['request_id'],
                'display_message' => "Campaign created successfylly",
                'campaigns' => $json['campaigns'],
            );
        }

        return $response;
    }

    /* 
     *  Borrar campaña 
     */
    function campaign_borrar_snapchat($appid, $access_token, $userid, $rowdata)
    {   
        
        $id = $rowdata['id_en_platform'];

        $typeData = array('string');

        $data = array($id);

        $validate = validate_type($data, $typeData);
        
        if($validate != "OK") {
            return array('error' => $validate);
        }
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://adsapi.snapchat.com/v1/campaigns/' . $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');


        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $access_token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
       
        $json = json_decode($result, true);

        if (curl_errno($ch)) {
            return array('error' => curl_error($ch));
        }
        
        if((curl_errno($ch)) && ($json['request_status'] == "ERROR"))  {
            switch($json['error']) {
                case 'invalidtoken':
                    $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
                    $headers[] = 'Authorization: Bearer ' . $access_token;
                    $result = curl_exec($ch);
                default:
                    return procesaerrores_snapchat($result['error']);
            }
        }

        curl_close($ch);

        $response = array();
        
        if($json['request_status'] == "ERROR") {
            $response = array(
                'request_status' => $json['request_status'],
                'request_id' => $json['request_id'],
                'debug_message' => isset($json['debug_message']) ? $json['debug_message'] : "",
                'display_message' => isset($json['display_message']) ? $json['display_message'] : "",
                'error_code' => isset($json['error_code']) ? $json['error_code'] : "",
                'error_code' => isset($json['campaigns'][0]['sub_request_error_reason']) ? $json['campaigns'][0]['sub_request_error_reason'] : "",
            );
        } else if($json['request_status'] == "SUCCESS") {
            $response = array(
                'request_status' => $json['request_status'],
                'request_id' => $json['request_id'],
                'display_message' => "Campaign deleted successfylly"
            );
        }

        return $response;

    }

    /* 
     *  Editar campaña 
     */
    function campaign_edit_snapchat($appid, $access_token, $userid, $rowdata)
    {
        
        $campaignParams = array(
            'name' => $rowdata['name'],
            'ad_account_id' => $rowdata['ad_account_id'],
            'status' => $rowdata['status'],
            'start_time' => $rowdata['start_time'],
            'end_time' => $rowdata['start_time'],
            'id' => $rowdata['id_en_platform'],
        );
        
        /*$typeData = array('string', 'string', 'string', 'string', 'string');

        $temp = array();
        $typeTemp = array();

        foreach ($campaignParams as $key => $value) {
            if (!is_null($value) && isset($value)) {
                $temp[] = $value;
                $typeTemp[] = $typeTemp['string'];
            }
        }
        
        $validate = validate_type($temp, $typeTemp); 
       
        if($validate != "OK") {
            return array('error' => $validate);
        }*/
        $id = array('id_en_platform' =>  $campaignParams['id']);
        $campaign = campaign_get_snapchat($appid, $access_token, $userid, $id);
        
        if($campaign && !empty($campaign['campaign'])) {
            $item = $campaign['campaign'];
            
            if($item['id'] === $campaignParams['id']){
                
                $result = array_merge($item, array_intersect_key($campaignParams, $item));
                
                $postFields['campaigns'] = [$result];
                
                $ch = curl_init();
        
                curl_setopt($ch, CURLOPT_URL, 'https://adsapi.snapchat.com/v1/adaccounts/' . $campaignParams['id'] . '/campaigns');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postFields));
        
                $headers = array();
                $headers[] = 'Authorization: Bearer ' . $access_token;
                $headers[] = 'Content-Type: application/json';
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
                $result = curl_exec($ch);

                $json = json_decode($result, true);
                
                if (curl_errno($ch)) {
                    //echo 'Error:' . curl_error($ch);
                    return array('error' => curl_error($ch));                        
                    
                }
        
                if((curl_errno($ch)) && ($json['request_status'] == "ERROR"))  {
                    switch($json['error']) {
                        case 'invalidtoken':
                            $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
                            $headers[] = 'Authorization: Bearer ' . $access_token;
                            $result = curl_exec($ch);
                        default:
                            return procesaerrores_snapchat($result['error']);
                    }
                }
        
                curl_close($ch);

                $response = array();

                if($json['request_status'] == "ERROR") {
                    $response = array(
                        'request_status' => $json['request_status'],
                        'request_id' => $json['request_id'],
                        'debug_message' => isset($json['debug_message']) ? $json['debug_message'] : "",
                        'display_message' => isset($json['display_message']) ? $json['display_message'] : "",
                        'error_code' => isset($json['error_code']) ? $json['error_code'] : "",
                        'error_code' => isset($json['campaigns'][0]['sub_request_error_reason']) ? $json['campaigns'][0]['sub_request_error_reason'] : "",
                    );
                } else if($json['request_status'] == "SUCCESS") {
                    $response = array(
                        'request_status' => $json['request_status'],
                        'request_id' => $json['request_id'],
                        'display_message' => "Campaign created successfylly",
                        'campaigns' => $json['campaigns'],
                    );
                }

                return $response;

            } else {
                return array('error' => "campaign not found");
            }
            
        } else {
            return array('error' => "campaign not found");
        }
        
        
    }

    /* 
     *  Consultar campaña en específico
     */
     function campaign_get_snapchat($appid, $access_token, $userid, $rowdata) 
     {
        $id = $rowdata['id_en_platform']; 
        
        $typeData = array('string');

        $data = array($id);

        $validate = validate_type($data, $typeData);
        
        if($validate != "OK") {
            return array('error' => $validate);
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://adsapi.snapchat.com/v1/campaigns/' . $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $access_token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
    
        $json = json_decode($result, true);

        if (curl_errno($ch)) {
            return array('error' => curl_error($ch));
        }
        
        if((curl_errno($ch)) && ($json['request_status'] == "ERROR"))  {
            switch($json['error']) {
                case 'invalidtoken':
                    $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
                    $headers[] = 'Authorization: Bearer ' . $access_token;
                    $result = curl_exec($ch);
                default:
                    return procesaerrores_snapchat($json['error']);
            }
        }

        curl_close($ch);
        
        $response = array();
        
        if($json['request_status'] == "ERROR") {
            $response = array(
                'request_status' => $json['request_status'],
                'request_id' => $json['request_id'],
                'debug_message' => isset($json['debug_message']) ? $json['debug_message'] : "",
                'display_message' => isset($json['display_message']) ? $json['display_message'] : "",
                'error_code' => isset($json['error_code']) ? $json['error_code'] : "",
                'error_code' => isset($json['campaigns'][0]['sub_request_error_reason']) ? $json['campaigns'][0]['sub_request_error_reason'] : "",
            );
        } else if($json['request_status'] == "SUCCESS") {
            $response = array(
                'campaign' => $json['campaigns'][0]['campaign'] ,
            );
        }

        return $response;
     }

     /* 
     *  Cambiar estatus de una campaña en específico
     */
     function campaign_estado_snapchat($appid, $access_token, $userid, $rowdata)
     {

        $response = campaign_edit_snapchat($appid, $access_token, $userid, $rowdata);

     }

     function creative_crear_snapchat($appid, $access_token, $userid, $campaignid, $add_account_id, $rowdata)
     {

        $media_id = null;

        $media = array(
            'name' => $rowdata['name'],
            'type' => $rowdata['type'],
            'ad_account_id' => $add_account_id,
        );

        $typeData = array('string', 'string', 'string');

        $validate = validate_type($media, $typeData);

        if($validate != "OK") {
            return array('error' => $validate);

        }
       
        $postFields['media'] = [$media];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://adsapi.snapchat.com/v1/adaccounts/' . $add_account_id . '/media');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postFields));

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer ' . $access_token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        $json = json_decode($result, true);
        
        curl_close($ch);

        if((curl_errno($ch)) && ($json['request_status'] == "ERROR"))  {
            switch($json['error']) {
                case 'invalidtoken':
                    $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
                    $headers[] = 'Authorization: Bearer ' . $access_token;
                    $result = curl_exec($ch);
                default:
                    return procesaerrores_snapchat($json['error']);
            }
        }

        if($result['request_status'] == "SUCCESS ") {
            $media_id = $json['media'][0]['media']['id'];
        }
        
        if($media_id === null) {
            return array('error' => "Error to create media");
            
        } else {
            if($rowdata['type'] == "IMAGE") {

                $response = upload_image_snapchat($access_token, $media_id);

                if(isset($response['error'])) {
                    return array('error' => $response['error']);
                }
                
            } else if($rowdata['type'] == "VIDEO" && $rowdata['size'] <= 32097152) { // unidades de bit
                $response = upload_video_snapchat($access_token, $media_id);

                if(isset($response['error'])) {
                    return array('error' => $response['error']);
                }
            }  else if($rowdata['type'] == "VIDEO" && $rowdata['size'] > 32097152) { // unidades de bit
                $response = upload_large_snapchat($access_token, $media_id);

                if(isset($response['error'])) {
                    return array('error' => $response['error']);
                }
            }
        } 

        // creative 
        $typeData = null;

        $creative = array(
            'ad_account_id' => $rowdata['ad_account_id'],
            'top_snap_media_id' => $rowdata['top_snap_media_id'],
            'name' => $rowdata['name'],
            'type' => $rowdata['type'],
            'brand_name' => $brand_name['type'],
            'headline' => $brand_name['headline'],
            'shareable' => $brand_name['shareable'],
        );

        $typeData = array('string', 'string', 'string','string', 'string', 'string', 'boolean');

        $validate = validate_type($creative, $typeData);

        if($validate != "OK") {
            return array('error' => $validate);
            
        }
       
        $postFields['creatives'] = [$creatives];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://adsapi.snapchat.com/v1/adaccounts/' . $ad_account_id . '/creativesINIT\"');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postFields));

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer' . $access_token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if (curl_errno($ch)) {
            return array('error' => curl_error($ch));
            
        }

        if($result['error'])  {
            switch($result['error']) {
                case 'invalidtoken':
                    $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
                    $headers[] = 'Authorization: Bearer ' . $access_token;
                    $result = curl_exec($ch);
                default:
                    return procesaerrores_snapchat($result['error']);
            }
        }

        curl_close($ch);
        $creativeType = array();
        
        switch($rowdata['type']) {
            case 'LONGFORM_VIDEO':
                $videoMediaId = array(
                    'video_media_id' => $media_id
                );
                $videoMediaId = json_encode($videoMediaId);

                $creativeType = array(
                    'ad_account_id' => $add_account_id,
                    'top_snap_media_id' => $rowdata['top_snap_media_id'],
                    'name' => $rowdata['name'],
                    'type' => $rowdata['type'],
                    'shareable' => $rowdata['shareable'],
                    'call_to_action' => $rowdata['call_to_action'],
                    'longform_video_properties' => $videoMediaId,
                );
            case 'APP_INSTALL':
                $properties = array(
                    'app_name' => $rowdata['app_name'],
                    'android_app_url' => $rowdata['android_app_url'],
                    'icon_media_id' => $rowdata['icon_media_id'],
                );
                $properties = json_encode($properties);

                $creativeType = array(
                    'ad_account_id' => $add_account_id,
                    'top_snap_media_id' => $rowdata['top_snap_media_id'],
                    'name' => $rowdata['name'],
                    'type' => $rowdata['type'],
                    'shareable' => $rowdata['shareable'],
                    'call_to_action' => $rowdata['call_to_action'],
                    'app_install_properties' => $properties,
                );
            case 'WEB_VIEW':
                $properties = array(
                    'url' => $rowdata['url'],
                );
                if(isset($rowdata['allow_snap_javascript_sdk'])) {
                    $properties = array(
                        'allow_snap_javascript_sdk' => $rowdata['allow_snap_javascript_sdk'],
                    );
                }
                $properties = json_encode($properties);

                $creativeType = array(
                    'ad_account_id' => $add_account_id,
                    'top_snap_media_id' => $rowdata['top_snap_media_id'],
                    'name' => $rowdata['name'],
                    'type' => $rowdata['type'],
                    'shareable' => $rowdata['shareable'],
                    'call_to_action' => $rowdata['call_to_action'],
                    'app_install_properties' => $properties,
                );
            case 'DEEP_LINK':
                $properties = array(
                    'deep_link_uri' => $rowdata['deep_link_uri'],
                    'ios_app_id' => $rowdata['ios_app_id'],
                    'android_app_url' => $rowdata['android_app_url'],
                    'app_name' => $rowdata['app_name'],
                    'icon_media_id' => $rowdata['icon_media_id'],
                );
                $properties = json_encode($properties);

                $creativeType = array(
                    'ad_account_id' => $add_account_id,
                    'top_snap_media_id' => $rowdata['top_snap_media_id'],
                    'name' => $rowdata['name'],
                    'type' => $rowdata['type'],
                    'shareable' => $rowdata['shareable'],
                    'call_to_action' => $rowdata['call_to_action'],
                    'brand_name' => $rowdata['brand_name'],
                    'top_snap_crop_position' => $rowdata['top_snap_crop_position'],
                    'deep_link_properties' => $properties,
                );
            case 'PREVIEW':
                $properties = array(
                    'preview_media_id' => $rowdata['preview_media_id'],
                    'logo_media_id' => $rowdata['logo_media_id'],
                    'preview_headline' => $rowdata['preview_headline'],
                );
                $properties = json_encode($properties);

                $creativeType = array(
                    'ad_account_id' => $add_account_id,
                    'name' => $rowdata['name'],
                    'type' => $rowdata['type'],
                    'preview_properties' => $properties,
                );
            case 'AD_TO_LENS':
                $properties = array(
                    'lens_media_id' => $rowdata['preview_media_id'],
                );
                $properties = json_encode($properties);

                $creativeType = array(
                    'ad_account_id' => $add_account_id,
                    'name' => $rowdata['name'],
                    'type' => $rowdata['type'],
                    'shareable' => $rowdata['shareable'],
                    'headline' => $rowdata['headline'],
                    'brand_name' => $rowdata['brand_name'],
                    'call_to_action' => $rowdata['call_to_action'],
                    'top_snap_media_id' => $rowdata['top_snap_media_id'],
                    'top_snap_crop_position' => $rowdata['top_snap_crop_position'],
                    'ad_product' => $rowdata['ad_product'],
                    'ad_to_lens_properties' => $properties,
                );
        }

        $postFields['creatives'] = [$creativeType];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://adsapi.snapchat.com/v1/adaccounts/' . $add_account_id . '/creatives');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postFields));

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer ' . $access_token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $resultCreativeType = curl_exec($ch);
        
        curl_close($ch);

        if($resultCreativeType['error'])  {
            switch($resultCreativeType['error']) {
                case 'invalidtoken':
                    $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
                    $headers[] = 'Authorization: Bearer ' . $access_token;
                    $resultCreativeType = curl_exec($ch);
                default:
                    return procesaerrores_snapchat($resultCreativeType['error']);
            }
        }

        curl_close($ch);

        return array(
            'creative_id' =>  $result['creatives']['creative']['id'],
            'creative_type_id' =>  $resultCreativeType['creatives']['creative']['id']
        );

     }

     function upload_image_snapchat($access_token, $media_id)
     {
        if(isset($_FILES['file'])){
            $errors= array();
            $file_name = $_FILES['file']['name'];
            $file_size =$_FILES['file']['size'];
            $file_tmp =$_FILES['file']['tmp_name'];
            $file_type=$_FILES['file']['type'];
            $file_ext=strtolower(end(explode('.',$_FILES['file']['name'])));
            $data = array('name' => $_FILES['file']['name'], 'file' => '@/path/to/'.$_FILES['file']['name']);
            $extensions= array("jpeg","jpg","png","ico");
            
            if(in_array($file_ext,$extensions)=== false){
               $errors[]="extension not allowed, please choose a JPEG or PNG file.";
            }
            
            if($file_size > 2097152){
               $errors[]='File size must be excately 2 MB';
            }
            
            if(empty($errors)==true){
               
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, 'https://adsapi.snapchat.com/v1/media/' . $media_id . '/upload');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                
                curl_setopt($ch, CURLOPT_POSTFIELDS, $_FILES['file']);

                $headers = array();
                $headers[] = 'Content-Type: multipart/form-data';
                $headers[] = 'Authorization: Bearer ' . $access_token;
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $result = curl_exec($ch);
                $json = json_decode($result, true);

                if((curl_errno($ch)) && ($json['request_status'] == "ERROR"))  {
                    switch($json['error']) {
                        case 'invalidtoken':
                            $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
                            $headers[] = 'Authorization: Bearer ' . $access_token;
                            $result = curl_exec($ch);
                        default:
                            return procesaerrores_snapchat($json['error']);
                    }
                }
                curl_close($ch);
                print("<pre>".print_r($json)."</pre>");die;

                return $json;

            }else{
               return array(
                    'errors' => $errors
                );
            }
         } else {
             return array('error' => "No media found");
            
         }
     }

     function upload_video_snapchat($access_token, $media_id)
     {
        if(isset($_FILES['file'])){
            $errors= array();
            $file_name = $_FILES['file']['name'];
            $file_size =$_FILES['file']['size'];
            $file_tmp =$_FILES['file']['tmp_name'];
            $file_type=$_FILES['file']['type'];
            $file_ext=strtolower(end(explode('.',$_FILES['file']['name'])));
            
            $extensions= array("mov","mp4");
            
            if(in_array($file_ext,$extensions)=== false){
               $errors[]="extension not allowed, please choose a MOV or MP4 file.";
            }
            
            if(empty($errors)==true){
               
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, 'https://adsapi.snapchat.com/v1/media/' . $media_id . '/upload');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                
                curl_setopt($ch, CURLOPT_POSTFIELDS, $_FILES['file']);

                $headers = array();
                $headers[] = 'Content-Type: multipart/form-data';
                $headers[] = 'Authorization: Bearer ' . $access_token;
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $result = curl_exec($ch);
                $json = json_decode($result, true);

                if((curl_errno($ch)) && ($json['request_status'] == "ERROR"))  {
                    switch($json['error']) {
                        case 'invalidtoken':
                            $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
                            $headers[] = 'Authorization: Bearer ' . $access_token;
                            $result = curl_exec($ch);
                        default:
                            return procesaerrores_snapchat($json['error']);
                    }
                }
                curl_close($ch);

                return $json;

            }else{
               return array(
                    'errors' => $errors
                );
            }
         }else {
            return array('error' => "No media found");
         }
     }

     function upload_large_snapchat($access_token, $media_id)
     {
        $file_name = $_POST['file_name'];
        $file_size = $_POST['file_size'];
        $number_of_parts = $_POST['number_of_parts'];

        $media = array($file_name, $file_size, $number_of_parts);

        $typeData = array('string', 'integer', 'integer');

        $validate = validate_type($media, $typeData);

        if($validate != "OK") {
            return array('error' => $validate);

        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://adsapi.snapchat.com/v1/media/' . $media_id . '/multipart-upload-v2?action=INIT');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($media));

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $access_token;
        $headers[] = 'Content-Type: multipart/form-data';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        $json = json_decode($result, true);

        if((curl_errno($ch)) && ($json['request_status'] == "ERROR"))  {
            switch($json['error']) {
                case 'invalidtoken':
                    $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
                    $headers[] = 'Authorization: Bearer ' . $access_token;
                    $result = curl_exec($ch);
                default:
                    return procesaerrores_snapchat($json['error']);
            }
        }
        curl_close($ch);

        
        $upload_id = null;
        $add_path = null;
        $finalize_path = null;

        $response = json_decode($result, true);
        $upload_id = $response['upload_id'];
        $add_path = $response['add_path'];
        $finalize_path = $response['finalize_path'];

        curl_close($ch);

        return array( 'result' => $response);
     }

     function upload_part_snapchat($access_token, $media_id)
     {
        $upload_id = $_POST['upload_id'];
        $part_number = $_POST['part_number'];

        $media = array($upload_id, $part_number);

        $typeData = array('string', 'integer');

        $validate = validate_type($media, $typeData);

        if($validate != "OK") {
            return array('error' => $validate);

        }

        if(isset($_FILES['file'])){
            $errors= array();
            $file_name = $_FILES['file']['name'];
            $file_size =$_FILES['file']['size'];
            $file_tmp =$_FILES['file']['tmp_name'];
            $file_type=$_FILES['file']['type'];
            $file_ext=strtolower(end(explode('.',$_FILES['file']['name'])));
            
            $extensions= array("mov","mp4");
            
            if(in_array($file_ext,$extensions)=== false){
               $errors[]="extension not allowed, please choose a MOV or MP4 file.";
            }
            
            if(empty($errors)==true){
               
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, 'https://adsapi.snapchat.com/us/v1/media/' . $media_id . '/multipart-upload-v2?action=ADD');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                
                curl_setopt($ch, CURLOPT_POSTFIELDS, $_FILES['file']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($media));

                $headers = array();
                $headers[] = 'Content-Type: multipart/form-data';
                $headers[] = 'Authorization: Bearer ' . $access_token;
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $result = curl_exec($ch);
                $json = json_decode($result, true);

                if((curl_errno($ch)) && ($json['request_status'] == "ERROR"))  {
                    switch($json['error']) {
                        case 'invalidtoken':
                            $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
                            $headers[] = 'Authorization: Bearer ' . $access_token;
                            $result = curl_exec($ch);
                        default:
                            return procesaerrores_snapchat($json['error']);
                    }
                }
                curl_close($ch);


                $json = json_decode($result, true);

                if((curl_errno($ch)) && ($json['request_status'] == "ERROR"))  {
                    switch($json['error']) {
                        case 'invalidtoken':
                            $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
                            $headers[] = 'Authorization: Bearer ' . $access_token;
                            $result = curl_exec($ch);
                        default:
                            return procesaerrores_snapchat($json['error']);
                    }
                }
                curl_close($ch);

                return $json;

            }else{
               return array(
                    'errors' => $errors
                );
            }
         }else {
            return array('error' => "No media found");
         }
     }

     function upload_part_finalize($access_token, $media_id)
     {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://adsapi.snapchat.com/v1/media/' . $media_id . '/multipart-upload-v2?action=FINALIZE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $access_token;
        $headers[] = 'Content-Type: multipart/form-data';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if($result['error'])  {
            switch($result['error']) {
                case 'invalidtoken':
                    $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
                    $headers[] = 'Authorization: Bearer ' . $access_token;
                    $result = curl_exec($ch);
                default:
                    return procesaerrores_snapchat($result['error']);
            }
        }
        curl_close($ch);

        return array('result' => $result);
     }

     function creative_editar_snapchat($appid, $access_token, $userid, $add_account_id, $rowdata)
     {
        $media_id = null;

        $webProperties = array(
            'url' => $rowdata['url'],
            'allow_snap_javascript_sdk' => $rowdata['allow_snap_javascript_sdk'],
            'block_preload' => $rowdata['block_preload'],
            'type' => $rowdata['type'],
            'ad_product' => $rowdata['ad_product'],
            'top_snap_media_id' => $rowdata['top_snap_media_id'],
            'top_snap_crop_position' => $rowdata['top_snap_crop_position'],
            'name' => $rowdata['name'],
            'call_to_action' => $rowdata['call_to_action'],
            'shareable' => $rowdata['shareable'],
        );

        $webProperties = json_encode($webProperties);

        $creativeParams = array(
            'ad_account_id' => $add_account_id,
            'brand_name' => $rowdata['brand_name'],
            'id' => $rowdata['id'],
            'headline' => $rowdata['headline'],
            'web_view_properties' => $webProperties,
            'status' => $rowdata['status'],
        );

        $typeData = array('string', 'string', 'string', 'string', 'string', 'string',);

        $temp = array();
        $typeTemp = array();

        foreach ($creativeParams as $key => $value) {
            if (!is_null($value) && isset($value)) {
                $temp[] = $value;
                $tipo = $typeData[$key];
                $typeTemp[] = $tipo;
            }
        }

        $validate = validate_type($temp, $typeTemp);

        $creative = creative_get_snapchat($appid, $access_token, $userid, $rowdata);

        if($campaign['request_status'] == "success" && !empty($creative['creatives'])) {
            $item = $creative['creatives'][0];
            if($item['id'] === $creativeParams['id']){
                foreach ($creativeParams as $key => $value) {
                    if (!is_null($value) && isset($value)) {
                        $item[$key] = $value[$key];  
                    }
                }
                $postFields['creatives'] = $item;
                $ch = curl_init();
        
                curl_setopt($ch, CURLOPT_URL, 'https://adsapi.snapchat.com/v1/adaccounts/' . $creativeParams['id'] . '/creatives');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
        
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postFields));
        
                $headers = array();
                $headers[] = 'Authorization: Bearer ' . $access_token;
                $headers[] = 'Content-Type: application/json';
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
                $result = curl_exec($ch);
                if (curl_errno($ch)) {
                    //echo 'Error:' . curl_error($ch);
                    return array('error' => curl_error($ch));                        
                    
                }
        
                if($result['error'])  {
                    switch($result['error']) {
                        case 'invalidtoken':
                            $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
                            $headers[] = 'Authorization: Bearer ' . $access_token;
                            $result = curl_exec($ch);
                        default:
                            return procesaerrores_snapchat($result['error']);
                    }
                }
        
                curl_close($ch);

                if($result['request_status'] == "success") {
                    $media_id = $result['media']['media']['id'];
                }

                if($rowdata['type'] == "IMAGE") {

                    $response = upload_image_snapchat($access_token, $media_id);
    
                    if(isset($response['error'])) {
                        return array('error' => $response['error']);
                    }
                    
                } else if($rowdata['type'] == "VIDEO" && $rowdata['size'] <= 32097152) { // unidades de bit
                    $response = upload_video_snapchat($access_token, $media_id);
    
                    if(isset($response['error'])) {
                        return array('error' => $response['error']);
                    }
                }  else if($rowdata['type'] == "VIDEO" && $rowdata['size'] > 32097152) { // unidades de bit
                    $response = upload_large_snapchat($access_token, $media_id);
    
                    if(isset($response['error'])) {
                        return array('error' => $response['error']);
                    }
                }

            } else {
                return array('error' => "creative not found");
            }
            
        } else {
            return array('error' => "creative not found");
        }

     }

     function creative_get_snapchat($appid, $access_token, $userid, $rowdata)
     {
        $id = $rowdata['id_en_platform']; 

        $typeData = array('integer');

        $data = array($id);

        $validate = validate_type($data, $typeData);
        
        if($validate != "OK") {
            return array('error' => $validate);
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://adsapi.snapchat.com/v1/creatives/' . $id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $access_token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {

            return array('error' => curl_error($ch));     
        }

        if($result['error'])  {
            switch($result['error']) {
                case 'invalidtoken':
                    $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
                    $headers[] = 'Authorization: Bearer ' . $access_token;
                    $result = curl_exec($ch);
                default:
                    return procesaerrores_snapchat($result['error']);
            }
        }

        curl_close($ch);

        return $result;

     }


     function creative_editar_estado_snapchat($appid, $access_token, $userid, $rowdata) 
     {
        $response = creative_editar_snapchat($appid, $access_token, $userid, $rowdata);
     }

     function adgroup_crear_snapchat($appid, $access_token, $user_id, $campaignid, $rowdata)
     {
         $targeting = [];
         $geos = [];

         $geos = array('country_code' => $rowdata['country_code']);
         $geos = json_encode($geos);
         $start_time = get_format_time(new DateTime($rowdata['start_time']));
         $targeting = array(
             'geos' => [$geos],
             'start_time' => $start_time
         );
         $targeting = json_encode($targeting);
         
        $adsquad = array(
            'campaign_id' => $rowdata['campaign_id'],
            'name' => $rowdata['name'],
            'type' => $rowdata['type'],
            'placement' => $rowdata['placement'],
            'optimization_goal' => $rowdata['optimization_goal'],
            'bid_micro' => $rowdata['bid_micro'],
            'daily_budget_micro' => $rowdata['daily_budget_micro'],
            'billing_event' => $rowdata['billing_event'],
            'targeting' => $targeting,
        );

        $typeData = array('string', 'string', 'string', 'string', 'string', 'integer', 'integer',
            'string', 'string');

        $validate = validate_type($adsquad, $typeData);

        //if($validate != "OK") {
        //   return array('error' => $validate);
        //}
        
        $postFields['adsquads'] = [$adsquad];
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, 'https://adsapi.snapchat.com/v1/campaigns/' . $campaignid .'/adsquads');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postFields));

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer ' . $access_token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $json = json_decode($result, true);
        
        if((curl_errno($ch)) && ($json['request_status'] == "ERROR"))  {
            switch($json['error']) {
                case 'invalidtoken':
                    $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
                    $headers[] = 'Authorization: Bearer ' . $access_token;
                    $result = curl_exec($ch);
                default:
                    return procesaerrores_snapchat($result['error']);
            }
        }

        curl_close($ch);

        $response = array();
        
        if($json['request_status'] == "ERROR") {
            $response = array(
                'request_status' => $json['request_status'],
                'request_id' => $json['request_id'],
                'debug_message' => isset($json['debug_message']) ? $json['debug_message'] : "",
                'display_message' => isset($json['display_message']) ? $json['display_message'] : "",
                'error_code' => isset($json['error_code']) ? $json['error_code'] : "",
                'error_code' => isset($json['adsquads'][0]['sub_request_error_reason']) ? $json['adsquads'][0]['sub_request_error_reason'] : "",
            );
        } else if($json['request_status'] == "SUCCESS") {
            $response = array(
                'request_status' => $json['request_status'],
                'request_id' => $json['request_id'],
                'display_message' => "Ad Squad created successfylly",
                'adsquads' => $json['adsquads'],
            );
        }

        return $response;
     }

     
