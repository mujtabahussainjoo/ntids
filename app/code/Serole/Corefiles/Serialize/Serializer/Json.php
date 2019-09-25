<?php

  namespace Serole\Corefiles\Serialize\Serializer;

  class Json extends \Magento\Framework\Serialize\Serializer\Json{

      public function unserialize($string)
      {
          $result = json_decode($string, true);
          if (json_last_error() !== JSON_ERROR_NONE) {
              if(strstr($string, '\\')){
                  $result = json_decode(stripslashes($string), true);
                  if (json_last_error() !== JSON_ERROR_NONE) {
                      throw new \InvalidArgumentException("Unable to unserialize value. Error: " . json_last_error_msg());
                  }
              }else{
                  throw new \InvalidArgumentException("Unable to unserialize value. Error: " . json_last_error_msg());

              }
          }
          return $result;
      }
  }