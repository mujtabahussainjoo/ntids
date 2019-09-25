<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\CommonController;
use StdClass;
class CustomerController
{
    public function __construct(){
    	
    }
	

	/*
	 * Customer Billing Information
	 * 
	 * request param  string  billing_flag
	 * request param  string  customer_id
	 * request param  string  oauth_token
	 * 
	 * return json response
	 * */
	public function getBillingInformation(CommonController $common){
		$billinginfo = array();		
		//$frgtpwdata['email'] = $_REQUEST['customer_id'];
		$billingFlg = (isset($_REQUEST['billing_flag']))? trim($_REQUEST['billing_flag']): '1';
		$accessToken = $_REQUEST['oauth_token'];
		
		$data = $common->getCurl($billinginfo, $common->api_url().'customers/me/','GET',$accessToken);
		if($data){
			$billingData = array();
			$billingData['customer'] = array('customer_id'=>$data->id, 'email'=>$data->email);
			$billingData['address'] = new StdClass;
			if(count($data->addresses) > 0){
				foreach($data->addresses as $key=>$val)
				{
					if(($billingFlg == 1 && isset($val->default_billing) && $val->default_billing == true) || ($billingFlg == 0 && isset($val->default_shipping) && $val->default_shipping == true)) 
					{
						/*$billingData['address']['address_id'] = $val->id;
						$billingData['address']['f_name'] = $val->firstname;
						$billingData['address']['l_name'] = $val->lastname;
						$billingData['address']['company'] = (isset($val->company))?$val->company : '';
						$billingData['address']['address'] = implode(',', $val->street);
						$billingData['address']['city'] = $val->city;
						$billingData['address']['state'] = $val->region->region_code;
						$billingData['address']['postcode'] = $val->postcode;
						$billingData['address']['country_id'] = $val->country_id;
						$billingData['address']['telephone'] = $val->telephone;*/
						$billingData['address']->address_id = $val->id;
						$billingData['address']->f_name = $val->firstname;
						$billingData['address']->l_name = $val->lastname;
						$billingData['address']->company = (isset($val->company))?$val->company : '';
						$billingData['address']->address = implode(',', $val->street);
						$billingData['address']->city = $val->city;
						$billingData['address']->state = $val->region->region_code;
						$billingData['address']->postcode = $val->postcode;
						$billingData['address']->country_id = $val->country_id;
						$billingData['address']->telephone = $val->telephone;
					}
				}
			}
			
			$json = '{ "success" : "1", "data" : ['.json_encode($billingData).'], "message" : ""}';
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : "Invalid data."}';
		}
		return $this->SendResponse ( $json );
	}
	
	public function updateBillingInformation(CommonController $common)
	{
		$first_name = (isset($_REQUEST['f_name']) && trim($_REQUEST['f_name']) != '')? $_REQUEST['f_name'] : '';
		$last_name = (isset($_REQUEST['l_name']) && trim($_REQUEST['l_name']) != '')? $_REQUEST['l_name'] : '';
		$customer_id = (isset($_REQUEST['customer_id']) && trim($_REQUEST['customer_id']) != '')? $_REQUEST['customer_id'] : '';
		$street1 = (isset($_REQUEST['address']) && trim($_REQUEST['address']) != '')? $_REQUEST['address'] : '';
		$company = (isset($_REQUEST['company']) && trim($_REQUEST['company']) != '')? $_REQUEST['company'] : '';
		$city = (isset($_REQUEST['city']) && trim($_REQUEST['city']) != '')? $_REQUEST['city'] : '';
		$postcode = (isset($_REQUEST['postcode']) && trim($_REQUEST['postcode']) != '')? $_REQUEST['postcode'] : '';
		$region = (isset($_REQUEST['state']) && trim($_REQUEST['state']) != '')? $_REQUEST['state'] : '';
		$country_id = (isset($_REQUEST['country_id']) && trim($_REQUEST['country_id']) != '')? $_REQUEST['country_id'] : '';
		$telephone = (isset($_REQUEST['phone']) && trim($_REQUEST['phone']) != '')? $_REQUEST['phone'] : '';
		$billing_flag = (isset($_REQUEST['billing_flag']) && trim($_REQUEST['billing_flag']) != '')? $_REQUEST['billing_flag'] : '1';
		$accessToken = (isset($_REQUEST['oauth_token']) && trim($_REQUEST['oauth_token']) != '')? $_REQUEST['oauth_token'] : '';
		$userData = array();
		$b_address = $common->getCurl($userData, $common->api_url().'customers/me/billingAddress','GET',$accessToken);
		$custDetails = $common->getCurl($userData, $common->api_url().'customers/me','GET',$accessToken);
		
		
		//print_r($b_address); exit;
		
		
		if(empty($b_address)){			
			
			$postArr1['customer']['id'] = $custDetails->id;
			$postArr1['customer']['email'] = $custDetails->email;
			$postArr1['customer']['firstname'] = $first_name;
			$postArr1['customer']['lastname'] = $last_name;
			$postArr1['customer']['website_id'] = $custDetails->website_id;
			$postArr1['customer']['addresses'][] = array("firstname"=> $first_name,"lastname"=> $last_name,"company" => $company,"street"=> array($street1),"city"=> $city,"region" => $region,"postcode" => $postcode,"country_id"=> $country_id,"telephone"=>$telephone,"default_billing"=> true);
			//print_r($postArr1); exit;
			$data = $common->getCurl($postArr1, $common->api_url().'customers/me','PUT',$accessToken);
			//print_r($data); exit;
			$b_address = $common->getCurl($userData, $common->api_url().'customers/me/billingAddress','GET',$accessToken);
			//print_r($b_address); exit;
			/*$_custom_address = array (
				'firstname' => $first_name,
				'lastname' => $last_name,
				'company' => $company,
				'street' => array (
					$street1 
				),
				'city' => $city,
				'regionId' => (isset($b_address->region_id) && $b_address->region_id != '') ? $b_address->region_id : 0,
				'regionCode' => $country_id,
				'region' => $region,
				'postcode' => $postcode,
				'countryId' => $country_id,
				'telephone' => $telephone,
				'customerId' => $customer_id,
				'customerAddressId' => (isset($b_address->id) && $b_address->id != '') ? $b_address->id : 0,,
				"saveInAddressBook" => 1,
			);*/
			
		} else {
			$_custom_address = array (
				'firstname' => $first_name,
				'lastname' => $last_name,
				'company' => $company,
				'street' => array (
					$street1 
				),
				'city' => $city,
				'regionId' => (isset($b_address->region_id) && $b_address->region_id != '') ? $b_address->region_id : 0,
				'regionCode' => $country_id,
				'region' => $region,
				'postcode' => $postcode,
				'countryId' => $country_id,
				'telephone' => $telephone,
				'customerId' => $customer_id,
				'customerAddressId' => (isset($b_address->id) && $b_address->id != '') ? $b_address->id : 0,
				"saveInAddressBook" => 1,
			);
			$postArr = array();
			$postArr['address'] = $_custom_address;
			$postArr['useForShipping'] = true;
			//$postArr['default_billing'] = 1;
			//print_r($postArr); exit;
			
			//$data = $common->getCurl($postArr, $common->api_url().'carts/mine/billing-address/','POST',$accessToken);
			$postArr1['customer'] = array('email' =>$custDetails->email ,'firstname' => $first_name,'lastname' => $last_name,'website_id' => $custDetails->website_id);
			$postArr1['customer']['addresses'][] = array('id' => $b_address->id,"firstname"=> $first_name,"lastname"=> $last_name,"company" => $company,"street"=> array($street1),"city"=> $city,"region_id"=> $b_address->region_id,"region" => $region,"postcode" => $postcode,"country_id"=> $country_id,"telephone"=>$telephone);
			//print_r($postArr1); exit;
			$data = $common->getCurl($postArr1, $common->api_url().'customers/me','PUT',$accessToken);
		}
		
		
		//print_r($data); exit;
		if($data){
			$json = '{ "success" : "1", "data" : [], "message" : "Address updated."}';
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : "Invalid data."}';
		}
		
		return $this->SendResponse ( $json );
	}
	
	
	public function getHomeContent(CommonController $common)
	{
		//$accessToken = (isset($_REQUEST['oauth_token']) && trim($_REQUEST['oauth_token']) != '')? $_REQUEST['oauth_token'] : '';
		$accessToken = $common->admin_token();
		$postArr = array();
		// admin token = 88r74rqa1eowc1ar6kxxu1xbmu9sjggd
		$data = $common->getCurl($postArr, $common->api_url().'cmsPage/58','GET',$accessToken);
		
		if($data){
			$content['content'] = $data->content;
			
			$json = '{ "success" : "1", "data" : ['.json_encode($content).'], "message" : ""}';
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : "Invalid page content."}';
		}
		
		return $this->SendResponse ( $json );
	}
	
	public function getGeneralInformation(CommonController $common)
	{
		$accessToken = (isset($_REQUEST['oauth_token']) && trim($_REQUEST['oauth_token']) != '')? $_REQUEST['oauth_token'] : '';
		$accessTokenAdmin = $common->admin_token();
		$postArr = array();
		// admin token = 88r74rqa1eowc1ar6kxxu1xbmu9sjggd
		$pageContent = $common->getCurl($postArr, $common->api_url().'cmsPage/search?searchCriteria','GET',$accessTokenAdmin);
		
		//$country = $common->getCurl($postArr, $common->api_url().'directory/countries?searchCriteria','GET',$accessTokenAdmin);
		$country = $common->getCurl($postArr, $common->api_url().'directory/countries/AU','GET',$accessTokenAdmin);
		
		/*if(count($country) > 0)
		{
			foreach($country as $cval)
			{
				$a = array();
				$a['country_id'] = $cval->id;
				$a['country_code'] = $cval->two_letter_abbreviation;
				$a['country_name'] = $cval->full_name_english;
				$dataArr['country'][] = $a;
			}
		}*/
		
		if($pageContent){
			$content = array();
			$content['USER'] = array();
			$dataArr = array();
			$dataArr['registration_id'] = 'Lorem Ipsum';
			$dataArr['thank_you'] = '';
			$dataArr['terms_and_condition'] = '';
			$dataArr['user_guide'] = 'Lorem Ipsum';
			$dataArr['home_page'] = '';
			$dataArr['country'] = array();
			$dataArr['empty_cart_message'] = '<p>Shopping Cart is empty.</p>';
			
			foreach($pageContent->items as $val)
			{
				if($val->id == 4){
					$dataArr['terms_and_condition'] = $val->content;
				}
				
				if($val->id == 3){
					$dataArr['thank_you'] = 'Thank you for shopping with XXX\r\n\r\nConfirmation has been sent to your email address\r\n\r\n';
				}
				
				if($val->id == 2){
					$dataArr['home_page'] = $val->content;
				}
			}
			
			if($country){
				$a = array();
				$a['country_id'] = $country->id;
				$a['country_code'] = $country->two_letter_abbreviation;
				$a['country_name'] = $country->full_name_english;
				$dataArr['country'][] = $a;
				unset($a);
			}
			
			$content['USER'] = $dataArr;
			unset($dataArr);
			$json = '{ "success" : "1", "data" : ['.json_encode($content).'], "message" : ""}';
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : "Invalid page content."}';
		}
		
		return $this->SendResponse ( $json );
	}
	
	
	
	public function applyPromoCode(CommonController $common)
	{
		$postArr = array();
		$accessToken = (isset($_REQUEST['oauth_token']) && trim($_REQUEST['oauth_token']) != '')? $_REQUEST['oauth_token'] : '';
		//$accessToken = (isset($_REQUEST['customer_id']) && trim($_REQUEST['customer_id']) != '')? $_REQUEST['customer_id'] : '';
		$promoCode = (isset($_REQUEST['coupon_code']) && trim($_REQUEST['coupon_code']) != '')? $_REQUEST['coupon_code'] : '';
		//$accessToken = $common->admin_token();
		 
		$data = $common->getCurl($postArr, $common->api_url().'carts/mine/coupons/'.$promoCode.'/','PUT',$accessToken);
		$dataTotal = $common->getCurl($postArr, $common->api_url().'carts/mine/totals','GET',$accessToken);
		$respData = array();
		if($data === true){
			$respData['sub_total'] = (isset($dataTotal->subtotal))? $dataTotal->subtotal : '';
			$respData['discounted_sub_total'] = (isset($dataTotal->subtotal_with_discount))? $dataTotal->subtotal_with_discount : '';
			$respData['grand_total'] = (isset($dataTotal->grand_total))? $dataTotal->grand_total : '';
			$json = '{ "success" : "1", "data" : ['.json_encode($respData).'], "message" : "coupon code successfully applied to the cart"}';
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : "Invalid page content."}';
		}
		
		return $this->SendResponse ( $json );
	}
	
	
	public function getStateList(CommonController $common) {
		
		$accessTokenAdmin = $common->admin_token();
		$postArr = array();
		$country = $common->getCurl($postArr, $common->api_url().'directory/countries/AU','GET',$accessTokenAdmin);
		//print_r($country); exit;
		if($country) {
			$state = array();
			if(count($country->available_regions) > 0){
				foreach($country->available_regions as $val)
				{	
					$tmp = array();
					$tmp['region_id'] = $val->id;
					$tmp['code'] = $val->code;
					$tmp['name'] = $val->name;
					$state[] = $tmp;
				}
			}
			
			$json = '{ "success" : "1", "data" : '.json_encode($state).', "message" : "List of states."}';
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : "No states available."}';
		}
		
		return $this->SendResponse ( $json );
	}
	
	
	public function getCartCounter(CommonController $common) {
		
		$accessToken = (isset($_REQUEST['oauth_token']) && trim($_REQUEST['oauth_token']) != '')? $_REQUEST['oauth_token'] : '';
		$postArr = $data = array();
		//$data = $common->getCurl($postArr, $common->api_url().'carts/mine/totals','GET',$accessToken);
		$data = $common->getCurl($postArr, $common->api_url().'carts/mine/items','GET',$accessToken);
		//print_r($data); exit;
		$cart_count = array();
		$cart_count['cart_count'] = 0;
		if(!empty($data) ){
			//if(isset($data->items) && count($data->items) > 0)
			//{
			if(isset($data->message) && $data->message == 'Current customer does not have an active cart.'){
				$json = '{ "success" : "1", "data" : '.json_encode($cart_count).', "message" : "", "oauth_token" : "'.$accessToken.'"}';
			} else {
					
				$totalqty = 0;
				foreach($data as $pqty){
					if(isset($pqty->qty)){
						$totalqty += $pqty->qty;
					}
				}
				if(count($data) > 0){
					//$cart_count['cart_count'] = count($data);
					$cart_count['cart_count'] = $totalqty;
					$json = '{ "success" : "1", "data" : '.json_encode($cart_count).', "message" : "", "oauth_token" : "'.$accessToken.'"}';
				} else {
					$json = '{ "success" : "1", "data" : '.json_encode($cart_count).', "message" : "", "oauth_token" : "'.$accessToken.'"}';
				}
				//}
			}
		} else {
			//$json = '{ "success" : "1", "data" : [], "message" : "Cart is empty.", "oauth_token" : "'.$accessToken.'"}';
			$json = '{ "success" : "1", "data" : '.json_encode($cart_count).', "message" : "", "oauth_token" : "'.$accessToken.'"}';
		}
		return $this->SendResponse ( $json );
	}
	
	/*
	 * return response
	 * 
	 * request param string  $body
	 * 
	 * return json response
	 * */
    public function SendResponse($body = '', $content_type = 'text/html') {
        // header ( 'HTTP/1.1' );
        // header ( 'Content-type: ' . $content_type );
        // echo $body;
        // exit ();
        return new Response(
            $body
        );
    }
}
