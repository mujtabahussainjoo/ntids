<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\CommonController;

class OrderController
{
    public function __construct(){
    	
    }
	
	public function getUserPurchase(CommonController $common) {
		$customer_id = isset($_REQUEST['customer_id'])?$_REQUEST['customer_id']:'';
		$accessToken = isset($_REQUEST['oauth_token'])?$_REQUEST['oauth_token']:'';
        $chgpwdata = array();
        $data = $common->getCurl($chgpwdata, $common->api_url().'orders?searchCriteria[filterGroups][][filters][][field]=customer_id&searchCriteria[filterGroups][0][filters][0][value]='.$customer_id,'GET',$common->admin_token());
        $orderDetails = array();
		$i = 0;
		for($i=0;$i<count($data->items);$i++){
			for($j=0;$j<count($data->items[$i]->items);$j++){
			$orderDetails[$i]['order_id'] = $data->items[$i]->items[$j]->order_id;
			}
			$orderDetails[$i]['increment_id'] = $data->items[$i]->increment_id;
			$orderDetails[$i]['order_date'] = $data->items[$i]->created_at;
			$orderDetails[$i]['base_total'] = $data->items[$i]->base_subtotal;
			$orderDetails[$i]['grand_total'] = $data->items[$i]->grand_total;
			
		}
		
		if(!empty($orderDetails)){
			$orderCount = count ( $orderDetails );
			//$orders = json_encode ( $orderDetails );
			$json = '{ "success" : "1","orderCount" : ' . $orderCount . ',"oauth_token" : "'.$accessToken.'", "data" : '.json_encode($orderDetails).', "message" : "List of orders"}';
		}
		
		
		else {
			$json = '{ "success" : "0", "data" : [], "message" : "Invalid data."}';
		}
		return $this->SendResponse ( $json );
	}

	public function getUserPurchaseDetails(CommonController $common) {
		$increment_id = isset($_REQUEST['increment_id'])?$_REQUEST['increment_id']:'';
		$oauth_token = isset($_REQUEST['oauth_token'])?$_REQUEST['oauth_token']:'';
        $chgpwdata = array();
        $data = $common->getCurl($chgpwdata, $common->api_url().'orders?searchCriteria[filterGroups][][filters][][field]=increment_id&searchCriteria[filterGroups][0][filters][0][value]='.$increment_id,'GET',$common->admin_token());
        $orderDetails = array();
		
		for($i=0;$i<count($data->items);$i++){
			$orderDetails['items'] = array();
			
			foreach($data->items[$i]->extension_attributes->fooman_total_group->items as $taxamt){
				if($taxamt->code == 'fooman_surcharge'){
					$surcharge = $taxamt->amount;
					$surchargetax = $taxamt->tax_amount;
				} else {
					$surcharge = $surchargetax = '';
				}
			}
			
			for($j=0;$j<count($data->items[$i]->items);$j++){
				$orderItems = array();
				$orderItems['product_id'] = $data->items[$i]->items[$j]->product_id;
				$orderItems['name'] = $data->items[$i]->items[$j]->name;
				$orderItems['prod_img'] = "";
				$orderItems['sku'] = $data->items[$i]->items[$j]->sku;
				$orderItems['qty'] = $data->items[$i]->items[$j]->qty_ordered;
				$orderItems['price'] = $data->items[$i]->items[$j]->price;
				$orderDetails['items'][] = $orderItems;
			}
			$orderDetails['grand_total'] = $data->items[$i]->grand_total;
			$orderDetails['subtotal'] = $data->items[$i]->subtotal;
			$orderDetails['shipping_amount'] = $data->items[$i]->shipping_amount;
			$orderDetails['discount_amount'] = $data->items[$i]->discount_amount;
			$orderDetails['tax_amount'] = $data->items[$i]->tax_amount;
			$orderDetails['discount_description'] = "";
			$orderDetails['surcharge_amount'] = (isset($surcharge) && $surcharge != '') ? $surcharge : 0.0000;
			$orderDetails['surcharge_tax_amount'] = (isset($surchargetax) && $surchargetax != '') ? $surchargetax : 0.0000;
			$orderDetails['surcharge_description'] = null;

		}
		if(!empty($orderDetails)){
			$orderCount = count ( $orderDetails );
			$json = '{ "success" : "1","oauth_token" : "'.$oauth_token.'", "data" : '.json_encode($orderDetails).', "message" : "Order details"}';
		}
		
		
		else {
			$json = '{ "success" : "0", "data" : [], "message" : "Invalid data."}';
		}
		return $this->SendResponse ( $json );
	}
    
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
