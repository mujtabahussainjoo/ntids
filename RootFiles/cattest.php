<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    use Zend_Barcode;

    use \Magento\Framework\App\Bootstrap;
    include('app/bootstrap.php');
    $bootstrap = Bootstrap::create(BP, $_SERVER);
    $objectManager = $bootstrap->getObjectManager();
	
	$CategoryLinkRepository = $objectManager->get('\Magento\Catalog\Api\CategoryLinkManagementInterface');
	$objectManager->get('Magento\Framework\App\State')->setAreaCode('frontend');
	$category_ids =array(63);
	$skus = array('HLUXMSPEC','HSTNPDF','HCNPDF','HANPDF','HCNM','HANM','HSTNM','HLPDF','HPOPDRINKCTPDF','HCBPOPDRINKPDF','HANOPPDF','HCBCT','HCBCT');
	foreach($skus as $sku){
	  $CategoryLinkRepository->assignProductToCategories($sku, $category_ids);
	}


?>=COUNTIF(A:A, A2)>1