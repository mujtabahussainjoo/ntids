<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\CommonController;

class PurchaseController
{
    public function __construct(){
    	
    }
	
	/*
	 * Params
	 * customer_id : customer id for registered users.
	 * nonce_from_client : customer nonce from braintree
	 * amount : total amount from the cart
	 * save_my_card : 0 or 1, add card details in the braintree would be 1
	 * newsletter : 0 or 1, subscribe specific user to newsletter would be 1
	 * Description : confirm purchase for generate order 
	 */
	public function getConfirmPurchase(CommonController $common) {
		$customer_id = (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '') ? $_REQUEST['customer_id'] : '';
		$accessToken = (isset($_REQUEST['oauth_token']) && $_REQUEST['oauth_token'] != '') ? $_REQUEST['oauth_token'] : '';
		$newsletter = (isset($_REQUEST['newsletter']) && $_REQUEST['newsletter'] != '') ? $_REQUEST['newsletter'] : '';
		$save_my_card = (isset($_REQUEST['save_my_card']) && $_REQUEST['save_my_card'] != '') ? $_REQUEST['save_my_card'] : '';
		$nonce_from_client = (isset($_REQUEST['nonce_from_client']) && $_REQUEST['nonce_from_client'] != '') ? $_REQUEST['nonce_from_client'] : '';
		
		if($customer_id != ''){
			$userData = array();
			if ($newsletter == 1) {
				
				$customerDetails = $common->getCurl($userData, $common->api_url().'customers/me','GET',$accessToken);
				
				$custData = array();
				if(!empty($customerDetails)){
					$custData['customer']['id'] = $customerDetails->id;
					$custData['customer']['email'] = $customerDetails->email;
					$custData['customer']['firstname'] = $customerDetails->firstname;
					$custData['customer']['lastname'] = $customerDetails->lastname;
					$custData['customer']['store_id'] = $customerDetails->store_id;
					$custData['customer']['website_id'] = $customerDetails->website_id;
					$custData['customer']['extension_attributes'] = array('is_subscribed'=>true);
				}
				$cartDetails = $common->getCurl($custData, $common->api_url().'customers/'.$customer_id,'PUT',$common->admin_token());
				
				$customerDetails = $common->getCurl($userData, $common->api_url().'customers/me','GET',$accessToken);
				
			}
			$customerData = [
				'customer_id' => $customer_id
			];
			$quote_id = $common->getCurl($customerData, $common->api_url().'carts/mine','POST',$accessToken);
			
			$price = $common->getCurl($userData, $common->api_url().'carts/mine/totals','GET',$accessToken);
			
			$cData = array();
			$productArray = array();
			$myCartDetails = $common->getCurl($cData, $common->api_url().'carts/mine/items','GET',$accessToken);
			
			
			if(count($myCartDetails) > 0){
				foreach($myCartDetails as $myCart){
					$productArray['skuArray'][] = $quote_id.','.$myCart->sku.','.$myCart->qty.',1';
				}
			}
			
			// calling api for stock update
			$common->StockUpdate($productArray);
						
			$grandtotal = (isset($price->grand_total) && $price->grand_total != '') ? $price->grand_total : 0;
			$amount = number_format($grandtotal,2);
			$b_address = $common->getCurl($userData, $common->api_url().'customers/me/billingAddress','GET',$accessToken);
			if( !empty($b_address) ) {
				$getId = $b_address->id;
				$getFirstname = $b_address->firstname;
				$getLastname = $b_address->lastname;
				$getStreet1 = (isset($b_address->street[0]) && $b_address->street[0] != '') ? $b_address->street[0] : '';
				$getStreet2 = (isset($b_address->street[1]) && $b_address->street[1] != '') ? $b_address->street[1] : '';
				$getCity = $b_address->city;
				$getCountryId = $b_address->country_id;
				$getRegion = $b_address->region_id;
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
					'region_id' => $getRegion,
					'region' => $getRegion,
					'postcode' => $getPostcode,
					'telephone' => $getTelephone,
					'fax' => '',
					'vat_id' => '',
					//'save_in_address_book' => 1,
					'save_in_address_book' => 0,
					'same_as_billing' => 1
				),
				"useForShipping"=> true
			);
			$b_address = $common->getCurl($billingAddress, $common->api_url().'carts/mine/billing-address','POST',$accessToken);
			
			$billingAddress = array (
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
				'region_id' => $getRegion,
				"region_code"=> $getCountryId,
				'region' => $getRegion,
				'postcode' => $getPostcode,
				'telephone' => $getTelephone,
				'fax' => '',
				'vat_id' => '',
				//'save_in_address_book' => 1,
				'save_in_address_book' => 0,
				'same_as_billing'=> 1
			);
			//$save_my_card = 0;
			if ($save_my_card == 1) {
				$storeValult = "true";
			} else {
				$storeValult = "false";
			}
			
			$methodsandaddress = array (
				'paymentMethod' => 
				array (
					'method' => 'braintree',
					'extension_attributes' => 
					array (
						'agreement_ids' => 
						array (
							0 => '1',
							1 => '3',
						),
					),
					'additional_data' => array( 
						'payment_method_nonce' => $nonce_from_client, 
						'storeInVaultOnSuccess' => $storeValult,//$storeValult
						'store_in_vault' => $storeValult
					),
					
				),
				'billing_address' => $billingAddress,				
			);
			
			//print_r($methodsandaddress); exit;
			
			/*$myfile = fopen("newfile.txt", "a+");
			$txt1 = "\n";
			$txt = json_encode($methodsandaddress);
			
			fwrite($myfile, $txt1.$txt);*/
			
			
			$incrementId = $common->getCurl($methodsandaddress, $common->api_url().'carts/mine/payment-information','POST',$accessToken);
			//print_r($incrementId); exit;
			$orderInvData = array(
				"capture" => true,
				"notify" => false
			);
			
			/*$txt1 = "\n";
			$txt = json_encode($incrementId);
			fwrite($myfile, $txt1.$txt);*/
			
			
			$invoiceId = $common->getCurl($orderInvData, $common->api_url().'order/'.$incrementId.'/invoice','POST',$common->admin_token());	

			/*$txt1 = "\n";
			$txt = json_encode($invoiceId);
			fwrite($myfile, $txt1.$txt);			*/
			
			$oid = $common->getCurl($userData, $common->api_url().'changeorderstatus/'.$incrementId,'GET',$accessToken);
			
			/*$txt1 = "\n";
			$txt = json_encode($oid);
			fwrite($myfile, $txt1.$txt);*/
			
			//$pdfurl = $common->getCurl($userData, $common->api_url().'getcustompdfurl/'.$incrementId,'GET',$accessToken);
			//print_r($oid); exit;
			
			/*$txt1 = "\n";
			$txt = json_encode($pdfurl);
			fwrite($myfile, $txt1.$txt);*/
			
			$orderData = $common->getCurl($userData, $common->api_url().'orders/'.$incrementId,'GET',$common->admin_token());
			if($orderData->entity_id != ''){
				$pdfurl = $common->getCurl($userData, $common->api_url().'getcustompdfurl/'.$orderData->entity_id,'GET',$accessToken);
			} else {
				$pdfurl = '';
			}
			/*$txt1 = "\n";
			$txt = json_encode($orderData);
			fwrite($myfile, $txt1.$txt);*/
			
			$orderArr = array();
			$orderArr['braintree_customer_id'] = isset($orderData->customer_id)?$orderData->customer_id : '';
			$orderArr['token'] = isset($orderData->payment->extension_attributes->vault_payment_token->gateway_token)?$orderData->payment->extension_attributes->vault_payment_token->gateway_token : '';
			$orderArr['transaction_id'] = isset($orderData->payment->cc_trans_id)? $orderData->payment->cc_trans_id : '';
			$orderArr['order_id']= isset($orderData->increment_id) ? $orderData->increment_id : '';
			$orderArr['download_pdf_url'] = $pdfurl;
			$json = '{ "success" : "1", "data" : [' . json_encode ( $orderArr ) . '], "message" : "Your order has been confirmed!!"}';
			/*$txt1 = "======================================================== \n";
			fclose($myfile);*/
			return $this->SendResponse ( $json );
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : "Required parameters missing."}';
			return $this->SendResponse ( $json );
		}
	}
	
	/*
	 * Customer forgot password function
	 * 
	 * request param  string  $email
	 * 
	 * return json response
	 * */
	
	public function getConfirmPurchaseDetails(CommonController $common){
		
		$customer_id = (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '') ? $_REQUEST['customer_id'] : '';
		$accessToken = (isset($_REQUEST['oauth_token']) && $_REQUEST['oauth_token'] != '') ? $_REQUEST['oauth_token'] : '';
		$userData = array();
		$adminAccessToken = $common->admin_token();
		
		
		if(trim($customer_id) != '' && trim($accessToken) != ''){
			
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
				$getId = 0;
				$getFirstname = "";
				$getLastname = "";
				$getStreet1 = "";
				$getStreet2 = "";
				$getCity = "";
				$getCountryId = 0;
				$getRegion = "";
				$getRegionId = 0;
				$getRegionCode = '';
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
			if($billingAddress != 0){
				$b_address = $common->getCurl($billingAddress, $common->api_url().'carts/mine/billing-address','POST',$accessToken);
				//print_r($b_address); exit;
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
			}			
			$cartDetials = $common->getCurl($userData, $common->api_url().'carts/mine','GET',$accessToken);
			
			//print_r($cartDetials); exit;
			
			if($cartDetials->id != ''){
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
			
			
			$cartDetails = $common->getCurl($userData, $common->api_url().'carts/mine/totals','GET',$accessToken);//.(microtime(true)*1000)
			//print_r($cartDetails); exit;
			$cartItems = $common->getCurl($userData, $common->api_url().'carts/mine/items','GET',$accessToken);
			
			$cartArr = array();
			$i = 0;
			//print_r($cartDetails->total_segments); exit;
			/*if(!empty($cartDetails->total_segments)){
				if($cartDetails->total_segments[2]->code == 'fooman_surcharge'){
					$fooman_surcharge = $cartDetails->total_segments[2]->value;
				} else {
					$fooman_surcharge = 0.00;
				}
			}*/
			$fooman_surcharge = 0.00;
			if(!empty($cartDetails->total_segments)){
				foreach($cartDetails->total_segments as $total_segments){
					if($total_segments->code == 'tax'){
						$tax_grandtotal_details = $total_segments->extension_attributes->tax_grandtotal_details;
						if(!empty($tax_grandtotal_details)){
							foreach($tax_grandtotal_details as $tax_grandtotal_detail){
								
								foreach($tax_grandtotal_detail->rates as $rates){
									$taxAmt = $rates->percent;
								}
								//$taxAmt = $tax_grandtotal_detail->rates[0]->percent;
							}
						} else {
							$taxAmt = 0.00;
						}
					}
										
					if(isset($total_segments->code) && $total_segments->code == 'fooman_surcharge'){
						$surchargeItems = $total_segments->extension_attributes->fooman_surcharge_details->items;
						foreach($surchargeItems as $surchargeItem){
							//print_r($surchargeItem);
							$fooman_surcharge = $surchargeItem->amount + $surchargeItem->tax_amount;
						}
						//$fooman_surcharge = $total_segments->value;
					}/* else {
						$fooman_surcharge = 0.00;
					}*/
					
				}
			}
			if(isset($cartDetails->coupon_code)){
				$cartArr['promotion_code'] = $cartDetails->coupon_code;
			} else {
				$cartArr['promotion_code'] = '';
			}
			
			//$cartArr['subtotal'] = (isset($cartDetails->base_subtotal))? number_format($cartDetails->base_subtotal,2): '0.00';
			$cartArr['subtotal'] = (isset($cartDetails->subtotal_incl_tax))? $cartDetails->subtotal_incl_tax : '0.00';
			$cartArr['promotion_discount'] = isset($cartDetails->discount_amount)?str_replace('-','',$cartDetails->discount_amount):'0.00';
			$cartArr['cc_surcharge'] = $fooman_surcharge;
			//$cartArr['cc_surcharge'] = ($cartDetails->base_subtotal * $taxAmt) / 100;
			$cartArr['gst'] = (isset($cartDetails->tax_amount))? $cartDetails->tax_amount : '0.00';
			//$cartArr['discounted_sub_total'] = ($cartDetails->base_subtotal - str_replace('-','',$cartDetails->discount_amount));
			$subtotal_incl_tax = isset($cartDetails->subtotal_incl_tax) ? $cartDetails->subtotal_incl_tax : '0.00';
			$discount_amount = isset($cartDetails->discount_amount) ? $cartDetails->discount_amount : '0.00';
			$cartArr['discounted_sub_total'] = ($subtotal_incl_tax - str_replace('-','',$discount_amount));
			//$cartArr['grand_total'] = $cartDetails->grand_total + $cartArr['cc_surcharge']+$cartDetails->tax_amount;
			//$cartArr['grand_total'] = $cartDetails->base_grand_total + $cartArr['cc_surcharge'];
			//new
			//$cartArr['grand_total'] = isset($cartDetails->base_grand_total) ? $cartDetails->base_grand_total : '0.00';
			$cartArr['grand_total'] = isset($cartDetails->subtotal_incl_tax) ? ($cartDetails->subtotal_incl_tax + $fooman_surcharge) - $discount_amount : '0.00';
			
			$cartArr['newsletter'] = 0;
			
			//print_r($cartArr);
			
			if($cartItems){
				$prdArr = array();
				
				if(isset($cartItems)){
					$prdTmp = $cartItems;
					
					if(count($prdTmp) > 0){
						foreach($prdTmp as $val)
						{
							$cTmp = array();
							$searchCriteria = '?searchCriteria[filter_groups][0][filters][0][field]=sku&searchCriteria[filter_groups][0][filters][0][value]='.trim($val->sku).'&searchCriteria[filter_groups][0][filters][0][condition_type]=eq';
							$productDetails = $common->getCurl($userData, $common->api_url().'products'.$searchCriteria,'GET',$adminAccessToken);
							//print_r($productDetails); exit;
							if($productDetails){
								if(isset($productDetails->items) && count($productDetails->items) > 0){
									$cTmp['name'] = $productDetails->items[0]->name;
									$cTmp['sku']  = $productDetails->items[0]->sku;
									$cTmp['qty']  = $val->qty;
									$cTmp['price'] = $productDetails->items[0]->price;
									$cTmp['product_id'] = $productDetails->items[0]->id;
									
									$customerData1 = [
										'customer_id' => $customer_id
									];
									
									$prodQty = $common->getCurl($customerData1, $common->root_store_url().'apiControl/public/cart/checkQuantity?sku='.$productDetails->items[0]->sku,'GET',$accessToken);
									$totalQty = $prodQty->data[0]->qty;
									if($totalQty > $val->qty){
									} else {
										$cTmp['lowQty'] = 'This product is out of stock';
									}
									
									
									if(isset($productDetails->items[0]->custom_attributes) && count($productDetails->items[0]->custom_attributes) > 0) {
										foreach($productDetails->items[0]->custom_attributes as $valpt) {
											if($valpt->attribute_code == 'image'){
												$cTmp['prod_img'] = $common->store_url().'pub/media/catalog/product'.$valpt->value;
											}
											//print_r($valpt);
											if($valpt->attribute_code == 'special_price'){
												$cTmp['price'] = $valpt->value;
											}
										}
									}
									$cartArr['products'][] = $cTmp;
								}
								//exit;
							}
						}
					}
				}
				
				$json = '{ "success" : "1", "data" : '.json_encode($cartArr).', "message" : ""}';

			} else {
				//$json = '{ "success" : "0", "data" : [], "message" : ""}';
				$json = '{ "success" : "1", "message" : "Your cart is empty"}';
			}
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : "Required parameters missing."}';
		}
		
		return $this->SendResponse ( $json );
		exit;
	}
	
	
	/*
	 * Params
	 * customer_id : customer id for registered users.
	 * Description : Generate customer token from braintree
	 */
	public function getGenerateTokenCustom(CommonController $common) {
		$customer_id = (isset($_REQUEST['customer_id']) && $_REQUEST['customer_id'] != '') ? $_REQUEST['customer_id'] : '';
		$accessToken = (isset($_REQUEST['oauth_token']) && $_REQUEST['oauth_token'] != '') ? $_REQUEST['oauth_token'] : '';
		if($customer_id != ''){
			$userData = array();
			$getToken = $common->getCurl($userData, $common->store_url().'rest/V1/getgeneratetoken/'.$customer_id,'GET',$accessToken);
			$braintreeTokens = json_decode($getToken);
			$genTokenDetails = array();
			$genTokenDetails['customer_id'] = $customer_id;
			$genTokenDetails['braintree_customer_id'] = $braintreeTokens->braintree_customer_id;
			$genTokenDetails['client_token'] = $braintreeTokens->client_token;
			$json = '{ "success" : "1", "data" : [' . json_encode ( $genTokenDetails ) . '], "message" : ""}';
			return $this->SendResponse ( $json );
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : "Customer ID  is missing"}';
			return $this->SendResponse ( $json );
		}
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
