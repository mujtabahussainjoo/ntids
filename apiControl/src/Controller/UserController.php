<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\CommonController;

/*
//use Magento\Framework\App\Bootstrap;
//include('../../app/bootstrap.php');
$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
//print_r($storeManager);
echo $baseUrl= $storeManager->getStore()->getBaseUrl(); 
*/
class UserController
{
    public function __construct(){
    	
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
		/*
		$myfile = fopen("newfile.txt", "a");
		$txt1 = "\n";
		$txt = json_encode($_REQUEST);
		
		fwrite($myfile, $txt1.$txt);
		fclose($myfile);*/
		
		$logindata = array();
		$logindata['username'] = (isset($_REQUEST['email']) && $_REQUEST['email'] != '') ? $_REQUEST['email'] : '';
		$logindata['password'] = (isset($_REQUEST['password']) && $_REQUEST['password'] != '') ? $_REQUEST['password'] : '';
		
		$accessToken = $common->getToken($logindata, $common->api_url().'integration/customer/token/');
		
			if(!isset($accessToken->trace)){
				$userData = array();
				$customerDetails = $common->getCurl($userData, $common->api_url().'customers/me/','GET',$accessToken);
				
				$getToken = $common->getCurl($userData, $common->store_url().'rest/V1/getgeneratetoken/'.$customerDetails->id,'GET',$accessToken);
				
				
				$customer_data = array();
				$customer_data['id'] = $customerDetails->id;
				$customer_data['first_name'] = $customerDetails->firstname;
				$customer_data['last_name'] = $customerDetails->lastname;
				$customer_data['email'] = $customerDetails->email;
				if($customerDetails->id != ''){
					$braintreeTokens = json_decode($getToken);
					$customer_data['braintree_customer_id'] = $braintreeTokens->braintree_customer_id;
					$customer_data['client_token'] = $braintreeTokens->client_token;
				}
				
				
			} else {
				$json = '{ "success" : "0", "data" : [], "message" : "'.$accessToken->message.'"}';
				return $this->SendResponse ( $json );
			}
		
		if(!empty($customer_data)){
			$json = '{ "success" : "1","oauth_token": "'.$accessToken.'", "oauth_token_secret": "'.$accessToken.'", "data" : ['.json_encode($customer_data).'], "message" : "You are Logged In."}';
		} else {
			//$json = '{ "success" : "0", "data" : [], "message" : "Invalid data."}';
			$json = '{ "success" : "0", "data" : [], "message" : "The account sign-in was incorrect or your account is disabled temporarily. Please wait and try again later."}';
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
	public function userForgotPassword(CommonController $common){
		$frgtpwdata = array();		
		//$frgtpwdata['email'] = $_REQUEST['email'];
		$frgtpwdata['email'] = (isset($_REQUEST['email']) && $_REQUEST['email'] != '') ? $_REQUEST['email'] : '';
		$frgtpwdata['template'] = 'email_reset';
		$frgtpwdata['websiteId'] = 1;
		
		$data = $common->getCurl($frgtpwdata, $common->api_url().'customers/password','PUT');
		if($data){
			$json = '{ "success" : "1", "data" : [], "message" : "Reset confirmation sent to your mail id"}';
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : "Invalid data."}';
		}
		return $this->SendResponse ( $json );
	}
	
	/*
	 * Customer user change password function
	 * 
	 * request param  string  $old_password
	 * request param  string  $new_password
	 * 
	 * return json response
	 * */
	public function userChangePassword(CommonController $common){
		$chgpwdata = array();
		
		$chgpwdata['currentPassword'] = (isset($_REQUEST['old_password']) && $_REQUEST['old_password'] != '') ? $_REQUEST['old_password'] : '';
		$chgpwdata['newPassword'] = (isset($_REQUEST['new_password']) && $_REQUEST['new_password'] != '') ? $_REQUEST['new_password'] : '';
		/*
		$chgpwdata['currentPassword'] = $_REQUEST['old_password'];
		$chgpwdata['newPassword'] = $_REQUEST['new_password'];*/
		$accessToken = $_REQUEST['oauth_token'];
		
		$data = $common->getCurl($chgpwdata, $common->api_url().'customers/me/password','PUT',$accessToken);
		if($data){
			$json = '{ "success" : "1", "data" : ['.$data.'], "message" : "Your Password has been Changed Successfully."}';
		} else {
			$json = '{ "success" : "0", "data" : ['.$data.'], "message" : "Invalid data."}';
		}
		return $this->SendResponse ( $json );
	}

	public function logout(CommonController $common){
		$customerArray = array();
		
		$accessToken = $_REQUEST['oauth_token'];
		//$customerArray['customerId'] = $_REQUEST['customerId'];
		$customerDetails = $common->getCurl($customerArray, $common->api_url().'customers/me/','GET',$accessToken);
		
		$customerArray['customerId'] = $customerDetails->id;
		//$customerArray['customerId'] = $_REQUEST['customer_id'];
		$data = $common->getCurl($customerArray, $common->api_url().'integration/customer/revoke','POST',$accessToken);
		if($data){
			//$json = '{ "success" : "1", "data" : ['.json_encode($data).'], "message" : "Logged out successfully."}';
			$json = '{ "success" : "1", "data" : [], "message" : "Logged out successfully."}';
		} else {
			//$json = '{ "success" : "0", "data" : ['.json_encode($data).'], "message" : "Invalid data."}';
			$json = '{ "success" : "0", "data" : [], "message" : "Invalid data."}';
		}
		return $this->SendResponse( $json );
	}
	
	public function deviceARNId(CommonController $common){
		
		$json = '{ "success" : "0", "data" : [], "message" : ""}';
		return $this->SendResponse( $json );
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
