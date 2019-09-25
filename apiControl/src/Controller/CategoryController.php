<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\CommonController;

class CategoryController
{
    public function __construct(){
    	
    }
    
    public function getSubCategories(CommonController $common){
		
		$cinemaCatId = ( isset($_REQUEST['catId']) && $_REQUEST['catId'] != '' ) ? $_REQUEST['catId'] : '189';
		$accessToken = (isset($_REQUEST['oauth_token']) && $_REQUEST['oauth_token'] != '') ? $_REQUEST['oauth_token'] : '';
		$postData = array();
		$subCatArr = array();
		$pCategory = $common->getCurl($postData,$common->api_url().'categories/'.$cinemaCatId,'GET',$common->admin_token());
		
		if($pCategory)
		{
			$subCatArr['category']['category_id'] = $cinemaCatId;
			$subCatArr['category']['category_name'] = $pCategory->name;
			$subCatArr['category']['category_url'] = $pCategory->name;
			$subCatArr['category']['category_img'] = false;
			foreach($pCategory->custom_attributes as $pAttr)
			{
				if($pAttr->attribute_code == 'url_path')
				{
					$subCatArr['category']['category_url'] = $common->store_url().$pAttr->value.'.html';
				}
			}
			
		}

		if(trim($cinemaCatId) != '' && trim($accessToken) != '' ){
			$condition = '?searchCriteria[filter_groups][0][filters][0][field]=parent_id&searchCriteria[filter_groups][0][filters][0][value]='.$cinemaCatId;
			
			$category = $common->getCurl($postData,$common->api_url().'categories/list'.$condition,'GET',$common->admin_token());
			//print_r($category); exit;
			if($category){
				$subCatArr['products'] = array();
				if(isset($category->items) && count($category->items) > 0)
				{
					foreach($category->items as $value)
					{
						//print_r($value); exit;
						
						$category_dtls = $common->getCurl($postData,$common->api_url().'products?searchCriteria[filter_groups][0][filters][0][field]=category_id&searchCriteria[filter_groups][0][filters][0][value]='.$value->id,'GET',$common->admin_token());	
						//print_r($category_dtls);
						$tmp = array();
						
						if(count($category_dtls->items) > 0){
							$include_menu = 0;
							$tmp['product_id'] = $value->id;
							//$tmp['parent_id'] = $value->parent_id;
							$tmp['product_name'] = $value->name;
							//$tmp['category_img'] = str_replace('appbt/','',$common->store_url()).'pub/media/catalog/category/no-product-image.png';
							$tmp['product_image'] = false;
							$tmp['product_url'] = '';
							if(isset($value->custom_attributes) && count($value->custom_attributes) > 0)
							{
								foreach($value->custom_attributes as $cAttr)
								{
									if($cAttr->attribute_code == 'include_in_mobile_menu' && $cAttr->value == 1){
										$include_menu = 1;
									}
									if($cAttr->attribute_code == 'image' && $cAttr->value != '')
									{
										$tmp['product_image'] = str_replace($common->storename.'/','',$common->store_url()).'pub/media/catalog/category/'.trim(str_replace('pub/media/catalog/category/','',$cAttr->value));
									}
									if($cAttr->attribute_code == 'url_path')
									{
										$tmp['product_url'] = $common->store_url().$cAttr->value.'.html';
									}
								}
							}
							if($include_menu == 1){
								$subCatArr['products'][] = $tmp;
							}
						} else {
							//$subCatArr['products'] = $tmp;
							//$subCatArr['products'][] = array();//'product_id' => '','product_name' => '','product_image' => '','product_url' => ''
						}
						
						
					}
					
				} else {
					$json = '{ "success" : "0", "data" : [], "message" : "Something went wrong."}';
				}
				$json = '{ "success" : "1", "data" : ['.json_encode($subCatArr).'], "message" : "Successfully get products."}';
			} else {
				$json = '{ "success" : "0", "data" : [], "message" : "Something went wrong."}';
			}
			
			//print_r($subCatArr); exit;
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : "Required parameter missing"}';
		}
		return $this->SendResponse ( $json );
		
	}
	
	/*
	 * Customer login function
	 * 
	 * request param  string  $email
	 * request param  string  $password
	 * 
	 * return json response
	 * */
	public function getCinemasCategories(CommonController $common){
		$cinemaCatId = 189;
		$postData = array();
		$group_id = ( isset($_REQUEST['group_id']) && $_REQUEST['group_id'] != '' ) ? $_REQUEST['group_id'] : '';
		$keywords = ( isset($_REQUEST['keywords']) && $_REQUEST['keywords'] != '' ) ? $_REQUEST['keywords'] : '';

		$category = $common->getCurl($postData,$common->api_url().'categories/'.$cinemaCatId.'?fields=id,name,custom_attributes','GET',$common->admin_token());

		$categoryArray = array ();		
		$categoryArray ['category_id'] 		= $category->id != "" ? $category->id : "";
		$categoryArray ['category_name'] 	= $category->name != "" ? $category->name : "";
				
		foreach($category->custom_attributes as $cat_att){
			if($cat_att->attribute_code == 'url_path'){
				$categoryArray['category_url']	= $common->store_url().$cat_att->value.'.html';
			}
			if($cat_att->attribute_code == 'image'){
				if($cat_att->value != ''){
					$categoryArray['category_img']	= $common->store_url().'media/catalog/category/'.$cat_att->value;
				} else {
					$categoryArray['category_img']	= false;
				}
			} else {
				$categoryArray['category_img']	= false;
			}
		}
		$condition = '';
		if ($keywords != "") {
			$condition .= '&searchCriteria[filter_groups][0][filters][1][field]=name&searchCriteria[filter_groups][0][filters][1][value]=%'.$keywords.'%&searchCriteria[filter_groups][0][filters][1][condition_type]=like&searchCriteria[filter_groups][0][filters][2][field]=description&searchCriteria[filter_groups][0][filters][2][value]=%'.$keywords.'%&searchCriteria[filter_groups][0][filters][2][condition_type]=like&searchCriteria[filter_groups][0][filters][3][field]=short_description&searchCriteria[filter_groups][0][filters][3][value]=%'.$keywords.'%&searchCriteria[filter_groups][0][filters][3][condition_type]=like';			
		}
		
		if($group_id != ''){
			$condition .= '&searchCriteria[filter_groups][1][filters][0][field]=entity_id&searchCriteria[filter_groups][1][filters][0][value]='.$group_id.'&searchCriteria[filter_groups][1][filters][0][condition_type]=in';
		}		
		$dataProducts = $common->getCurl($postData,$common->api_url().'products?searchCriteria[filter_groups][0][filters][0][field]=category_id&searchCriteria[filter_groups][0][filters][0][value]='.$cinemaCatId.$condition,'GET',$common->admin_token());
		
		$productArray = array ();
		$i = 0;
		if(count($dataProducts->items) > 0 ) {
			foreach($dataProducts->items as $dataProduct){
				$productArray [$i] ['product_id'] = $dataProduct->id != "" ? $dataProduct->id : "";
				$productArray [$i] ['product_name'] = $dataProduct->name != "" ? $dataProduct->name : "";
				
				if(count($dataProduct->custom_attributes) > 0){
					foreach($dataProduct->custom_attributes as $prodAtts){						
						if($prodAtts->attribute_code == 'url_key'){
							$value = $prodAtts->value;							
							$productArray [$i]['product_url']	= $value != "" ? $common->store_url().$value : "";
						}
					}
				}
				if(count($dataProduct->media_gallery_entries) > 0){
					foreach($dataProduct->media_gallery_entries as $prodImage){
						$productUrl = $common->store_url().'pub/media/catalog/product'.$prodImage->file;
						$productArray [$i]['product_image']	= $prodImage->file != "" ? $productUrl : false;
					}
				} else {
					$productArray [$i]['product_image']	= false;
				}
				$i ++;
			}
		}
		
		$productsArray = array (
				"category" => $categoryArray,
				"products" => $productArray 
		);
		
		
		if($productsArray){
			$json = '{ "success" : "1", "data" : '.json_encode($productsArray).', "message" : ""}';
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : ""}';
		}
		return $this->SendResponse ( $json );
	
	}
	
	
	public function getCategoryDetailsWithProducts(CommonController $common){
		// product_id = category id
		$storeId = '79';
		$product_id = (isset($_REQUEST['product_id']) && $_REQUEST['product_id'] != '') ? $_REQUEST['product_id'] : '';
		$customer_id = (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '') ? $_REQUEST['customer_id'] : '';
		$accessToken = (isset($_REQUEST['oauth_token']) && $_REQUEST['oauth_token'] != '') ? $_REQUEST['oauth_token'] : '';
		$sort_by = (isset($_REQUEST['sort_by']) && $_REQUEST['sort_by'] != '') ? $_REQUEST['sort_by'] : '';
		$type = (isset($_REQUEST['type']) && $_REQUEST['type'] != '') ? $_REQUEST['type'] : '';
		
		$userData = array();
		$adminAccessToken = $common->admin_token();
		
		
		if(trim($product_id) != '' && trim($accessToken) != ''){
			
			$postData = array();
			$pCategory = $common->getCurl($postData,$common->api_url().'categories/'.$product_id,'GET',$common->admin_token());
			//print_r($pCategory); exit;
			$prdArr = array();
			$prdArr['customer_id'] = $customer_id;
			
			if($pCategory)
			{
				$prdArr['prod_id'] = $product_id;
				$prdArr['prod_name'] = $pCategory->name;
				$prdArr['prod_s_desc'] = '';
				$prdArr['prod_l_desc'] = '';
				$prdArr['images'] = array();
				foreach($pCategory->custom_attributes as $pAttr)
				{
					if($pAttr->attribute_code == 'url_path')
					{
						$prdArr['prod_url'] = $common->store_url().$pAttr->value.'.html';
					}
					if($pAttr->attribute_code == 'image' && $pAttr->value != '')
					{
						$prdArr['images'][] = str_replace($common->storename.'/','',$common->store_url()).'pub/media/catalog/category/'.trim(str_replace('pub/media/catalog/category/','',$pAttr->value));
					}
					if($pAttr->attribute_code == 'description')
					{
						$prdArr['prod_s_desc'] = $pAttr->value;
						$prdArr['prod_l_desc'] = $pAttr->value;
					}
				}
				
			}
			//print_r($prdArr); exit;
			$prdArr['associated_products'] = array();
			$condition = '';
			//echo 'condition===='.$condition; exit;
			$condition .='&searchCriteria[filter_groups][1][filters][0][field]=type_id&searchCriteria[filter_groups][1][filters][0][condition_type]=eq&searchCriteria[filter_groups][1][filters][0][value]=virtual';
			$condition .='&searchCriteria[filter_groups][2][filters][0][field]=status&searchCriteria[filter_groups][2][filters][0][condition_type]=eq&searchCriteria[filter_groups][2][filters][0][value]=1';
			
			if ($type != '') {
				//$types = explode ( ",", $type );
				//$condition .= '&searchCriteria[filter_groups][3][filters][0][field]=ni_product_ticketype&searchCriteria[filter_groups][3][filters][0][value]='.$type.'&searchCriteria[filter_groups][3][filters][0][condition_type]=finset';
				$condition .= '&searchCriteria[filter_groups][3][filters][0][field]=ni_product_ticketype&searchCriteria[filter_groups][3][filters][0][value]='.$type.'&searchCriteria[filter_groups][3][filters][0][condition_type]=in';
			}
			
			//echo 'condition===='.$condition; exit;
			$dataProducts = $common->getCurl($postData,$common->api_url().'products?searchCriteria[filter_groups][0][filters][0][field]=category_id&searchCriteria[filter_groups][0][filters][0][value]='.$product_id.$condition,'GET',$common->admin_token());
			//$dataProducts = $common->getCurl($postData,$common->api_url().'categories/'.$product_id.'/products','GET',$common->admin_token());
			//print_r($dataProducts); exit;// &fields=items[id,name,status,price,type_id,custom_attributes[attribute_code,value]]
			
			//echo 'condition===='.$condition; exit;
			
			//print_r($dataProducts); exit;
			
			if($dataProducts){
				
				if(isset($dataProducts->items) & count($dataProducts->items) > 0){
					$prdTmp = $dataProducts->items;
					$i=0;
					$tArr = array();
					
					//print_r($dataProducts->items); exit;
					foreach($dataProducts->items as $dataProduct){
						//print_r($prdTmp[$i]); 
						$strId = array();
						if($prdTmp[$i]->status == 1 && $prdTmp[$i]->type_id == 'virtual'){//$prdTmp[$i]->type_id == 'simple' ||
							//print_r($prdTmp[$i]); 
							$tArr['price'] = $prdTmp[$i]->price;
							$strId = $prdTmp[$i]->extension_attributes->website_ids;
							
							if(in_array($storeId,$strId)){
								$is_mobile_active = 0;
								foreach($prdTmp[$i]->custom_attributes as $pAttr)
								{
									if($pAttr->attribute_code == 'special_price')
									{
										$tArr['price'] = $pAttr->value;
									}
									
									if($pAttr->attribute_code == 'is_mobile_active' && $pAttr->value == 1)
									{
										$is_mobile_active = 1;
									}
								}
								if ($sort_by == "recommended") {
									$custDetails = $common->getCurl($postData,$common->store_url().'rest/V1/customers/me?fields=email','GET',$accessToken);
				
									$custOrders = $common->getCurl($postData,$common->api_url().'orders?searchCriteria[filter_groups][0][filters][0][field]=customer_email&searchCriteria[filter_groups][0][filters][0][value]='.$custDetails->email.'&fields=items','GET',$common->admin_token());
									
									foreach($custOrders->items as $cOrder){
										$productsOrder = array();	
										$productsQty = 0;
										foreach($cOrder->items as $oProds){
											if($prdTmp[$i]->id == $oProds->product_id){
												$tArr['product_bought'] = $oProds->qty_ordered;
											} else {
												$tArr['product_bought'] = 0;
											}
										}
									}
								}
								/*if ($getFinalPrice < $prod_price) {
									$prod_special_price = 100 - round ( ($getFinalPrice / $prod_price) * 100 );
								} else {
									$prod_special_price = 0;
								}*/
								$tArr['id'] = $prdTmp[$i]->id;
								$tArr['name'] = $prdTmp[$i]->name;
								
								$tArr['is_favourite'] = 0;
								$tArr['quantity'] = 0.00;
								if($is_mobile_active == 1){
									$prdArr['associated_products'][] = $tArr;
								}
							}
						}
						$i++;
					}
					//exit;						
				}
				
				if ($sort_by == "low") {
					usort ( $prdArr['associated_products'], function ($a, $b) {
						return $a ['price'] - $b ['price'];
					} );
				} else if ($sort_by == "high") {
					usort ( $prdArr['associated_products'], function ($a, $b) {
						return $b ['price'] - $a ['price'];
					} );
				} else if ($sort_by == "popular") {
					usort ( $prdArr['associated_products'], function ($a, $b) {
						return $b ['views'] - $a ['views'];
					} );
				} else if ($sort_by == "discount") {
					usort ( $prdArr['associated_products'], function ($a, $b) {
						//return $a ['prod_special_price'] - $b ['prod_special_price'];
						return $a ['price'] - $b ['price'];
					} );
				} else if ($sort_by == "recommended") {
					usort ( $prdArr['associated_products'], function ($a, $b) {
						return $b ['product_bought'] - $a ['product_bought'];
					} );
				}
				//print_r($prdArr); exit;
				
				$json = '{ "success" : "1", "data" : ['.json_encode($prdArr).'], "message" : ""}';
			} else {
				$json = '{ "success" : "0", "data" : [], "message" : "Product not found."}';
			}
		} else {
			$json = '{ "success" : "0", "data" : ['.json_encode($common->api_url().'products').'], "message" : "Required parameters missing."}';
		}
		
		return $this->SendResponse ( $json );
	}
	
	
	/*public function getCategoryDetailsWithProducts(CommonController $common){
		
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
							if($val->attribute_code == 'url_key'){
								$prdArr['prod_url'] = $common->store_url().$val->value;
							}
							if($val->attribute_code == 'image'){
								$prdArr['images'][] = $common->store_url().'pub/media/catalog/product'.$val->value;
							}
						}
					}
					
					
					$prdArr['is_favourite'] = 0;
					
				}
				
				
				$json = '{ "success" : "1", "data" : ['.json_encode($prdArr).'], "message" : ""}';
			} else {
				$json = '{ "success" : "0", "data" : [], "message" : "Product not found."}';
			}
		} else {
			$json = '{ "success" : "0", "data" : ['.json_encode($common->api_url().'products').'], "message" : "Required parameters missing."}';
		}
		
		return $this->SendResponse ( $json );
	}*/

	
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
