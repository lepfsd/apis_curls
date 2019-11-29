<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__.'/configs/facebook.php';
require __DIR__ . '/../../vendor/autoload.php';

use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;	 	 
use FacebookAds\Cursor;
Cursor::setDefaultUseImplicitFetch(false);
use FacebookAds\Object\User;
use FacebookAds\Object\Page;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Campaign;
use FacebookAds\Object\AdSet;
use FacebookAds\Object\Fields\AdSetFields;
use FacebookAds\Object\Values\AdSetBillingEventValues;
use FacebookAds\Object\Values\AdSetOptimizationGoalValues;
use FacebookAds\Object\Fields\TargetingFields;
use FacebookAds\Object\Targeting;
use FacebookAds\Object\AdCreative;


function fb_creacampana($app_id, $app_secret,$access_token,$userid, $add_account_id,$rowdata) {

	$api = Api::init($app_id, $app_secret ,$access_token);
	$api->setLogger(new CurlLogger());
	$fields=array();
	$params = array(
	  'name' =>$userid.'|'.$rowdata['id'].':'. $rowdata['name'].' '.date('Y/m/d h:i:s a', time()) ,
	  'objective' => 'REACH', //ver esto https://developers.facebook.com/docs/reference/ads-api/adcampaign#create
		//enum{APP_INSTALLS, BRAND_AWARENESS, CONVERSIONS, EVENT_RESPONSES, LEAD_GENERATION, LINK_CLICKS, LOCAL_AWARENESS, MESSAGES, OFFER_CLAIMS, PAGE_LIKES, POST_ENGAGEMENT, PRODUCT_CATALOG_SALES, REACH, VIDEO_VIEWS}
	  'status' => 'PAUSED',
	);
	$campananueva=(new AdAccount($add_account_id))->createCampaign($fields,  $params)->exportAllData();
		
	return $campananueva;	
}
//https://developers.facebook.com/docs/marketing-api/campaign-structure
function fb_readcampana($app_id, $app_secret,$access_token,$userid, $add_account_id,$rowdata) {
	$api = Api::init($app_id, $app_secret ,$access_token);
	$api->setLogger(new CurlLogger());
	$fields = array(
		'name',
		'objective',
	);
	$params = array(
		'effective_status' => array('ACTIVE','PAUSED'),
	);
	$campana = (new AdAccount($add_account_id))->getCampaigns($fields, $params)->getResponse()->getContent();
		
	return $campana;	
}

function fb_creaadset($app_id, $app_secret,$access_token,$userid, $add_account_id,$rowdata) {

	$api = Api::init($app_id, $app_secret ,$access_token);
	$api->setLogger(new CurlLogger());
	$fields=array();
	$params = array(
		'name' => $rowdata['name'],
		'lifetime_budget' => '20000',
		'start_time' => $rowdata['start'],
		'end_time' => $rowdata['end'],
		'campaign_id' => '<adCampaignLinkClicksID>',
		'bid_amount' => '500',
		'billing_event' => 'IMPRESSIONS',
		'optimization_goal' => 'POST_ENGAGEMENT',
		'targeting' => array('age_min' => 20,'age_max' => 24,'behaviors' => array(array('id' => 6002714895372,'name' => 'All travelers')),'genders' => array(1),'geo_locations' => array('countries' => array('US'),'regions' => array(array('key' => '4081')),'cities' => array(array('key' => '777934','radius' => 10,'distance_unit' => 'mile'))),'interests' => array(array('id' => '<adsInterestID>','name' => '<adsInterestName>')),'life_events' => array(array('id' => 6002714398172,'name' => 'Newlywed (1 year)')),'publisher_platforms' => array('facebook','audience_network')),
		'status' => 'PAUSED',
	  );
	
	$adset=(new AdAccount($add_account_id))->createAdSet($fields, $params)->exportAllData();
		
	return $adset;	
}

function fb_readadset($app_id, $app_secret,$access_token,$userid, $add_account_id, $ad_set_id) {
	$api = Api::init($app_id, $app_secret ,$access_token);
	$api->setLogger(new CurlLogger());
	$params = array();	

	$adset = new AdSet(ad_set_id);
	$adset->read(array(
		AdSetFields::NAME,
		AdSetFields::CONFIGURED_STATUS,
		AdSetFields::EFFECTIVE_STATUS,
	), $params);

	return $adset;
}

function fb_createad($app_id, $app_secret,$access_token,$userid, $add_account_id,$rowdata, $adset_id) {

	$api = Api::init($app_id, $app_secret ,$access_token);
	$api->setLogger(new CurlLogger());
	$fields=array();
	$params = array(
		'name' => $rowdata['name'],
		'adset_id' => $adset_id,
		'creative' => array($rowdata['creativities_ids']),
		'status' => 'PAUSED',
	);
	
	$ad=(new AdAccount($add_account_id))->createAd($fields, $params)->exportAllData();
		
	return $ad;	
}

