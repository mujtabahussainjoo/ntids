<?php
/*
$request['OrderReferenceNumber'] = "dktest";
$request['Recipient']['Email'] = "dhananjay@serole.com";
$request['Recipient']['FirstName'] = "";
$request['Recipient']['LastName'] = "";
$request['Recipient']['MobileNumber'] = "";
$request['Recipient']['PhoneNumber'] = "";
$request['DigitalStoreCardOrder']['PersonalisationInfo']['To'] = "";
$request['DigitalStoreCardOrder']['PersonalisationInfo']['Message'] = "";
$request['DigitalStoreCardOrder']['PersonalisationInfo']['From'] = "";
$request['DigitalStoreCardOrder']['OrderItems']['From'] = "";
*/

$x ='{"OrderReferenceNumber":210000000056,"Recipient":{"Email":"admin@neatideas.com","FirstName":"","LastName":"","MobileNumber":"","PhoneNumber":""},"DigitalStoreCardOrder":{"PersonalisationInfo":{"To":"","Message":"","From":""},"OrderItems":[{"Quantity":1,"Amount":"45.4500","SKU":"dg1","ReferenceNumber":210000000056}]}}';

//echo "aaa";
echo "<pre>";
print_r(json_decode($x));
$make_call = callAPI('POST', 'https://deis.dguat.com/Api/V1/Order', $x);
$response = json_decode($make_call, true);
echo "<pre>";
print_r($response);

function callAPI($method, $url, $data){
   $curl = curl_init();

   switch ($method){
      case "POST":
         curl_setopt($curl, CURLOPT_POST, 1);
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
         break;
      case "PUT":
         curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
         if ($data)
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
         break;
      default:
         if ($data)
            $url = sprintf("%s?%s", $url, http_build_query($data));
   }

   // OPTIONS:
   curl_setopt($curl, CURLOPT_URL, $url);
   $headers = array(
    'Content-Type:application/json',
    'Authorization: Basic '. base64_encode("NTDAPIUAT:Jiu4wK[.") 
   );
   curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

   // EXECUTE:
   $result = curl_exec($curl);
   if(!$result){die("Connection Failure");}
   curl_close($curl);
   return $result;
}
?>