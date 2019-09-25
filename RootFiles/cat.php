<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    use Zend_Barcode;

    use \Magento\Framework\App\Bootstrap;
    include('app/bootstrap.php');
    $bootstrap = Bootstrap::create(BP, $_SERVER);
    $objectManager = $bootstrap->getObjectManager();
    $url = \Magento\Framework\App\ObjectManager::getInstance();
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	$CategoryLinkRepository = $objectManager->get('\Magento\Catalog\Api\CategoryLinkManagementInterface');
	$category_ids = array('41','102');
	$sku = 'WLDCATSLEGO';
	$CategoryLinkRepository->assignProductToCategories($sku, $category_ids);
?>