function fb_creacreative($app_id, $app_secret,$access_token,$userid, $add_account_id,$rowdata) {

	$api = Api::init($app_id, $app_secret ,$access_token);
	$api->setLogger(new CurlLogger());
	$fields = array();
	
	switch ($rowdata['type']) {
		case "img":
			$creative->setData(array(
				AdCreativeFields::OBJECT_STORY_SPEC => array(
					'page_id' => $rowdata['property_id'], 
					'photo_data' => array('url' => $rowdata['banner'], 'caption' =>  $rowdata['content'])
				)
			));
			$creative->create();	
			break;
		case "post":
			$creative->setData(array(
				AdCreativeFields::OBJECT_STORY_SPEC => array(
					'object_story_id' => array('page_id' => $rowdata['property_id'], 'post_id' =>  $rowdata['post_id']), 
				)
			));
			$creative->create();
			break;
		case "externallink":
			$link_data = new AdCreativeLinkData();
			$link_data->setData(array(
				AdCreativeLinkDataFields::MESSAGE => $rowdata['title'],
				AdCreativeLinkDataFields::LINK => $rowdata['linkdescription'],
				AdCreativeLinkDataFields::IMAGE_HASH => $rowdata['image_hash'],
			));

			$object_story_spec = new AdCreativeObjectStorySpec();
			$object_story_spec->setData(array(
				AdCreativeObjectStorySpecFields::PAGE_ID => array('page_id' => $row_data['property_id']),
				AdCreativeObjectStorySpecFields::LINK_DATA => $link_data,
			));

			$creative = new AdCreative(null, $add_account_id);

			$creative->setData(array(
				AdCreativeFields::NAME => $rowdata['name'],
				AdCreativeFields::OBJECT_STORY_SPEC => $object_story_spec,
			));

			$creative->create();
			break;
		case 'carrouselad':
			$product_array = array();
			foreach($rowdata['products'] as $product) {
				$product1 = (new AdCreativeLinkDataChildAttachment())->setData(array(
					AdCreativeLinkDataChildAttachmentFields::LINK => $product['link'],
					AdCreativeLinkDataChildAttachmentFields::NAME => $product['name'],
					AdCreativeLinkDataChildAttachmentFields::DESCRIPTION => $product['description'],
					AdCreativeLinkDataChildAttachmentFields::IMAGE_HASH => $product['image_hash'],
					AdCreativeLinkDataChildAttachmentFields::VIDEO_ID => $product['video_id'],
				  ));

				  $product_array[] = $product1;
			} 

			$link_data = new AdCreativeLinkData();
			$link_data->setData(array(
				AdCreativeLinkDataFields::LINK => $rowdata['link'],
				AdCreativeLinkDataFields::CHILD_ATTACHMENTS => array(
					$product_array,
				),
			));

			$object_story_spec = new AdCreativeObjectStorySpec();
			$object_story_spec->setData(array(
				AdCreativeObjectStorySpecFields::PAGE_ID => $rowdata['page_id'],
				AdCreativeObjectStorySpecFields::LINK_DATA => $link_data,
			));

			$creative = new AdCreative(null, $add_account_id);
			$creative->setData(array(
				AdCreativeFields::NAME => $rowdata['name'],
				AdCreativeFields::OBJECT_STORY_SPEC => $object_story_spec,
			));

			$creative->create();
			break;
		case 'videopage':
			$video_data = new AdCreativeVideoData();
			$video_data->setData(array(
				AdCreativeVideoDataFields::IMAGE_URL => $rowdata['image_url'],
				AdCreativeVideoDataFields::VIDEO_ID => $rowdata['video_id'],
				AdCreativeVideoDataFields::CALL_TO_ACTION => array(
					'type' => AdCreativeCallToActionTypeValues::LIKE_PAGE,
					'value' => array(
						'page' => $rowdata['page_id'],
					),
				),
			));

			$object_story_spec = new AdCreativeObjectStorySpec();
			$object_story_spec->setData(array(
				AdCreativeObjectStorySpecFields::PAGE_ID => $rowdata['page_id'],
				AdCreativeObjectStorySpecFields::VIDEO_DATA => $video_data,
			));

			$creative = new AdCreative(null, $add_account_id);

			$creative->setData(array(
				AdCreativeFields::NAME => 'Sample Creative',
				AdCreativeFields::OBJECT_STORY_SPEC => $object_story_spec,
			));

			$creative->create();
			break;
		case 'promotedpost':
			$creative->setData(array(
				AdCreativeFields::NAME => 'Sample Promoted Post',
				AdCreativeFields::OBJECT_STORY_ID => $rowdata['post_id'],
			  ));
			  $creative->create();
		default:
			
	}
		
	return $creative;	
}

