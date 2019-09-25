<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\CommonController;

class CartController
{
    public function __construct(){
    	
    }
	
	/*
	 * Params
	 * customer_id : customer id of registering user
	 * cart_item : cart items array provided by IOS team.
	 * Description : add to cart multiple products
	 */
	public function addToCart(CommonController $common) {
		$customer_id = (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '') ? $_REQUEST['customer_id'] : '';
		$cart_item = (isset($_REQUEST['cart_item']) && $_REQUEST['cart_item'] != '') ? $_REQUEST['cart_item'] : '';
		$accessToken = (isset($_REQUEST['oauth_token']) && $_REQUEST['oauth_token'] != '') ? $_REQUEST['oauth_token'] : '';
		$allPdata = json_decode($cart_item);
		
		$customerData = [
			'customer_id' => $customer_id
		];
		$quote_id = $common->getCurl($customerData, $common->api_url().'carts/mine','POST',$accessToken);
	
		foreach($allPdata as $pdata){
		
			$pDetails = $common->getCurl($customerData, $common->api_url().'products?searchCriteria[filterGroups][0][filters][0][field]=entity_id&searchCriteria[filterGroups][0][filters][0][condition_type]=eq&searchCriteria[filterGroups][0][filters][0][value]='.$pdata->product_id,'GET',$common->admin_token());
			
			$productData = [
				'cart_item' => [
					'quote_id' => $quote_id,
					'sku' => $pDetails->items[0]->sku,
					'qty' => $pdata->qty
				]
			];
			$prodQty = $common->getCurl($customerData, $common->root_store_url().'apiControl/public/cart/checkQuantity?sku='.$pDetails->items[0]->sku,'GET',$accessToken);
			$totalQty = $prodQty->data[0]->qty;
			//if($totalQty > 0){
			if($totalQty > $pdata->qty){
			} else {
				$json = '{ "success" : "0", "data" : [], "message" : "Qty is low."}';
				return $this->SendResponse ( $json );
			}
			$cartDetails = $common->getCurl($productData, $common->api_url().'carts/mine/items','POST',$accessToken);
		}
			
		
		$cData = array();
		$myCartDetails = $common->getCurl($cData, $common->api_url().'carts/mine/items','GET',$accessToken);
		
		$allCartDetails = array();
		foreach($myCartDetails as $myCart){
			$allCartDetails[] = array('product_id'=>$myCart->item_id,'qty'=>$myCart->qty,'customer_id'=>$customer_id);
		}
		
		if (is_array($allCartDetails) && !empty($allCartDetails)) {
			$json = '{ "success" : "1", "data" : ' . json_encode ( $allCartDetails ) . ', "message" : "Your selected product has been successfully added to cart."}';
			return $this->SendResponse ( $json );
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : "customer_id and cart_item are missing"}';
			return $this->SendResponse ( $json );
		}
		
	}
	
	/*
	 * Params
	 * customer_id : customer id of registering user
	 * Description : get all items in the cart for specific user
	 */
	public function userCartDetails(CommonController $common) {
		$customer_id = (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '') ? $_REQUEST['customer_id'] : '';
		$accessToken = (isset($_REQUEST['oauth_token']) && $_REQUEST['oauth_token'] != '') ? $_REQUEST['oauth_token'] : '';
		$productArray = $userData = array();
		
		if ($customer_id != "") {
			
			
			$b_address = $common->getCurl($userData, $common->api_url().'customers/me/billingAddress','GET',$accessToken);
			//
			if( !empty($b_address) ) {
				$getId = $b_address->id;
				$getFirstname = $b_address->firstname;
				$getLastname = $b_address->lastname;
				$getStreet1 = (isset($b_address->street[0]) && $b_address->street[0] != '') ? $b_address->street[0] : '';
				$getStreet2 = (isset($b_address->street[1]) && $b_address->street[1] != '') ? $b_address->street[1] : '';
				$getCity = $b_address->city;
				$getCountryId = $b_address->country_id;
				$getRegionId = $b_address->region->region_id;
				$getRegionCode = $b_address->region->region_code;
				$getRegion = $b_address->region->region;
				$getPostcode = $b_address->postcode;
				$getTelephone = $b_address->telephone;
			} else {
				$getId = "";
				$getFirstname = "";
				$getLastname = "";
				$getStreet1 = "";
				$getStreet2 = "";
				$getCity = "";
				$getCountryId = "";
				$getRegion = "";
				$getRegionId = '';
				$getregionCode = '';
				$getPostcode = "";
				$getTelephone = "";
			}	
			
			$billingAddress = array( 
				'address'=> array (
					'id' => $getId,
					'prefix' => '',
					'firstname' => $getFirstname,
					'middlename' => '',
					'lastname' => $getLastname,
					'suffix' => '',
					'company' => '',
					'street' => array (
						'0' => $getStreet1,
						'1' => $getStreet2
					),
					'city' => $getCity,
					'country_id' => $getCountryId,
					'region_id' => $getRegionId,
					'region' => $getRegion,
					'postcode' => $getPostcode,
					'telephone' => $getTelephone,
					'fax' => '',
					'vat_id' => '',
					'save_in_address_book' => 1,
					'same_as_billing' => 1
				),
				"useForShipping"=> true
			);
			$b_address = $common->getCurl($billingAddress, $common->api_url().'carts/mine/billing-address','POST',$accessToken);
			
			$billingAddress = array (
				'customerAddressId' => $getId,
				'prefix' => '',
				'firstname' => $getFirstname,
				'middlename' => '',
				'lastname' => $getLastname,
				'suffix' => '',
				'company' => '',
				'street' => array (
					'0' => $getStreet1,
					'1' => $getStreet2
				),
				'city' => $getCity,
				'country_id' => $getCountryId,
				'region_id' => $getRegionId,
				'regionCode' => $getRegionCode,
				'customerId' => $customer_id,
				'region' => $getRegion,
				'postcode' => $getPostcode,
				'telephone' => $getTelephone,
				'fax' => '',
				'vat_id' => '',
				'save_in_address_book' => null,
				'same_as_billing'=> 1
			);
			
			$cartDetials = $common->getCurl($userData, $common->api_url().'carts/mine','GET',$accessToken);
			
			//print_r($cartDetials); exit;
			
			if(isset($cartDetials->id) && $cartDetials->id != ''){
				$methodsandaddress = array (
						'cartId'=>$cartDetials->id,
						'paymentMethod' => 
						array (
							'method' => 'braintree',
							'extension_attributes' => 
							array (
								'agreement_ids' => 
								array (
									
								),
							),
							'additional_data' => 
							array (
								'fooman_payment_surcharge_preview' => true,
								'buckaroo_skip_validation' => true,
							),						
						),
						'billing_address' => $billingAddress,			 
					);
					
				$incrementId = $common->getCurl($methodsandaddress, $common->api_url().'carts/mine/set-payment-information','POST',$accessToken);
				//print_r($incrementId); exit;
			}
			
			
			$cartDetails = $common->getCurl($userData, $common->api_url().'carts/mine/totals','GET',$accessToken);
			//print_r($cartDetails);
			
			$postData = $cData = array();
			$myCartDetails = $common->getCurl($cData, $common->api_url().'carts/mine/items','GET',$accessToken);
			$i = 0;
			//print_r($myCartDetails);
			if(!empty($myCartDetails)){
				foreach($myCartDetails as $mycart){
					if(isset($mycart->message) && $mycart->message == 'Current customer does not have an active cart.'){
						$json = '{ "success" : "1", "data" : ' . json_encode ( $productArray ) . ', "message" : "Your cart is empty!"}';
					} else {
						
						$dataProducts = $common->getCurl($postData,$common->api_url().'products/'.$mycart->sku,'GET',$common->admin_token());
						$productArray [$i]['product_id'] = $mycart->item_id;
						$productArray [$i]['name'] = $mycart->name;
						if(count($dataProducts->media_gallery_entries) > 0){
							foreach($dataProducts->media_gallery_entries as $prodImage){
								$productUrl = $common->store_url().'pub/media/catalog/product'.$prodImage->file;
								$productArray [$i]['prod_img']	= $prodImage->file != "" ? $productUrl : false;
							}
						} else {
							$productArray [$i]['prod_img']	= false;
						}
						$productArray [$i]['sku'] = $mycart->sku;
						$productArray [$i]['qty'] = $mycart->qty;
						$productArray [$i]['price'] = $mycart->price;
						$productArray [$i]['surcharge'] = $cartDetails;
						$i ++;
					}
				}
			}
			if(!empty($productArray)){
				$json = '{ "success" : "1", "data" : ' . json_encode ( $productArray ) . ', "message" : ""}';
			} else {
				$json = '{ "success" : "1", "data" : ' . json_encode ( $productArray ) . ', "message" : "Your cart is empty!"}';
			}
			return $this->SendResponse ( $json );
			
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : "customer_id is missing"}';
			return $this->SendResponse ( $json );
		}
		exit;
	}
	
	/*
	 * Params
	 * customer_id : customer id of registering user
	 * product_id : auto increment id for specific product
	 * Description : delete specific product from the cart
	 */
	public function releaseCartProduct(CommonController $common) {
		$customer_id = (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '') ? $_REQUEST['customer_id'] : '';
		$product_id = (isset($_REQUEST['product_id']) && $_REQUEST['product_id'] != '') ? $_REQUEST['product_id'] : '';
		$accessToken = (isset($_REQUEST['oauth_token']) && $_REQUEST['oauth_token'] != '') ? $_REQUEST['oauth_token'] : '';
		$productArray = array();
		if ($customer_id != "" && $product_id != '') {
			$postData = $cData = array();
			$myCartDetails = $common->getCurl($cData, $common->api_url().'carts/mine/items','GET',$accessToken);
			
			$customerData = [
				'customer_id' => $customer_id
			];
			$pDetails = $common->getCurl($customerData, $common->api_url().'products?searchCriteria[filterGroups][0][filters][0][field]=entity_id&searchCriteria[filterGroups][0][filters][0][condition_type]=eq&searchCriteria[filterGroups][0][filters][0][value]='.$product_id,'GET',$common->admin_token());
			
			$pSku = $pDetails->items[0]->sku;
			
			foreach($myCartDetails as $myCart){
				if($pSku == $myCart->sku){				
					$removeCartItem = $common->getCurl($cData, $common->api_url().'carts/mine/items/'.$myCart->item_id,'DELETE',$accessToken);
				}
			}
			//exit;
			$myCartDetails = $common->getCurl($cData, $common->api_url().'carts/mine/items','GET',$accessToken);
			$i = 0;
			foreach($myCartDetails as $mycart){
				$productArray [$i]['product_id'] = $mycart->item_id;
				$productArray [$i]['name'] = $mycart->name;
				$productArray [$i]['sku'] = $mycart->sku;
				$productArray [$i]['qty'] = $mycart->qty;
				$productArray [$i]['price'] = $mycart->price;
				$i ++;
			}
			if(!empty($productArray)){
				$json = '{ "success" : "1", "data" : ' . json_encode ( $productArray ) . ', "message" : "Your selected product has been successfully removed from cart"}';
			} else {
				$json = '{ "success" : "1", "data" : ' . json_encode ( $productArray ) . ', "message" : "Your cart is empty."}';
			}
			
			return $this->SendResponse ( $json );
			
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : "customer_id or product_id is missing"}';
			return $this->SendResponse ( $json );
		}
	}
	
	
	/*
	 * Params
	 * customer_id : customer id of registering user
	 * product_id : product id to update quantity
	 * qty : quantity to update quantity
	 * oauth_token : Customer authenticate token
	 * Description : add to cart multiple products
	 */
	public function updateCartQuantity(CommonController $common) {
		$customer_id = (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '') ? $_REQUEST['customer_id'] : '';
		$product_id = (isset($_REQUEST['product_id']) && $_REQUEST['product_id'] != '') ? $_REQUEST['product_id'] : '';
		$qty = (isset($_REQUEST['qty']) && $_REQUEST['qty'] != '') ? $_REQUEST['qty'] : '';
		$accessToken = (isset($_REQUEST['oauth_token']) && $_REQUEST['oauth_token'] != '') ? $_REQUEST['oauth_token'] : '';
		
		$customerData = [
			'customer_id' => $customer_id
		];
		$quote_id = $common->getCurl($customerData, $common->api_url().'carts/mine','POST',$accessToken);
		$pDetails = $common->getCurl($customerData, $common->api_url().'products?searchCriteria[filterGroups][0][filters][0][field]=entity_id&searchCriteria[filterGroups][0][filters][0][condition_type]=eq&searchCriteria[filterGroups][0][filters][0][value]='.$product_id,'GET',$common->admin_token());
		//print_r($pDetails); exit;
		
		$customerData = [
			'sku' => $pDetails->items[0]->sku
		];
		
		$prodQty = $common->getCurl($customerData, $common->root_store_url().'apiControl/public/cart/checkQuantity?sku='.$pDetails->items[0]->sku,'GET',$accessToken);
		$totalQty = $prodQty->data[0]->qty;
		
		if($totalQty > 0){
			$cData = array();
			$myCartDetails = $common->getCurl($cData, $common->api_url().'carts/mine/items','GET',$accessToken);
			
			$allCartDetails = array();
			foreach($myCartDetails as $myCart){
				$productData = [
					'cart_item' => [
						'quote_id' => $quote_id,
						'item_id' => $myCart->item_id,
						//'sku' => $pDetails->items[0]->sku,
						'qty' => $qty
					]
				];
				$cartDetails = $common->getCurl($productData, $common->api_url().'carts/'.$quote_id.'/items/'.$myCart->item_id,'PUT',$common->admin_token());
				
				$allCartDetails[] = array('product_id'=>$myCart->item_id,'qty'=>$myCart->qty,'customer_id'=>$customer_id);
			}
			//$myCartDetails = $common->getCurl($cData, $common->api_url().'carts/mine/items','GET',$accessToken);
			
			 //exit;
			if (is_array($allCartDetails) && !empty($allCartDetails)) {
				$json = '{ "success" : "1", "data" : [], "message" : "Your selected product has been successfully updated to cart."}';
				return $this->SendResponse ( $json );
			} else {
				$json = '{ "success" : "0", "data" : [], "message" : "customer_id is missing"}';
				return $this->SendResponse ( $json );
			}
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : "Qty is low."}';
			return $this->SendResponse ( $json );
		}
		
	}
	
	
	/*
	 * Params
	 * customer_id : customer id of registering user
	 * oauth_token : Customer authenticate token
	 * Description : add to cart multiple products
	 */
	public function getUpsellItems(CommonController $common) {
		$customer_id = (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '') ? $_REQUEST['customer_id'] : '';
		$accessToken = (isset($_REQUEST['oauth_token']) && $_REQUEST['oauth_token'] != '') ? $_REQUEST['oauth_token'] : '';
		
		$customerData = [
			'customer_id' => $customer_id
		];
		$quote_id = $common->getCurl($customerData, $common->api_url().'carts/mine','POST',$accessToken);
		$cData = array();
		$myCartDetails = $common->getCurl($cData, $common->api_url().'carts/mine/items','GET',$accessToken);
		if(count($myCartDetails) > 0){
			foreach($myCartDetails as $myCart){
				$pDetails = $common->getCurl($customerData, $common->api_url().'products?searchCriteria[filterGroups][0][filters][0][field]=sku&searchCriteria[filterGroups][0][filters][0][condition_type]=eq&searchCriteria[filterGroups][0][filters][0][value]='.$myCart->sku,'GET',$common->admin_token());
				$upsellProducts = array();
				if(count($pDetails->items) > 0){
					foreach($pDetails->items as $pd ){
						foreach($pd->product_links as $plinkList){
							if($plinkList->link_type == 'upsell'){
								$upsellProducts[] = $plinkList->linked_product_sku;
							}					
						}
					}
				}
			}
			$allUpsellProduct = array();
			$i = 0;
			foreach($upsellProducts as $pdetials ){
				$upProduct = $common->getCurl($customerData, $common->api_url().'products/'.$pdetials,'GET',$common->admin_token());
				$allUpsellProduct[$i]['product_id'] = $upProduct->id;
				$allUpsellProduct[$i]['name'] = $upProduct->name;
				$allUpsellProduct[$i]['sku'] = $upProduct->sku;
				$allUpsellProduct[$i]['prod_img'] = 'false';
				if(count($upProduct->media_gallery_entries) > 0){
					foreach($upProduct->media_gallery_entries as $prodImage){
						$productUrl = $common->store_url().'pub/media/catalog/product'.$prodImage->file;
						$allUpsellProduct[$i]['prod_img']	= $prodImage->file != "" ? $productUrl : false;
					}
				}
				$allUpsellProduct[$i]['price'] = $upProduct->price;
				$i++;
			}
			$json = '{ "success" : "1", "data" : '.json_encode($allUpsellProduct).', "message" : ""}';
			return $this->SendResponse ( $json );
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : "Cart is empty."}';
			return $this->SendResponse ( $json );
		}
	}
	
	public function checkQuantity(){
		$sku = isset($_REQUEST['sku'])?$_REQUEST['sku']:'';
       
		$soapUrl = "http://13.236.249.168:8082/NiWebService.asmx?wsdl"; // asmx URL of WSDL
        $soapaction = "http://13.236.249.168:8082/NiWebService.asmx?wsdl";
        $xml_post_string = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:tem="http://tempuri.org/">

        <soap:Body>
            <tem:CheckStock>
                <!--Optional:-->
                <tem:arrayPara>
                    <!--Zero or more repetitions:-->
                    <tem:string>'.$sku.'</tem:string>
                </tem:arrayPara>
                <!--Optional:-->
                <tem:strEmail>ericyap@acsolv.com</tem:strEmail>
                <!--Optional:-->
                <tem:strPassword>642udgO</tem:strPassword>
            </tem:CheckStock>
        </soap:Body>
        </soap:Envelope>
        ';   // data from the form, e.g. some ID number

	   $headers = array(
					"Content-type: text/xml;charset=\"utf-8\"",
					"Accept: text/xml",
					"Cache-Control: no-cache",
					"Pragma: no-cache", 
					"Content-length: ".strlen($xml_post_string),
				); //SOAPAction: your op URL

		$url = $soapUrl;


		// PHP cURL  for https connection with auth
		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
		//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		// converting
		$response = curl_exec($ch);

		
		// converting
		
		$response1 = str_replace("<soap:Body>","",$response);
		$response2 = str_replace("</soap:Body>","",$response1);
        
		// convertingc to XML
		$parser = simplexml_load_string($response2);
        $a  = $parser->CheckStockResponse->CheckStockResult;
        $b =simplexml_load_string($a);
        $resp = array();
        $resp['sku'] = (string)$b->item->sku[0];
        $resp['qty'] = (string)$b->item->qty;
        
		if(!empty($resp)){
			$json = '{ "success" : "1", "data" : ['.json_encode($resp).'], "message" : ""}';
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : "Invalid data."}';
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
