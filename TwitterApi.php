<?php
   require_once "./Helper.php";

	/* 
     *  Crear campaña 
     */
	function campaign_crear_twitter($appid, $access_token, $userid, $rowdata, $version, $account_id)
	{

		$campaign = array(
            'funding_instrument_id' => $rowdata['funding_instrument_id'],
            'name' => $rowdata['name'],
			'start_time' => $rowdata['start_time'],
			'daily_budget_amount_local_micro' => $rowdata['daily_budget_amount_local_micro'],
			'entity_status' => $rowdata['entity_status'],
        );

        $typeData = array('string', 'string', 'string', 'integer', 'string');

        $validate = validate_type($campaign, $typeData);

        if($validate != "OK") {
            return array('error' => $validate);
        }
        
        $campaign['start_time'] = new DateTime($rowdata['start_time']);
        $fields = array();
		$fields['params'] = json_encode($campaign);
		$fields['operation_type'] = "CREATE";
		
		

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://ads-api.twitter.com/' . $version . '/accounts/' . $account_id . '/campaigns');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [json_encode($fields)]);

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $access_token;
        $headers[] = 'Content-Type: application/json';
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
		
		return $result['data'];

	}

	function campaign_borrar_twitter($appid, $access_token, $userid, $version, $account_id, $campaign_id)
    {   
        
        $typeData = array('string');

        $data = array($campaign_id);

        $validate = validate_type($data, $typeData);
        
        if($validate != "OK") {
            return array('error' => $validate);
        }
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://ads-api.twitter.com/' . $version . '/accounts/' . $account_id . '/campaigns/' . $campaign_id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');


        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $access_token;
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

        return $result['data'];

	}
	
	/* 
     *  Editar campaña 
     */
    function campaign_edit_twitter($appid, $access_token, $userid, $version, $account_id, $campaign_id, $rowdata)
    {
        
        $campaignParams = array(
            'entity_status' => $rowdata['entity_status'],
            'duration_in_days' => $rowdata['duration_in_days'],
            'frequency_cap' => $rowdata['frequency_cap'],
            'name' => $rowdata['name'],
            'standard_delivery' => $rowdata['standard_delivery'],
			'start_time' => $rowdata['start_time'],
			'total_budget_amount_local_micro' => $rowdata['total_budget_amount_local_micro'],
        );

        $typeData = array('string', 'integer', 'integer', 'string', 'boolean', 'string', 'integer');

        $temp = array();
        $typeTemp = array();

        foreach ($campaignParams as $key => $value) {
            if (!is_null($value) && isset($value)) {
                $temp[] = $value;
                $tipo = $typeData[$key];
                $typeTemp[] = $tipo;
            }
        }

        $validate = validate_type($temp, $typeTemp);

        if($validate != "OK") {
            return array('error' => $validate);
        }

        $campaign = campaign_id_get_twitter($appid, $access_token, $userid, $version, $account_id, $campaign_id);

        if(!empty($campaign['data'])) {
            $item = $campaign['data'];
            if($item['id'] === $campaign_id){
                foreach ($campaignParams as $key => $value) {
                    if (!is_null($value) && isset($value)) {
                        $item[$key] = $value[$key];  
                    }
                }
                
                $ch = curl_init();
        
                curl_setopt($ch, CURLOPT_URL, 'https://ads-api.twitter.com/' . $version . '/accounts/' . $account_id . '/campaigns/' .$campaign_id);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($item));
        
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
            
        } else {
            return array('error' => "campaign not found");
        }
        
    }
    
    function campaign_estado_twitter($appid, $access_token, $userid, $version, $account_id, $campaign_id, $rowdata)
    {
        $campaignParams = array(
            'entity_status' => $rowdata['entity_status'],
        );

        return campaign_edit_twitter($appid, $access_token, $userid, $version, $account_id, $campaign_id, $campaignParams);
    }
	
	function campaign_id_get_twitter($appid, $access_token, $userid, $version, $account_id, $campaign_id) 
	{

	   $ch = curl_init();

	   curl_setopt($ch, CURLOPT_URL, 'https://ads-api.twitter.com/' . $version . '/accounts/' . $account_id . '/campaigns/' .$campaign_id);
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

	   $headers = array();
	   $headers[] = 'Authorization: Bearer ' . $access_token;
	   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	   $result = curl_exec($ch);

	   if($result['errors'])  {
		   switch($result['errors']) {
			   case 'invalidtoken':
				   $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
				   $headers[] = 'Authorization: Bearer ' . $access_token;
				   $result = curl_exec($ch);
			   default:
				   return procesaerrores_snapchat($result['error']);
		   }
	   }

	   curl_close($ch);

	   return array('data' => $result['data']);
	}

	
	function accounts_get_twitter($appid, $access_token, $userid, $rowdata, $version) 
	{

	   $ch = curl_init();

	   curl_setopt($ch, CURLOPT_URL, 'https://ads-api.twitter.com/' . $version . '/accounts');
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

	   $headers = array();
	   $headers[] = 'Authorization: Bearer ' . $access_token;
	   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	   $result = curl_exec($ch);

	   if($result['errors'])  {
		   switch($result['errors']) {
			   case 'invalidtoken':
				   $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
				   $headers[] = 'Authorization: Bearer ' . $access_token;
				   $result = curl_exec($ch);
			   default:
				   return procesaerrores_snapchat($result['error']);
		   }
	   }

	   curl_close($ch);

	   return $result['data'];
	}

	function account_id_get_twitter($appid, $access_token, $userid, $rowdata, $version, $account_id) 
	{

	   $typeData = array('string');

	   $data = array($account_id);

	   $validate = validate_type($data, $typeData);
	   
	   if($validate != "OK") {
		   return array('error' => $validate);
	   }

	   $ch = curl_init();

	   curl_setopt($ch, CURLOPT_URL, 'https://ads-api.twitter.com/' . $version . '/accounts/' . $account_id);
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

	   $headers = array();
	   $headers[] = 'Authorization: Bearer ' . $access_token;
	   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	   $result = curl_exec($ch);

	   if($result['errors'])  {
		   switch($result['errors']) {
			   case 'invalidtoken':
				   $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
				   $headers[] = 'Authorization: Bearer ' . $access_token;
				   $result = curl_exec($ch);
			   default:
				   return procesaerrores_snapchat($result['error']);
		   }
	   }

	   curl_close($ch);

	   return $result['data'];
	}

	function funding_instruments_get_twitter($appid, $access_token, $userid, $rowdata, $version, $account_id) 
	{

	   $ch = curl_init();

	   curl_setopt($ch, CURLOPT_URL, 'https://ads-api.twitter.com/' . $version . '/accounts/' . $account_id . '/funding_instruments');
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

	   $headers = array();
	   $headers[] = 'Authorization: Bearer ' . $access_token;
	   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	   $result = curl_exec($ch);

	   if($result['errors'])  {
		   switch($result['errors']) {
			   case 'invalidtoken':
				   $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
				   $headers[] = 'Authorization: Bearer ' . $access_token;
				   $result = curl_exec($ch);
			   default:
				   return procesaerrores_snapchat($result['error']);
		   }
	   }

	   curl_close($ch);

	   return $result['data'];
	}

	
	function funding_instruments_id_get_twitter($appid, $access_token, $userid, $rowdata, $version, $account_id, $funding_instrument_id) 
	{

	   $ch = curl_init();

	   curl_setopt($ch, CURLOPT_URL, 'https://ads-api.twitter.com/' . $version . '/accounts/' . $account_id . '/funding_instruments/' . $funding_instrument_id);
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

	   $headers = array();
	   $headers[] = 'Authorization: Bearer ' . $access_token;
	   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	   $result = curl_exec($ch);

	   if($result['errors'])  {
		   switch($result['errors']) {
			   case 'invalidtoken':
				   $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
				   $headers[] = 'Authorization: Bearer ' . $access_token;
				   $result = curl_exec($ch);
			   default:
				   return procesaerrores_snapchat($result['error']);
		   }
	   }

	   curl_close($ch);

	   return $result['data'];
	}

	function line_items_crear_twitter($appid, $access_token, $userid, $rowdata, $version, $account_id)
	{

		$items = array(
            'campaign_id' => $rowdata['campaign_id'],
            'objective' => $rowdata['objective'],
			'placements' => $rowdata['placements'],
			'product_type' => $rowdata['product_type'],
			'bid_amount_local_micro' => $rowdata['bid_amount_local_micro'],
			'entity_status' => $rowdata['entity_status'],
        );

        $typeData = array('string', 'string', 'string', 'string', 'integer', 'string');

        $validate = validate_type($items, $typeData);

        if($validate != "OK") {
            return array('error' => $validate);
        }
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://ads-api.twitter.com/' . $version . '/accounts/' . $account_id . '/line_items');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($items));

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $access_token;
        $headers[] = 'Content-Type: application/json';
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
		
		return $result['data'];

    }
    
    function line_items_borrar_twitter($appid, $access_token, $userid, $rowdata, $version, $account_id, $line_item_id)
    {   
        
        $typeData = array('string');

        $data = array($line_item_id);

        $validate = validate_type($data, $typeData);
        
        if($validate != "OK") {
            return array('error' => $validate);
        }
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://ads-api.twitter.com/' . $version . '/accounts/' . $account_id . '/line_items/' . $line_item_id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');


        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $access_token;
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

        return $result['data'];

    }
    
    function line_items_id_get_twitter($appid, $access_token, $userid, $version, $account_id, $line_item_id) 
	{

	   $ch = curl_init();

	   curl_setopt($ch, CURLOPT_URL, 'https://ads-api.twitter.com/' . $version . '/accounts/' . $account_id . '/line_items/' .$line_item_id);
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

	   $headers = array();
	   $headers[] = 'Authorization: Bearer ' . $access_token;
	   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	   $result = curl_exec($ch);

	   if($result['errors'])  {
		   switch($result['errors']) {
			   case 'invalidtoken':
				   $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
				   $headers[] = 'Authorization: Bearer ' . $access_token;
				   $result = curl_exec($ch);
			   default:
				   return procesaerrores_snapchat($result['error']);
		   }
	   }

	   curl_close($ch);

	   return array('data' => $result['data']);
    }
    
    function line_items_edit_twitter($appid, $access_token, $userid, $version, $account_id, $line_item_id, $rowdata)
    {
        
        $itemParams = array(
            'advertiser_domain' => $rowdata['advertiser_domain'],
            'advertiser_user_id' => $rowdata['advertiser_user_id'],
            'automatically_select_bid' => $rowdata['automatically_select_bid'],
            'bid_amount_local_micro' => $rowdata['bid_amount_local_micro'],
            'bid_type' => $rowdata['bid_type'],
			'categories' => $rowdata['categories'],
            'end_time' => $rowdata['end_time'],
            'entity_status' => $rowdata['entity_status'],
            'audience_expansion' => $rowdata['audience_expansion'],
            'name' => $rowdata['name'],
            'optimization' => $rowdata['optimization'],
            'start_time' => $rowdata['start_time'],
            'total_budget_amount_local_micro' => $rowdata['total_budget_amount_local_micro'],
            'tracking_tags' => $rowdata['tracking_tags'],
        );

        $typeData = array('string', 'integer', 'boolean', 'integer', 'string', 'string', 'string', 'string',
                'string', 'string', 'string', 'string', 'integer', 'string');

        $temp = array();
        $typeTemp = array();

        foreach ($itemParams as $key => $value) {
            if (!is_null($value) && isset($value)) {
                $temp[] = $value;
                $tipo = $typeData[$key];
                $typeTemp[] = $tipo;
            }
        }

        $validate = validate_type($temp, $typeTemp);

        if($validate != "OK") {
            return array('error' => $validate);
        }

        $line_item = line_items_id_get_twitter($appid, $access_token, $userid, $version, $account_id, $line_item_id);

        if(!empty($line_item['data'])) {
            $item = $line_item['data'];
            if($item['id'] === $line_item_id){
                foreach ($itemParams as $key => $value) {
                    if (!is_null($value) && isset($value)) {
                        $item[$key] = $value[$key];  
                    }
                }
                
                $ch = curl_init();
        
                curl_setopt($ch, CURLOPT_URL, 'https://ads-api.twitter.com/' . $version . '/accounts/' . $account_id . '/line_items/' .$line_item_id);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($item));
        
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
            
        } else {
            return array('error' => "campaign not found");
        }
        
    }

    function line_items_estado_twitter($appid, $access_token, $userid, $version, $account_id, $line_item_id, $rowdata)
    {
        $params = array(
            'entity_status' => $rowdata['entity_status'],
        );

        return line_items_edit_twitter($appid, $access_token, $userid, $version, $account_id, $campaign_id, $params);
    }

	function line_items_apps_crear_twitter($appid, $access_token, $userid, $rowdata, $version, $account_id)
	{

		$items = array(
            'line_item_id' => $rowdata['line_item_id'],
            'app_store_identifier' => $rowdata['app_store_identifier'],
			'os_type' => $rowdata['os_type'],
        );

        $typeData = array('string', 'string', 'string');

        $validate = validate_type($items, $typeData);

        if($validate != "OK") {
            return array('error' => $validate);
        }
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://ads-api.twitter.com/' . $version . '/accounts/' . $account_id . '/line_item_apps');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($items));

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $access_token;
        $headers[] = 'Content-Type: application/json';
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
		
		return $result['data'];

	}

	function targeting_criteria_crear_twitter($appid, $access_token, $userid, $rowdata, $version, $account_id)
	{

		$criteria = array(
            'line_item_id' => $rowdata['line_item_id'],
            'targeting_type' => $rowdata['targeting_type'],
			'targeting_value' => $rowdata['targeting_value'],
        );

        $typeData = array('string', 'string', 'string');

        $validate = validate_type($criteria, $typeData);

        if($validate != "OK") {
            return array('error' => $validate);
        }
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://ads-api.twitter.com/' . $version . '/accounts/' . $account_id . '/targeting_criteria');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($criteria));

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $access_token;
        $headers[] = 'Content-Type: application/json';
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
		
		return $result['data'];

	}

	function targeting_criteria_get_twitter($appid, $access_token, $userid, $version, $targeting_options) 
	{
		/* app_store_categories, behavior_taxonomies, behaviors, conversations, devices, events*/
	   if(null == $targeting_options) 
	   {
		   return array('error' => 'El  targeting_options es requerido');
	   }

	   if($targeting_options)  {
		switch($targeting_options) {
			case 'app_store_categories':
				$url =  'https://ads-api.twitter.com/' . $version . '/targeting_criteria/' . $targeting_options;
			case 'behavior_taxonomies':
				$url =  'https://ads-api.twitter.com/' . $version . '/targeting_criteria/' . $targeting_options;
			case 'behaviors':
					$url =  'https://ads-api.twitter.com/' . $version . '/targeting_criteria/' . $targeting_options;
			case 'conversations':
				$url =  'https://ads-api.twitter.com/' . $version . '/targeting_criteria/' . $targeting_options;
			case 'devices':
				$url =  'https://ads-api.twitter.com/' . $version . '/targeting_criteria/' . $targeting_options;
			case 'events':
				$url =  'https://ads-api.twitter.com/' . $version . '/targeting_criteria/' . $targeting_options;
			case 'interests':
				$url =  'https://ads-api.twitter.com/' . $version . '/targeting_criteria/' . $targeting_options;
			case 'languages':
				$url =  'https://ads-api.twitter.com/' . $version . '/targeting_criteria/' . $targeting_options;
			case 'locations':
				$url =  'https://ads-api.twitter.com/' . $version . '/targeting_criteria/' . $targeting_options;
			case 'network_operators':
				$url =  'https://ads-api.twitter.com/' . $version . '/targeting_criteria/' . $targeting_options;
			case 'platform_versions':
				$url =  'https://ads-api.twitter.com/' . $version . '/targeting_criteria/' . $targeting_options;
			case 'platforms':
				$url =  'https://ads-api.twitter.com/' . $version . '/targeting_criteria/' . $targeting_options;
			case 'tv_markets':
				$url =  'https://ads-api.twitter.com/' . $version . '/targeting_criteria/' . $targeting_options;
			case 'tv_shows':
				$url =  'https://ads-api.twitter.com/' . $version . '/targeting_criteria/' . $targeting_options;
		
		}
	}

	   $ch = curl_init();

	   curl_setopt($ch, CURLOPT_URL, 'https://ads-api.twitter.com/' . $version . '/targeting_suggestions');
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

	   $headers = array();
	   $headers[] = 'Authorization: Bearer ' . $access_token;
	   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	   $result = curl_exec($ch);

	   if($result['errors'])  {
		   switch($result['errors']) {
			   case 'invalidtoken':
				   $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
				   $headers[] = 'Authorization: Bearer ' . $access_token;
				   $result = curl_exec($ch);
			   default:
				   return procesaerrores_snapchat($result['error']);
		   }
	   }

	   curl_close($ch);

	   return $result['data'];
	}

	function targeting_suggestions_get_twitter($appid, $access_token, $userid, $version) 
	{
		/* app_store_categories, behavior_taxonomies, behaviors, conversations, devices, events*/
	   

	   $ch = curl_init();

	   curl_setopt($ch, CURLOPT_URL, $url);
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

	   $headers = array();
	   $headers[] = 'Authorization: Bearer ' . $access_token;
	   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	   $result = curl_exec($ch);

	   if($result['errors'])  {
		   switch($result['errors']) {
			   case 'invalidtoken':
				   $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
				   $headers[] = 'Authorization: Bearer ' . $access_token;
				   $result = curl_exec($ch);
			   default:
				   return procesaerrores_snapchat($result['error']);
		   }
	   }

	   curl_close($ch);

	   return $result['data'];
	}

	function media_creatives_crear_twitter($appid, $access_token, $userid, $rowdata, $version, $account_id)
	{

		$items = array(
            'line_item_id' => $rowdata['line_item_id'],
            'account_media_id' => $rowdata['account_media_id'],
			'account_id' => $account_id,
        );

        $typeData = array('string', 'string', 'string');

        $validate = validate_type($items, $typeData);

        if($validate != "OK") {
            return array('error' => $validate);
        }
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://ads-api.twitter.com/' . $version . '/accounts/' . $account_id . '/media_creatives');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($items));

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $access_token;
        $headers[] = 'Content-Type: application/json';
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
		
		return $result['data'];

	}

	function promoted_accounts_crear_twitter($appid, $access_token, $userid, $rowdata, $version, $account_id)
	{

		$items = array(
            'line_item_id' => $rowdata['line_item_id'],
            'user_id' => $userid,
			'account_id' => $account_id,
        );

        $typeData = array('string', 'string', 'string');

        $validate = validate_type($items, $typeData);

        if($validate != "OK") {
            return array('error' => $validate);
        }
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://ads-api.twitter.com/' . $version . '/accounts/' . $account_id . '/promoted_accounts');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($items));

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $access_token;
        $headers[] = 'Content-Type: application/json';
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
		
		return $result['data'];

	}

	function promoted_tweets_crear_twitter($appid, $access_token, $userid, $rowdata, $version, $account_id)
	{

		$items = array(
            'line_item_id' => $rowdata['line_item_id'],
			'tweet_ids' => $rowdata['tweet_ids'],
        );

        $typeData = array('string', 'string', 'string');

        $validate = validate_type($items, $typeData);

        if($validate != "OK") {
            return array('error' => $validate);
        }
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://ads-api.twitter.com/' . $version . '/accounts/' . $account_id . '/promoted_tweets');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($items));

        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $access_token;
        $headers[] = 'Content-Type: application/json';
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
		
		return $result['data'];

	}

	function promotable_users_get_twitter($appid, $access_token, $userid, $version, $account_id) 
	{
		
	   $ch = curl_init();

	   curl_setopt($ch, CURLOPT_URL,'https://ads-api.twitter.com/' . $version . '/accounts/' . $account_id . '/promotable_users');
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

	   $headers = array();
	   $headers[] = 'Authorization: Bearer ' . $access_token;
	   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	   $result = curl_exec($ch);

	   if($result['errors'])  {
		   switch($result['errors']) {
			   case 'invalidtoken':
				   $access_token =  refrescatoken_snapchat($appid, $userid, $accestoken);
				   $headers[] = 'Authorization: Bearer ' . $access_token;
				   $result = curl_exec($ch);
			   default:
				   return procesaerrores_snapchat($result['error']);
		   }
	   }

	   curl_close($ch);

	   return $result['data'];
	}


?>