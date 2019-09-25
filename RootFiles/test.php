<?php

$dataSycdate = date('Y-m-d H:i:s');
$to      = 'dhananjay.kumar@serole.com';
$subject = 'Data Sync Process Start -'.$dataSycdate;
$message = 'Data Sync Process Start -'.$dataSycdate;
$headers = 'From: iamramesh.a@gmail.com' . "\r\n" .
           'Reply-To: iamramesh.a@gmail.com' . "\r\n";
mail($to, $subject, $message, $headers);


/*
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    ini_set('memory_limit', '6G');

    require '/var/www/html/lib/gearpdf/vendor/autoload.php';
    require '/var/www/html/lib/gearpdf/vendor/H2OpenXML/sourcecode/HTMLtoOpenXML.php';
    use Zend_Barcode;
*/

    //use \PhpOffice\PhpWord\TemplateProcessor;
/*
    use Braintree_Transaction;

    use \Magento\Framework\App\Bootstrap;
    include('app/bootstrap.php');
    $bootstrap = Bootstrap::create(BP, $_SERVER);
    $objectManager = $bootstrap->getObjectManager();
    $url = \Magento\Framework\App\ObjectManager::getInstance();
    $storeManager = $url->get('\Magento\Store\Model\StoreManagerInterface');
    $mediaurl= $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    $state = $objectManager->get('\Magento\Framework\App\State');
    $state->setAreaCode('frontend');


    $orderSerialCodeObj = $objectManager->create('\Serole\Serialcode\Model\OrderitemSerialcode')->getCollection();
    $orderSerialCodeObj->addFieldToFilter('OrderID', 21000314991);
    $orderSerialCodeObj->addFieldToFilter('status', 1);
    echo "<pre>"; print_r($orderSerialCodeObj->getData());
	
			
		$htt = '<ul>
				 <li>Test1</li>
                 <li>Test1</li>
                 <li>Test1</li>
				<li>Test1</li>
				<li>Test1</li>
				<li>Test1</li>
				<li>Test1</li>				 
			   </ul>';

		$toOpenXML = HTMLtoOpenXML::getInstance()->fromHTML($htt);
		//echo "<pre>"; print_r($toOpenXML);

		$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('test.docx');
		$templateProcessor->setValue('data', $toOpenXML);
		$templateProcessor->saveAs('only-final.docx');
		exit;
*/
	
	

?>