<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    ini_set('memory_limit', '6G');

     use \Magento\Framework\App\Bootstrap;
    include('app/bootstrap.php');
    $bootstrap = Bootstrap::create(BP, $_SERVER);
    $objectManager = $bootstrap->getObjectManager();
    $url = \Magento\Framework\App\ObjectManager::getInstance();
    $storeManager = $url->get('\Magento\Store\Model\StoreManagerInterface');
    $mediaurl= $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    $state = $objectManager->get('\Magento\Framework\App\State');
    $state->setAreaCode('frontend');

   $orderSerialCodeObj = $objectManager->create('\Serole\Vendoremail\Cron\Order')->execute();
  // $subscriber = $objectManager->create('\Serole\Subscriber\Cron\Subscriberlist')->execute();
?>