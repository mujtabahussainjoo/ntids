<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
header('Access-Control-Allow-Methods:  POST, GET, PUT, DELETE');  //OPTIONS,

class CommonController
{
	public $storename = 'neattickets';
	
	public function root_store_url()
	{
		return 'https://neattickets.neatideas.com.au/';
	}
	
	public function parent_menuid()
	{
		return 189;
	}
	
	public function api_url()
	{
		return $this->root_store_url().$this->storename.'/rest/'.$this->storename.'/V1/';
	}
	
	public function store_url()
	{
		return $this->root_store_url().$this->storename.'/';
	}
	
	public function admin_token()
	{
		//return 'jm42lxliq3edyi3cl7j7nyedf0leb5q4';
		return 'xkk3rt9e2qums13f3venbt57vsovmnt5';
	}
	
	public function getToken($postdata,$apiurl,$method='POST',$accesstoken=''){
		
		$curlurl= $apiurl;

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $curlurl,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => json_encode($postdata),
			CURLOPT_HTTPHEADER => array(
				"content-type: application/json"
			),
		));

		$response = curl_exec($curl);
		
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
			$json = '{"cURL Error #:" . '.$err.'}';
			return '';
		} else {
			return json_decode($response);
		}
		//return $this->SendResponse ( $json );
	}
	
	public function getCurl($postdata,$apiurl,$method='POST',$accesstoken=''){
		
		$curlurl= $apiurl;

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $curlurl,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_POSTFIELDS => json_encode($postdata),
			CURLOPT_HTTPHEADER => array(
				"content-type: application/json",
				"Authorization: Bearer ".$accesstoken.""
			),
		));

		$response = curl_exec($curl);
		
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) {
			$json = '{"cURL Error #:" . '.$err.'}';
			return json_decode($json);
		} else {
			return json_decode($response);
		}
		//return $this->SendResponse ( $json );
	}
	
	public function StockUpdate($skuarr){
		
		$soapUrl = "http://13.236.249.168:8082/NiWebService.asmx?wsdl"; // asmx URL of WSDL
        $soapaction = "http://13.236.249.168:8082/NiWebService.asmx?wsdl"; 
        
        $xml_post_string = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:tem="http://tempuri.org/">
        <soap:Body>
            <tem:StockUpdate>
                <!--Optional:-->
                <tem:arrayPara>
                    <!--Zero or more repetitions:-->';
                    foreach($skuarr['skuArray'] as $skyitm ){
						$xml_post_string .= '<tem:string>'.$skyitm.'</tem:string>';
					}
                    
                $xml_post_string .= '</tem:arrayPara>
                <!--Optional:-->
                <tem:strEmail>ericyap@acsolv.com</tem:strEmail>
                <!--Optional:-->
                <tem:strPassword>642udgO</tem:strPassword>
            </tem:StockUpdate>
        </soap:Body>
        </soap:Envelope>
        '; 
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
		
		if($response != ''){
			$json = '{ "success" : "1", "data" : [], "message" : "Success"}';
		} else {
			$json = '{ "success" : "0", "data" : [], "message" : "Invalid data."}';
		}
		return true;
		//return $this->SendResponse ( $json );
	}
}
