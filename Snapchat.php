<?php
   require_once "./Helper.php";
 
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
            echo json_response($message = $validate, $code = 409);
            die;
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

        return array('id' => $result['campaigns']['campaign']['id']);
    }

    /* 
     *  Borrar campaña 
     */
    function campaign_borrar_snapchat($appid, $access_token, $user_id, $rowdata)
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

        return array('status' => $result['request_status']);

    }

    /* 
     *  Editar campaña 
     */
    function campaign_edit_snapchat($appid, $access_token, $user_id, $rowdata)
    {
        
        $campaignParams = array(
            'name' => $rowdata['name'],
            'ad_account_id' => $rowdata['ad_account_id'],
            'status' => $rowdata['status'],
            'start_time' => $rowdata['start_time'],
            'end_time' => $rowdata['start_time'],
            'id' => $rowdata['id_en_platform'],
        );

        $typeData = array('string', 'string', 'string', 'string', 'string', 'string');

        $validate = validate_type($campaign, $typeData);

        if($validate != "OK") {
            return array('error' => $validate);
        }

        $campaign = campaign_get_snapchat($appid, $access_token, $user_id, $campaignParams['id']);

        if($campaign['request_status'] == "success" && !empty($campaign['campaigns'])) {
            foreach($campaign as $item) {
                if($item['id'] === $campaignParams['id']){
                    $campaignParams['start_time'] = get_format_time(new DateTime($rowdata['start_time']));
                    $campaignParams['end_time'] = get_format_time(new DateTime($rowdata['end_time']));
                    $postFields['campaigns'] = $campaignParams;
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

                    return array('id' => $result['campaigns']['campaign']['id']);

                } else {
                    return array('error' => "campaign not found");
                }
            }
        } else {
            return array('error' => "campaign not found");
        }
        
        
    }

    /* 
     *  Consultar campaña en específico
     */
     function campaign_get_snapchat($appid, $access_token, $user_id, $rowdata) 
     {
        $id = $rowdata['id_en_platform']; 

        $typeData = array('integer');

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

        echo json_response($message = $result, $code = 200);

        return $result;
     }

     /* 
     *  Cambiar estatus de una campaña en específico
     */
     function campaign_estado_snapchat($appid, $access_token, $user_id, $rowdata)
     {

        $campaignParams = array(
            'status' => $rowdata['status'],
            'id' => $rowdata['id_en_platform'],
        );

        $typeData = array('string', 'string', 'string');

        $validate = validate_type($campaignParams, $typeData);

        if($validate != "OK") {
            return array('error' => $validate);
        }

        $campaign = campaign_get_snapchat($appid, $access_token, $user_id, $campaignParams['id']);

        if($campaign['request_status'] == "success" && !empty($campaign['campaigns'])) {
            foreach($campaign as $item) {
                if($item['id'] === $campaignParams['id']){
                    $postFields['campaigns'] = $item;
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

                    echo json_response($message = $result['campaigns']['campaign']['id'], $code = 200);
                } else {
                    echo json_response($message = "campaign not found", $code = 400);
                    die;
                }
            }
        } else {
            echo json_response($message = "campaign not found", $code = 400);
            die;
        }
     }

     function creative_crear_snapchat($appid, $access_token, $user_id, $campaignid, $rowdata)
     {

        $media_id = null;

        $media = array(
            'name' => $rowdata['name'],
            'type' => $rowdata['type'],
            'ad_account_id' => $campaignid,
        );

        $typeData = array('string', 'string', 'string');

        $validate = validate_type($media, $typeData);

        if($validate != "OK") {
            return array('error' => $validate);

        }
       
        $postFields['media'] = $media;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://adsapi.snapchat.com/v1/adaccounts/' . $campaignid . '/media');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postFields));

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer ' . $access_token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

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

        if($result['request_status'] == "success") {
            $media_id = $result['media']['media']['id'];
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
            echo json_response($message = $validate, $code = 409);
            die;
        }
       
        $postFields['creatives'] = $creatives;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://adsapi.snapchat.com/v1/adaccounts/' . $campaignid . '/creativesINIT\"');
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

        echo json_response($message = $result['creatives']['creative']['id'], $code = 200);

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
                if (curl_errno($ch)) {
                    return array('error' => curl_error($ch));
                    
                }
                curl_close($ch);

                return array(
                    'id' => $result['result']['id'],
                    'status' => $result['request_status']
                );

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
                if (curl_errno($ch)) {
                    return array('error' => curl_error($ch));
                }
                curl_close($ch);

                return array(
                    'id' => $result['result']['id'],
                    'status' => $result['request_status']
                );

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
        //To Do
     }

     
