<?php
   require_once "./Helper.php";
   require_once "./tokens.php";

   function campaign_group_crear_linkedin($appid, $access_token, $userid, $rowdata)
   {

	   $campaign = array(
		   'account' => $rowdata['account'],
		   'name' => $rowdata['name'],
		   'runSchedule' => array(
			   'end' => $rowdata['end'],
			   'start' => $rowdata['start']
		   ),
		   'status' => $rowdata['status'],
		   'totalBudget' => array(
				'amount' => $rowdata['amount'],
				'currencyCode' => $rowdata['currencyCode']
			),
	   );

	   $typeData = array('string', 'string', 'array', 'string', 'array');

	   $validate = validate_type($campaign, $typeData);

	   if($validate != "OK") {
		   return array('error' => $validate);
	   }
	   
	   $ch = curl_init();

	   curl_setopt($ch, CURLOPT_URL, 'https://api.linkedin.com/v2/adCampaignGroupsV2');
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt($ch, CURLOPT_POST, 1);
	   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($campaign));

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
	   
	   return $result;

   }

   function target_audience_get_linkedin($appid, $access_token, $userid, $rowdata) 
   {

	   $ch = curl_init();

	   curl_setopt($ch, CURLOPT_URL, 'https://api.linkedin.com/v2/adTargetingFacets');
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

	   return $result;
	}

	function budget_pricing_get_linkedin($appid, $access_token, $userid, $rowdata) 
    {

	   $ch = curl_init();

	   curl_setopt($ch, CURLOPT_URL, 'https://api.linkedin.com/v2/adBudgetPricing?account=urn:li:sponsoredAccount:502616245&bidType=CPM&campaignType=TEXT_AD&matchType=EXACT&q=criteria&target.includedTargetingFacets.locations[0]=urn:li:country:ca&target.includedTargetingFacets.locations[1]=urn:li:country:us&target.excludingTargetingFacets.seniorities[0]=urn:li:seniority:3&dailyBudget.amount=100&dailyBudget.currencyCode=USD');
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

	   return $result;
	}

	function form_responses_get_linkedin($appid, $access_token, $userid, $rowdata) 
    {

	   $ch = curl_init();

	   curl_setopt($ch, CURLOPT_URL, 'https://api.linkedin.com/v2/adFormResponses?q=account&account={sponsoredAccountUrn}');
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

	   return $result;
	}

	function campaign_crear_linkedin($appid, $access_token, $userid, $rowdata)
    {

	   $campaign = array(
		   'account' => $rowdata['account'],
		   'audienceExpansionEnabled' => $rowdata['audienceExpansionEnabled'],
		   'costType' => $rowdata['costType'],
		   'creativeSelection' => $rowdata['creativeSelection'],
		   'dailyBudget' => array(
			   'amount' => $rowdata['amount'],
			   'currencyCode' => $rowdata['currencyCode']
		   ),
		   'locale' => array(
				'country' => $rowdata['country'],
				'language' => $rowdata['language']
			),
		   'name' => $rowdata['name'],
		   'offsiteDeliveryEnabled' => $rowdata['offsiteDeliveryEnabled'],
		   'runSchedule' => array(
				'start' => $rowdata['start'],
				'end' => $rowdata['end']
			),
			'type' => $rowdata['type'],
			'unitCost' => array(
				'amount' => $rowdata['amount'],
				'currencyCode' => $rowdata['currencyCode']
			),
	   );

	   $typeData = array('string', 'boolean', 'string', 'string', 'array', 'array', 'string', 'boolean', 'array',
			'string', 'array');

	   $validate = validate_type($campaign, $typeData);

	   if($validate != "OK") {
		   return array('error' => $validate);
	   }
	   
	   $ch = curl_init();

	   curl_setopt($ch, CURLOPT_URL, 'https://api.linkedin.com/v2/adCampaignsV2');
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt($ch, CURLOPT_POST, 1);
	   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($campaign));

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
	   
	   return $result;

   }


   function ad_creative_crear_linkedin($appid, $access_token, $userid, $rowdata)
   {

	   $creative = array(
		   'campaign' => $rowdata['campaign'],
		   'status' => $rowdata['status'],
		   'type' => $rowdata['type'],
		   'variables' => array(
			   'clickUri' => $rowdata['clickUri'],
			   'data' =>array(
					'text' => $rowdata['text'],
					'title' => $rowdata['title']
				),
		   ),
		   
	   );

	   $typeData = array('string', 'string', 'string', 'array');

	   $validate = validate_type($creative, $typeData);

	   if($validate != "OK") {
		   return array('error' => $validate);
	   }
	   
	   $ch = curl_init();

	   curl_setopt($ch, CURLOPT_URL, 'https://api.linkedin.com/v2/adCreativesV2');
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt($ch, CURLOPT_POST, 1);
	   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($creative));

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
	   
	   return $result;

   }

   function video_ads_crear_linkedin($appid, $access_token, $userid, $rowdata)
   {

	   $video = array(
		   'account' => $rowdata['account'],
		   'contentReference' => $rowdata['contentReference'],
		   'name' => $rowdata['name'],
		   'owner' => $rowdata['owner'],
		   'type' => $rowdata['type'],
	   );

	   $typeData = array('string', 'string', 'string', 'array');

	   $validate = validate_type($video, $typeData);

	   if($validate != "OK") {
		   return array('error' => $validate);
	   }
	   
	   $ch = curl_init();

	   curl_setopt($ch, CURLOPT_URL, 'https://api.linkedin.com/v2/adDirectSponsoredContents');
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt($ch, CURLOPT_POST, 1);
	   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($video));

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
	   
	   return $result;

   }

   function sponsored_inmail_crear_linkedin($appid, $access_token, $userid, $rowdata)
   {

	   $video = array(
		   'account' => $rowdata['account'],
		   'contentReference' => $rowdata['contentReference'],
		   'name' => $rowdata['name'],
		   'owner' => $rowdata['owner'],
		   'type' => $rowdata['type'],
	   );

	   $typeData = array('string', 'string', 'string', 'array');

	   $validate = validate_type($video, $typeData);

	   if($validate != "OK") {
		   return array('error' => $validate);
	   }
	   
	   $ch = curl_init();

	   curl_setopt($ch, CURLOPT_URL, 'https://api.linkedin.com/v2/adInMailContentsV2');
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt($ch, CURLOPT_POST, 1);
	   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($video));

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
	   
	   return $result;

   }

   function creative_delete_linkedin($appid, $access_token, $userid, $rowdata, $creative_id)
    {   
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.linkedin.com/v2/adCreativesV2/' . $creative_id);
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

        return $result;

	}


?>