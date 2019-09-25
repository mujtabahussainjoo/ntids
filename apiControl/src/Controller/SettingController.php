<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\CommonController;

class SettingController
{
    public function __construct(){
    	
    }
	
	
	/*
	 * Customer forgot password function
	 * 
	 * request param  string  $email
	 * 
	 * return json response
	 * */
	public function getUserSetting(CommonController $common){
		
		$customer_id = (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '') ? $_REQUEST['customer_id'] : '';
		$accessToken = (isset($_REQUEST['oauth_token']) && $_REQUEST['oauth_token'] != '') ? $_REQUEST['oauth_token'] : '';
		$userData = array();
		$adminAccessToken = $common->admin_token();
		if(trim($customer_id) != '' && $customer_id > 0 && trim($accessToken) != ''){
			$custArr = array();
			$custArr['first_name'] = '';
			$custArr['last_name'] = '';
			$customer = $common->getCurl($userData, $common->api_url().'customers/me','GET',$accessToken);
			if($customer)
			{
				$custArr['first_name'] = $customer->firstname;
				$custArr['last_name'] = $customer->lastname;
			}
			$settings = $common->getCurl($userData, $common->api_url().'wishlist/getusersetting/'.$customer_id,'GET',$accessToken);
			if($settings){
				if($settings[0]->phone_number == '-'){
					$settings[0]->phone_number = '';
				}
				$settings[0]->first_name = $custArr['first_name'];
				$settings[0]->last_name = $custArr['last_name'];
				$json = '{ "success" : "1", "data" : ['.json_encode($settings[0]).'], "message" : ""}';
			} else {
				$settings['customer_id'] = $customer_id;
				$settings['proximity_notification'] = '0';
				$settings['push_notification'] = '0';
				$settings['geo_location'] = '0';
				$settings['phone_number'] = '0';
				$settings['first_name'] = $custArr['first_name'];
				$settings['last_name'] = $custArr['last_name'];
				$json = '{ "success" : "1", "data" : ['.json_encode($settings).'], "message" : ""}';
			}
			
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : "Required fields are missing."}';
		}
		
