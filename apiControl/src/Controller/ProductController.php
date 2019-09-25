<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\CommonController;

class ProductController
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
	
	public function promotionProducts(CommonController $common){
		
		$product_id = (isset($_REQUEST['product_id']) && $_REQUEST['product_id'] != '') ? $_REQUEST['product_id'] : '';
		$customer_id = (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '') ? $_REQUEST['customer_id'] : '';
		$accessToken = (isset($_REQUEST['oauth_token']) && $_REQUEST['oauth_token'] != '') ? $_REQUEST['oauth_token'] : '';
		$userData = array();
		$adminAccessToken = $common->admin_token();
		
		if(trim($product_id) != '' && trim($accessToken) != ''){
			
			$searchCriteria = '?searchCriteria[filter_groups][0][filters][0][field]=entity_id&searchCriteria[filter_groups][0][filters][0][value]='.trim($product_id.'&searchCriteria[filter_groups][0][filters][0][condition_type]=eq');
			$productDetails = $common->getCurl($userData, $common->api_url().'products'.$searchCriteria,'GET',$adminAccessToken);
			
			if($productDetails){
				
				$prdArr = array();
				if(isset($productDetails->items)){
					$prdTmp = $productDetails->items;
					
					
					$prdArr['customer_id'] = $customer_id;
					$prdArr['prod_id'] = $prdTmp[0]->id;
					$prdArr['prod_name'] = $prdTmp[0]->name;
					$prdArr['prod_price'] = $prdTmp[0]->price;
					$prdArr['images'] = array();
					if(isset($prdTmp[0]->custom_attributes) && count($prdTmp[0]->custom_attributes) > 0) {
						
						foreach($prdTmp[0]->custom_attributes as $val) {
							
							if($val->attribute_code == 'short_description'){
								$prdArr['prod_s_desc'] = $val->value;
							}
							if($val->attribute_code == 'description'){
								$prdArr['prod_l_desc'] = $val->value;
							}
							
							if($val->attribute_code == 'image'){
								$prdArr['images'][] = $common->store_url().'pub/media/catalog/product'.$val->value;
							}
							
							if($val->attribute_code == 'url_key'){
								$prdArr['prod_url'] = $common->store_url().$val->value;
							}
							
						}
					}
					
					
					$prdArr['is_favourite'] = 0;
					
				}
				
				
				$json = '{ "success" : "1", "data" : ['.json_encode($prdArr).'], "message" : "valid data."}';
			} else {
				$json = '{ "success" : "0", "data" : [], "message" : "Product not found."}';
			}
		} else {
			$json = '{ "success" : "0", "data" : ['.json_encode($common->api_url().'products').'], "message" : "Required parameters missing."}';
		}
		
		
		//$test = '?searchCriteria[page_size]=50&searchCriteria[filter_groups][0][filters][0][field]=price&searchCriteria[filter_groups][0][filters][0][value]=50&searchCriteria[filter_groups][0][filters][0][condition_type]=eq';
		//$searchCriteria = '&searchCriteria[sortOrders][0][field]=name&searchCriteria[sortOrders][0][direction]=DESC';
		//echo $common->api_url().'products'.$test.$searchCriteria.'</br>'; 
		
		
		return $this->SendResponse ( $json );
	}
	
	public function addRemoveFavourite(CommonController $common)
	{
		
		$product_id = (isset($_REQUEST['product_id']) && $_REQUEST['product_id'] != '') ? $_REQUEST['product_id'] : '';
		$customer_id = (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '') ? $_REQUEST['customer_id'] : '';
		$is_favourite = (isset($_REQUEST['is_favourite']) && trim($_REQUEST['is_favourite']) != '') ? $_REQUEST['is_favourite'] : '';
		$accessToken = (isset($_REQUEST['oauth_token']) && $_REQUEST['oauth_token'] != '') ? $_REQUEST['oauth_token'] : '';
		$userData = array();
		$resp = array();
		$resp['customer'] = array('customer_id'=>$customer_id);
		$resp['products'] = array();
		if($is_favourite == 1){ // add to favourite
			
			$data = $common->getCurl($userData, $common->api_url().'wishlist/add/'.$customer_id.'/'.$product_id.'/','GET',$accessToken);
			
			if($data){
				$resp['products'] = $data;
				$json = '{ "success" : "1", "data" : ['.json_encode($resp).'], "message" : "Your selected product has been successfully added to your favourite."}';
			} else {
				$json = '{ "success" : "0", "data" : [], "message" : "Please try again later1."}';
			}
			
		} else { // remove from favourite
			
			$data = $common->getCurl($userData, $common->api_url().'wishlist/remove/'.$customer_id.'/'.$product_id.'/','GET',$accessToken);
			if($data){
				$resp['products'] = $data;
				$json = '{ "success" : "1", "data" : ['.json_encode($resp).'], "message" : "Your selected product has been successfully removed from your favourite."}';
			} else {
				$json = '{ "success" : "0", "data" : ['.json_encode($data).'], "message" : "Please try again later."}';
			}
		}
		
		
		return $this->SendResponse ( $json );
	}
	
	public function get_user_favorite_products(CommonController $common){
		$userData = array();
		$customer_id = (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '') ? $_REQUEST['customer_id'] : '';
		$accessToken = (isset($_REQUEST['oauth_token']) && $_REQUEST['oauth_token'] != '') ? $_REQUEST['oauth_token'] : '';
		$dataPrd = $common->getCurl($userData, $common->api_url().'wishlist/product/'.$customer_id.'/','GET',$accessToken);
		if($dataPrd){
			$resp = array();
			foreach($dataPrd as $val)
			{
				$resp[] = $val;
			}
			
			$json = '{ "success" : "1", "data" : '.json_encode($resp).', "message" : "Your selected product has been successfully added to your favourite."}';
		} else {
			//$json = '{ "success" : "0", "data" : [], "message" : "Please try again later."}';
			$json = '{ "success" : "1", "data" : [], "message" : "Item not found."}';
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
