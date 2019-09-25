<?php

  namespace Serole\Westbenefits\Observer;


  use Magento\Framework\Event\Observer;

  class Westbenefits implements \Magento\Framework\Event\ObserverInterface{

      protected $customerSession;

      public function __construct(\Magento\Customer\Model\Session $customerSession)  {

          $this->customerSession = $customerSession;

      }
      public function execute(Observer $observer){

          $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Westbenefits-observer-execute.log');
          $logger = new \Zend\Log\Logger();
          $logger->addWriter($writer);

          echo $email = $this->customerSession->getCustomer()->getEmail();
exit;
          if ($email == 'patel.iec@gmail.com'){
              $logger->info($email.' - Hello DK :) ');
              return 'Y';
          }
          $success = 'N';
          $fields = array('emailAddress' => urlencode($email));
          $fields_string = '';

          foreach($fields as $key=>$value) {
              $fields_string .= $key.'='.$value.'&';
          }
          rtrim($fields_string, '&');
          $username='benefits';
          $password='6F8CPstbHRfxkXs5CMaZ';
          $URL='https://ssapi.wanews.com.au/v1/benefits/subscriber/exists';

          $logger->info($email.' - URL '.$URL);
          $logger->info($email.' - fields '.$fields_string);

          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL,$URL);
          curl_setopt($ch, CURLOPT_TIMEOUT, 15); //timeout after 30 seconds
          curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
          curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
          curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
          curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
          curl_setopt($ch, CURLOPT_HEADER, 0);
          curl_setopt($ch, CURLOPT_POST, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

          try {
              $result=curl_exec($ch);
              if ($result){
                  $logger->info($email.' - http result ='.$result);
              } else {
                  $logger->info($email.' - http result = '.curl_error($ch));
              }
              $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
              $logger->info($email.' - http status code='.$status_code);

              // SUCCESS! - well .. nearly
              if ($status_code == 200 || $status_code == 404 ){
                  $logger->info($email.' - decoding ', null, 'westbenefits-gigyaRaasPostLogin.log');
                  if ( strpos($result, '"status":')===false){
                      $success = 'E';
                  } else if ( strpos($result, '"status":"active"')===false){
                      $success = 'N';
                  } else {
                      $success = 'Y';   // WooHoo!
                  }
              } else {
                  $success = 'E';
              }
          } catch (Exception $e){
              $logger->info($email.' - exception thrown '.$e->getMessage());
              $success = 'E';
          }

          curl_close ($ch);
          $logger->info($email.' - result is '.$success);
          if ($success == 'Y'){
              $logger->info($email.' - Customer IS Subscribed');
          } else if ($success == 'E'){
              $logger->info($email.' - Unable to check subscription');
          } else {
              $logger->info($email.' - Customer IS NOT Subscribed');
          }
          $this->customerSession->setData('isSubscribed',$success);
      }
  }