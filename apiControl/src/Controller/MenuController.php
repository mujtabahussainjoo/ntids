<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\CommonController;



class MenuController extends CommonController
{
    var $api_url = '';
    var $bearer_token = '';

    public function __construct(){
		$this->api_url = 'http://10.0.0.196/magento/rest/V1/';
		
    }
	
	/*
	 * Customer login function
	 * 
	 * request param  string  $email
	 * request param  string  $password
	 * 
	 * return json response
	 * */
	public function userLogin(CommonController $common){
		$logindata = array();
		$logindata['username'] = $_REQUEST['email'];
		$logindata['password'] = $_REQUEST['password'];
		
		$accessToken = $common->getToken($logindata, $this->api_url.'integration/customer/token/');
		$userData = array();
		$customerDetails = $common->getCurl($userData, $this->api_url.'customers/me/','GET',json_decode($accessToken));
		
		$customer_data = array();
		$customer_data['id'] = $customerDetails->id;
		$customer_data['first_name'] = $customerDetails->firstname;
		$customer_data['last_name'] = $customerDetails->lastname;
		$customer_data['email'] = $customerDetails->email;
		if(!empty($customer_data)){
			$json = '{ "success" : "1","oauth_token": "'.$accessToken.'", "oauth_token_secret": "'.$accessToken.'", "data" : ['.json_encode($customer_data).'], "message" : "You are Logged In."}';
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : "Invalid data."}';
		}
		return $this->SendResponse ( $json );
	}

	/*
	 * Customer forgot password function
	 * 
	 * request param  string  $email
	 * 
	 * return json response
	 * */
	
    
    public function get_menu_options(CommonController $common){
		$frgtpwdata = array();		
	
		$userData = array();
		$category_data = array();
		$result = array();
		$data = $common->getCurl($userData,$common->api_url().'categories','GET',$common->admin_token());
		
		$category_data[] =  json_decode(json_encode($common->getCurl($userData,$common->api_url().'categories/'.$data->id,'GET',$common->admin_token())));
		$i = 0;
		foreach($data->children_data as $category){
			$j = $i + 1;
			$category_data[] = json_decode(json_encode($common->getCurl($userData,$common->api_url().'categories/'.$category->id,'GET',$common->admin_token())));
			$result[$i]['title'] = $category_data[$i]->name;
			$result[$i]['order'] = $j;
			$result[$i]['id'] = $category_data[$i]->id;
			foreach($category_data[$i]->custom_attributes as $cat_att){
				
			if($cat_att->attribute_code == 'url_key' && $cat_att->value =='home'){
				$result[$i]['slug'] = $cat_att->value;
				$result[$i]['url'] = $common->store_url().'home-mobile-app';
				$result[$i]['menu_id'] = 1;	
			}
			if($cat_att->attribute_code == 'url_key' && $cat_att->value =='promotions'){
				$result[$i]['slug'] = $cat_att->value;
				$result[$i]['url'] = $common->store_url().'promotions';
				$result[$i]['menu_id'] = $j;
			}
			if($cat_att->attribute_code =='url_key' && $cat_att->value =='cinemas'){
				$result[$i]['slug'] = $cat_att->value;
				$result[$i]['url'] = $common->store_url().'cinemas';
				$result[$i]['menu_id'] = $j;
			}
			if($cat_att->attribute_code =='url_key' && $cat_att->value =='favourites'){
				$result[$i]['slug'] = $cat_att->value;
				$result[$i]['url'] = $common->store_url().'favourites';
				$result[$i]['menu_id'] = $j;
			}
			if($cat_att->attribute_code =='url_key' && $cat_att->value =='my-purchases'){
				$result[$i]['slug'] = 'sales/order/history/';
				$result[$i]['url'] = $common->store_url().'sales/order/history/';
				$result[$i]['menu_id'] = $j;
			}
			if($cat_att->attribute_code=='url_key' && $cat_att->value=='settings'){
				$result[$i]['slug'] = 'customer/account/';
				$result[$i]['url'] = $common->store_url().'customer/account/';
				$result[$i]['menu_id'] = $j;
			}
			if($cat_att->attribute_code =='url_key' && $cat_att->value =='logout'){
				$result[$i]['slug'] = $cat_att->value;
				$result[$i]['url'] = $common->store_url().'customer/account/logout/';
				$result[$i]['menu_id'] = $j;
			}
			if($cat_att->attribute_code =='url_key' && $cat_att->value =='user-guide'){
				$result[$i]['slug'] = $cat_att->value;
				$result[$i]['url'] = $common->store_url().'user-guide/';
				$result[$i]['menu_id'] = $j;		
			}
		}
		$i++;
	}
		if($result){
			$json = '{ "success" : "1", "data" : '.json_encode($result).', "message" : ""}';
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : ""}';
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
    function SendResponse($body = '', $content_type = 'text/html') {
        // header ( 'HTTP/1.1' );
        // header ( 'Content-type: ' . $content_type );
        // echo $body;
        // exit ();
        return new Response(
            $body
        );
    }
}
