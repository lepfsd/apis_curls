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

	   if (curl_errno($ch)) {
			
			return procesaerrores_linkedin (['error' => curl_error($ch)]);
		}


	   curl_close($ch);
	   
	   return $result;

   }

   function ad_analitics_get_linkedin($appid, $access_token, $userid) 
	{
		$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

		$headers = array();
		$headers[] = 'Authorization: Bearer ' . $access_token;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);

		if (curl_errno($ch)) {
			
			return procesaerrores_linkedin (['error' => curl_error($ch)]);
		}

		curl_close($ch);

		return array('data' => $result['elements']);
	}

   function campaign_group_update_linkedin($appid, $access_token, $userid, $adCampaignGroupId, $rowdata)
   {

	   $amount = $rowdata['amount'];
	   $currencyCode = $rowdata['currencyCode'];

	   $campaign = [$amount, $currencyCode,]; 

	   $typeData = array('string', 'string' );

	   if((!isset($rowdata['status'])) && (empty($rowdata['status']))) {
			$validate = validate_type($campaign, $typeData);

			if($validate != "OK") {
				return array('error' => $validate);
			}
	
			$data = array(
				'patch' => array(
					'$set' => array(
						'totalBudget' => array(
							'amount' => $amount,
							'currencyCode' => $currencyCode
						)
					)
				)
			);

	   } else {
			$data = array(
				'patch' => array(
					'$set' => array(
						'status' => $rowdata['status']
					)
				)
			);
	   }
	   $ch = curl_init();

	   curl_setopt($ch, CURLOPT_URL, 'https://api.linkedin.com/v2/adCampaignGroupsV2/' . $adCampaignGroupId);
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt($ch, CURLOPT_POST, 1);
	   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

	   $headers = array();
	   $headers[] = 'Authorization: Bearer ' . $access_token;
	   $headers[] = 'Content-Type: application/json';
	   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	   $result = curl_exec($ch);

	   if (curl_errno($ch)) {
			
			return procesaerrores_linkedin (['error' => curl_error($ch)]);
		}

	   curl_close($ch);
	   
	   return $result;

   }

   function campaign_group_status_linkedin($appid, $access_token, $userid, $adCampaignGroupId, $rowdata)
   {	

	   if(!isset($rowdata['status'])) {
			return array('error' => "Status is required");
	   } 

		$response = campaign_group_update_linkedin($appid, $access_token, $userid, $adCampaignGroupId, $rowdata);

		return $response;

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

	   if (curl_errno($ch)) {
				
			return procesaerrores_linkedin (['error' => curl_error($ch)]);
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

	   if (curl_errno($ch)) {
			
			return procesaerrores_linkedin (['error' => curl_error($ch)]);
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

	   if (curl_errno($ch)) {
			
			return procesaerrores_linkedin (['error' => curl_error($ch)]);
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

	   if (curl_errno($ch)) {
			
			return procesaerrores_linkedin (['error' => curl_error($ch)]);
		}

	   curl_close($ch);
	   
	   return $result;

   }

   function campaign_update_linkedin($appid, $access_token, $userid, $campaignID, $rowdata)
    {
		$amountDailyBudget = $rowdata['amountDailyBudget'];
		$currencyCodeDailyBudget = $rowdata['currencyCodeDailyBudget'];
		$amountTotalBudget = $rowdata['amountTotalBudget'];
		$currencyCodeTotalBudget = $rowdata['currencyCodeTotalBudget'];
		
		if(!isset($rowdata['status'])) { 
			$campaign = array($amountDailyBudget, $currencyCodeDailyBudget, $amountTotalBudget, $currencyCodeTotalBudget);

			$typeData = array('string','string','string','string',);

			$validate = validate_type($campaign, $typeData);

			if($validate != "OK") {
				return array('error' => $validate);
			}

			$data = array(
					'patch' => array(
						'$set' => array(
							'dailyBudget' => array(
								'amount' => $amountDailyBudget,
								'currencyCode' => $currencyCodeDailyBudget
							),
							'totalBudget' => array(
								'amount' => $amountTotalBudget,
								'currencyCode' => $currencyCodeTotalBudget
							)
						)
					)
				);
		} else {
			$data = array(
				'patch' => array(
					'$set' => array(
						'status' => $rowdata['status']
					)
				)
			);
		}

		
	   
	   $ch = curl_init();

	   curl_setopt($ch, CURLOPT_URL, 'https://api.linkedin.com/v2/adCampaignsV2' . $campaignID);
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt($ch, CURLOPT_POST, 1);
	   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

	   $headers = array();
	   $headers[] = 'Authorization: Bearer ' . $access_token;
	   $headers[] = 'Content-Type: application/json';
	   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	   $result = curl_exec($ch);

	   if (curl_errno($ch)) {
			
			return procesaerrores_linkedin (['error' => curl_error($ch)]);
		}

	   curl_close($ch);
	   
	   return $result;

   }

   function campaign_status_linkedin($appid, $access_token, $userid, $campaignID, $rowdata)
   {
		if(!isset($rowdata['status'])) {
			return array('error' => "Status is required");
		} 
	 	$response = campaign_update_linkedin($appid, $access_token, $userid, $campaignID, $rowdata);	
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

	   if (curl_errno($ch)) {
			
			return procesaerrores_linkedin (['error' => curl_error($ch)]);
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

        if (curl_errno($ch)) {
			
			return procesaerrores_linkedin (['error' => curl_error($ch)]);
		}

        curl_close($ch);

        return $result;

	}

	function ad_creative_update_linkedin($appid, $access_token, $userid, $creative_id, $rowdata)
   	{

	   $clickUri = $rowdata['clickUri'];
	   $text = $rowdata['text'];
	   $title = $rowdata['title'];

	   $creative = [$clickUri, $text, $title]; 

	   $typeData = array('string', 'string', 'string', );

	   if((!isset($rowdata['status'])) && (empty($rowdata['status']))) {
			$validate = validate_type($creative, $typeData);

			if($validate != "OK") {
				return array('error' => $validate);
			}
	
			$data = array(
				'patch' => array(
					'$set' => array(
						'variables' => array(
							'clickUri' => $clickUri,
							'data' => array(
								'com.linkedin.ads.TextAdCreativeVariables' => array(
									'text' => $text,
									'title' => $title
								)
							)
						)
					)
				)
			);

	   } else {
			$data = array(
				'patch' => array(
					'$set' => array(
						'status' => $rowdata['status']
					)
				)
			);
	   }
	   
	   $ch = curl_init();

	   curl_setopt($ch, CURLOPT_URL, 'https://api.linkedin.com/v2/adCreativesV2/' . $creative_id);
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   curl_setopt($ch, CURLOPT_POST, 1);
	   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

	   $headers = array();
	   $headers[] = 'Authorization: Bearer ' . $access_token;
	   $headers[] = 'Content-Type: application/json';
	   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	   $result = curl_exec($ch);

	   if (curl_errno($ch)) {
			
			return procesaerrores_linkedin (['error' => curl_error($ch)]);
		}

	   curl_close($ch);
	   
	   return $result;

   }

   function ad_creative_status_linkedin($appid, $access_token, $userid, $creative_id, $rowdata)
   {	

	   if(!isset($rowdata['status'])) {
			return array('error' => "Status is required");
	   } 

		$response = ad_creative_update_linkedin($appid, $access_token, $userid, $creative_id, $rowdata);

		return $response;

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

	   if (curl_errno($ch)) {
			
			return procesaerrores_linkedin (['error' => curl_error($ch)]);
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

	   if (curl_errno($ch)) {
			
			return procesaerrores_linkedin (['error' => curl_error($ch)]);
		}

	   curl_close($ch);
	   
	   return $result;

   }

?>