function fb_readcrative($app_id, $app_secret,$access_token,$userid, $add_account_id,$ad_creative_id) {
	$api = Api::init($app_id, $app_secret ,$access_token);
	$api->setLogger(new CurlLogger());
	$fields = array(
		'name',
  		'object_story_id',
	);
	$params = array(
	);

	$creative = (new AdCreative($ad_creative_id))->getSelf($fields, $params)->exportAllData();
		
	return $creative;	
}

// https://developers.facebook.com/docs/marketing-api/reference/ad-account/adspixels/#ejemplo-2
function fb_crearadspixels($app_id, $app_secret,$access_token,$userid, $add_account_id,$rowdata) {

	/* make the API call */
	try {
		// Returns a `Facebook\FacebookResponse` object
		$response = $fb->post(
			'/act_' . $add_account_id . '/adspixels',
			array (
				'name' => $rowdata['name'],
			),
			$access_token
		);
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
	}
	$graphNode = $response->getGraphNode();

	return $graphNode;
	/* handle the result */
}

function fb_readadspixels($app_id, $app_secret,$access_token,$userid, $add_account_id,$pixel_id) {

	/* make the API call */
	try {
		// Returns a `Facebook\FacebookResponse` object
		$response = $fb->get(
			'/' . $pixel_id .'/',
			$access_token
		);
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
	$graphNode = $response->getGraphNode();
	/* handle the result */
	return $graphNode;
}

//https://developers.facebook.com/docs/marketing-api/audiences-api

function fb_crearaudience($app_id, $app_secret,$access_token,$userid, $add_account_id,$rowdata) {

	$api = Api::init($app_id, $app_secret, $access_token);
	$api->setLogger(new CurlLogger());

	$fields = array(
	);
	$params = array(
		'name' => $rowdata['name'],
		'subtype' => 'CUSTOM',
		'description' => $rowdata['description'],
		'customer_file_source' => 'USER_PROVIDED_ONLY',
	);

	$audience = (new AdAccount($add_account_id))->createCustomAudience($fields,  $params)->exportAllData();
		
	return $audience;	
}

function fb_readaudience($app_id, $app_secret,$access_token,$userid, $add_account_id,$rowdata) {

	// no lo veo en la doc
}

/* 
Campana tipo "post automatico": como adespresso pero con excluyentes, cruces, 
https://developers.facebook.com/docs/graph-api/reference/user/posts/
*/
function fb_readpost($app_id, $app_secret,$access_token,$userid, $add_account_id,$user_id) {

	/* make the API call */
	try {
		// Returns a `Facebook\FacebookResponse` object
		$response = $fb->get(
			'/' . $user_id . '/posts',
			$access_token
		);
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
	$graphNode = $response->getGraphNode();
	/* handle the result */

	return $graphNode;
}

//https://developers.facebook.com/docs/graph-api/reference/rtb-dynamic-post/#parameters

function fb_readrtb_dynamic_post($app_id, $app_secret,$access_token,$userid, $add_account_id,$rtb_dynamic_post_id) {

	/* make the API call */
	try {
		// Returns a `Facebook\FacebookResponse` object
		$response = $fb->get(
			'/' . $rtb_dynamic_post_id,
			$access_token
		);
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
	$graphNode = $response->getGraphNode();
	/* handle the result */

	return $graphNode;
}

// https://developers.facebook.com/docs/marketing-api/dynamic-product-ads/ads-management#categories

function fb_read_ads_categories($app_id, $app_secret,$access_token,$userid, $add_account_id,$api_version, $product_catalog_id) {
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/' . $api_version . '/' . $product_catalog_id . '/categories');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

	$headers = array();
	$headers[] = 'Content-Type: application/x-www-form-urlencoded';
	$headers[] = 'Authorization: Bearer ' . $access_token;

	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	$result = curl_exec($ch);
	if (curl_errno($ch)) {
		echo 'Error:' . curl_error($ch);
	}
	curl_close($ch);

	$response = json_decode($result);
}

// catalogo https://developers.facebook.com/docs/marketing-api/reference/product-catalog

function fb_crearproduct_catalog($app_id, $app_secret,$access_token,$userid, $add_account_id,$business_id, $rowdata) {
	$product_catalog = new ProductCatalog(null, $business_id);

	$product_catalog->setData(array(
		ProductCatalogFields::NAME => $rowdata['name'],
	));

	$response = $product_catalog->create();

	return $response;
}

function fb_readproduct_catalog($app_id, $app_secret,$access_token,$userid, $add_account_id,$rtb_dynamic_post_id, $product_catalog_id) {

	/* make the API call */
	try {
		// Returns a `Facebook\FacebookResponse` object
		$response = $fb->get(
		'/' . $product_catalog_id,
		$access_token
		);
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
	$graphNode = $response->getGraphNode();
	/* handle the result */
	return $graphNode;
}

