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


?>