		return $this->SendResponse( $json );
	}
	
	
	public function saveUserSetting(CommonController $common){
		$customer_id = (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '') ? $_REQUEST['customer_id'] : '';
		$accessToken = (isset($_REQUEST['oauth_token']) && $_REQUEST['oauth_token'] != '') ? $_REQUEST['oauth_token'] : '';
		$proximity_notification = (isset($_REQUEST['proximity_notification']) && $_REQUEST['proximity_notification'] != '') ? $_REQUEST['proximity_notification'] : '0';
		$push_notification = (isset($_REQUEST['push_notification']) && $_REQUEST['push_notification'] != '') ? $_REQUEST['push_notification'] : '0';
		$phone_number = (isset($_REQUEST['phone_number']) && $_REQUEST['phone_number'] != '') ? $_REQUEST['phone_number'] : '-';
		$geo_location = (isset($_REQUEST['geo_location']) && $_REQUEST['geo_location'] != '') ? $_REQUEST['geo_location'] : '0';
		$f_name = (isset($_REQUEST['f_name']) && $_REQUEST['f_name'] != '') ? $_REQUEST['f_name'] : '';
		$l_name = (isset($_REQUEST['l_name']) && $_REQUEST['l_name'] != '') ? $_REQUEST['l_name'] : '';
		
		$userData = array();
		$adminAccessToken = $common->admin_token();
		if(trim($customer_id) != '' && $customer_id > 0 && trim($accessToken) != ''){
			
			$customer = $common->getCurl($userData, $common->api_url().'customers/me','GET',$accessToken);
			
			$postArr1 = array();
			$postArr1['customer'] = array('email' =>$customer->email ,'firstname' => $f_name,'lastname' => $l_name,'website_id' => $customer->website_id);
			
			$customer = $common->getCurl($postArr1, $common->api_url().'customers/me','PUT',$accessToken);
			//print_r($customer); exit;
			$setting = $common->getCurl($userData, $common->api_url().'wishlist/saveusersetting/'.$customer_id.'/'.$proximity_notification.'/'.$push_notification.'/'.$geo_location.'/'.$phone_number,'GET',$accessToken);
			if($setting){
				if($setting == 'updated') {
					$json = '{ "success" : "1", "data" : [], "message" : "Your settings has been updated"}';
				} else if($setting == 'inserted') {
					$json = '{ "success" : "1", "data" : [], "message" : "Your settings has been saved"}';
				} else {
					$json = '{ "success" : "0", "data" : [], "message" : "Something went wrong."}';
				}
				
			} else {
				$json = '{ "success" : "0", "data" : [], "message" : "Something went wrong."}';
			}
			
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : "Required fields are missing."}';
		}
		
		return $this->SendResponse ( $json );
	}
	
	/*
	 * Params
	 * Description : get all filter options
	 */
	public function getFilterOptions(CommonController $common) {
		$menuArray = $attributeArray= $userData = array();
		$accessToken = (isset($_REQUEST['oauth_token']) && $_REQUEST['oauth_token'] != '') ? $_REQUEST['oauth_token'] : '';
		$entityType = 'catalog_product';
		$attrCode = 'ni_product_ticketype';
		$prod_attr = $common->getCurl($userData, $common->api_url().'products/attributes/'.$attrCode.'/options','GET',$common->admin_token());
		$j = 0;
		if(!empty($prod_attr)){
			foreach ( $prod_attr as $option ) {
				if (isset($option->value) && $option->value != "" ) {
					$attributeArray [$j] ['name'] = $option->label;
					$attributeArray [$j] ['id'] = $option->value;
					$j ++;
				}
			}
		}
		$sortOption = array (
				"sortyby" => array (
						array (
								"id" => "recommended",
								"name" => "Recommended" 
						),						
						array (
								"id" => "discount",
								"name" => "Discount" 
						),
						array (
								"id" => "low",
								"name" => "Price - Low to High" 
						),
						array (
								"id" => "high",
								"name" => "Price - High to Law" 
						) 
				),
				"filter" => array (
						"Cinema" => $menuArray,
						"Type" => $attributeArray 
				) 
		);
		
		$json = '{ "success" : "1", "data" : [' . json_encode ( $sortOption ) . '], "message" : ""}';
		return $this->SendResponse ( $json );
	}
	
	
	public function sort_products(CommonController $common){
		//$userData['keywords'] = (isset($_REQUEST['keywords']) && $_REQUEST['keywords'] != '') ? $_REQUEST['keywords'] : '';
		//$userData['group_id'] = (isset($_REQUEST['group_id']) && $_REQUEST['group_id'] != '') ? $_REQUEST['group_id'] : '';
		$sort_by = (isset($_REQUEST['sort_by']) && $_REQUEST['sort_by'] != '') ? $_REQUEST['sort_by'] : '';
		//$userData['type'] = (isset($_REQUEST['type']) && $_REQUEST['type'] != '') ? $_REQUEST['type'] : '';
		$accessToken = (isset($_REQUEST['oauth_token']) && $_REQUEST['oauth_token'] != '') ? $_REQUEST['oauth_token'] : '';
		$userData = array();
		$adminAccessToken = $common->admin_token();
		//$accessToken = $userData['oauth_token'];
		if($sort_by != ''){
			//$searchCriteria = '?searchCriteria[page_size]=500&searchCriteria[sortOrders][0][field]=id&searchCriteria[sortOrders][0][direction]=DESC';
			$searchCriteria = '?searchCriteria[sortOrders][0][field]=name&searchCriteria[sortOrders][0][direction]=DESC';
			
			//searchCriteria[page_size]=50&searchCriteria[filter_groups][0][filters][0][field]=price&searchCriteria[filter_groups][0][filters][0][value]=50&searchCriteria[filter_groups][0][filters][0][condition_type]=eq&
			
		} else {
			$searchCriteria = '?searchCriteria';
		}
		//echo $searchCriteria;
		
		//$test = '?searchCriteria[page_size]=50&searchCriteria[filter_groups][0][filters][0][field]=price&searchCriteria[filter_groups][0][filters][0][value]=50&searchCriteria[filter_groups][0][filters][0][condition_type]=eq';
		//$searchCriteria = '&searchCriteria[sortOrders][0][field]=name&searchCriteria[sortOrders][0][direction]=DESC';
		//echo $common->api_url().'products'.$test.$searchCriteria.'</br>'; 
		
		$productDetails = $common->getCurl($userData, $common->api_url().'products'.$searchCriteria,'GET',$adminAccessToken);
		//print_r($productDetails); exit;
		$json = '{ "success" : "1", "data" : [], "message" : "valid data."}';
		
		return $this->SendResponse ( $json );
	}
	
	
	public function getReorder(CommonController $common){
        $customer_id = isset($_REQUEST['customer_id'])?$_REQUEST['customer_id']:'';
        $increment_id = isset($_REQUEST['increment_id'])?$_REQUEST['increment_id']:'';
        $accessToken = isset($_REQUEST['oauth_token'])?$_REQUEST['oauth_token']:'';
		$productData = array();
		$cData = array();
        $chgpwdata = array();
        $data = $common->getCurl($chgpwdata, $common->api_url().'orders?searchCriteria[filterGroups][][filters][][field]=increment_id&searchCriteria[filterGroups][0][filters][0][value]='.$increment_id,'GET',$common->admin_token());
        $orderDetails = array();
		$i = 0;
		for($i=0;$i<count($data->items);$i++){
			
			//print_r($data->items[$i]->items); exit;
			
			for($j=0;$j<count($data->items[$i]->items);$j++){
				$orderDetails[$i][$j]['product_id'] = $data->items[$i]->items[$j]->product_id;
				$orderDetails[$i][$j]['name'] = $data->items[$i]->items[$j]->name;
				$orderDetails[$i][$j]['prod_img'] = "";
				$orderDetails[$i][$j]['sku'] = $data->items[$i]->items[$j]->sku;
				$orderDetails[$i][$j]['qty'] = $data->items[$i]->items[$j]->qty_ordered;
				$orderDetails[$i][$j]['price'] = $data->items[$i]->items[$j]->price;
				$sku = $data->items[$i]->items[$j]->sku;
		   
				$cart_item=array();
				$cart_item[$i]['product_id'] = $data->items[$i]->items[$j]->product_id;
				$cart_item[$i]['qty'] = $data->items[$i]->items[$j]->qty_ordered;
				
				$customerData = [
					'customer_id' => $customer_id
				];
				$quote_id = $common->getCurl($customerData, $common->api_url().'carts/mine','POST',$accessToken);
				
				$productData = [
					'cart_item' => [
						'quote_id' => $quote_id,
						'sku' => $data->items[$i]->items[$j]->sku,
						'qty' => $data->items[$i]->items[$j]->qty_ordered
					]
				];
				$cartDetails = $common->getCurl($productData, $common->api_url().'carts/mine/items','POST',$accessToken);
				
		   
			}
		}
		
        
        $myCartDetails = $common->getCurl($cData, $common->api_url().'carts/mine/items','GET',$accessToken);
		//print_r($myCartDetails); exit;
		$allCartDetails = array();
		foreach($myCartDetails as $myCart){
			$allCartDetails[] = array('product_id'=>$myCart->item_id,'qty'=>$myCart->qty,'customer_id'=>$customer_id);
		}

		if (is_array($allCartDetails) && !empty($allCartDetails)) {
			$json = '{ "success" : "1", "data" : ' . json_encode ( $allCartDetails ) . ', "message" : "Your shopping cart has been updated."}';
			
		} else{
            $json = '{ "success" : "0", "data" : ' .json_encode ($productData).',"message":"Something went wrong!"}';
        }
        return $this->SendResponse ( $json );
	}
	
	/* 
	 * return json response
	 * */
    public function getLogo(CommonController $common) {
		
		$curlurl= $common->api_url().'integration/getlogourl';

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $curlurl,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_POSTFIELDS => '',
			CURLOPT_HTTPHEADER => array(
				"content-type: application/json"
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		
		$json = '{ "success" : "1", "data" : ' .json_encode ($response).',"message":""}';
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
