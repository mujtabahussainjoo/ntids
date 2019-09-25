<?php

namespace Serole\Subscriber\Cron;
 
class Subscriberlist
{
	protected $logger;
	protected $_storeManager;
	public function __construct(
		\Psr\Log\LoggerInterface $loggerInterface,
		\Magento\Store\Model\StoreManagerInterface $storeManager
	) {
		$this->_storeManager = $storeManager;
		$this->logger = $loggerInterface;
	}
	
 	public function saveReport(){
		
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/AAAtest.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$objDate = $objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
		$currentDate = $objDate->gmtDate();
		$currentDate = date_format(date_create($currentDate), 'd-m-y');
		$fileName='subscriber_'.$currentDate.'.csv';
		$csvFile = '/var/www/html/var/subscriber/'.$fileName; 	
		$file = fopen($csvFile, 'w');
		fputcsv($file, array('Date/Time','Member number','First name','Last name','Phone number','Email address','Address','Suburb','Postcode','State','Marketing consent check','Order number'));
		$subscribers=$objectManager->create('\Serole\Subscriber\Model\Subscriber')->getCollection();
		$data = $subscribers->getData();
		$logger->info($data);
		// save each row of the data
		$csvarray =array();
		foreach ($data as $row){
			$csvarray[0] = $row['created_at'];
			$csvarray[1] = $row['customer_member'];
			$csvarray[2] = $row['customer_first_name'];
			$csvarray[3] = $row['customer_last_name'];
			$csvarray[4] = $row['customer_phno'];
			$csvarray[5] = $row['customer_email'];
			$csvarray[6] = $row['customer_address'];
			$csvarray[7] = $row['suburb'];
			$csvarray[8] = $row['customer_postcode'];
			$csvarray[9] = $row['customer_state'];
			$csvarray[10] = $row['opt_in'];
			$csvarray[11] = $row['order_id'];
			fputcsv($file, $csvarray);
		}
		// Close the file
		fclose($file);
		$this->sendMailReport();
		$this->sendSftp();
	}
	
	public function sendMailReport(){
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
		$status = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('subscriber/subscriber/status',$storeScope);
		$mailTo = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('subscriber/subscriber/mailto',$storeScope);
		$mailFrom = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('subscriber/subscriber/mailfrom',$storeScope);
		$objDate = $objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
		$currentDate = $objDate->gmtDate();
		$currentDate = date_format(date_create($currentDate), 'd-m-y');
		$fileName='subscriber_'.$currentDate.'.csv';
		$csvFile = '/var/www/html/var/subscriber/'.$fileName; 
		$transportBuilder = $objectManager->get('\Magento\Framework\Mail\Template\TransportBuilder');
		$link = "Store Link";
		$subject = "Reports";
		$vars = array(
						'subscriber_name'   => "Xyz",
						'subscriber_mailid'	=> "xyz@gmail.com"
					);
		$transport = $transportBuilder->setTemplateIdentifier('82')
							->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND,'store' =>$this->_storeManager->   getStore()->getId(),])
							->setTemplateVars($vars)
							->setFrom(array('email' =>$mailFrom,'name' => 'Report'))
							->addTo($mailTo,'Mujtaba');
							if(file_exists($csvFile)){
							 $transportBuilder->addAttachment(file_get_contents($csvFile),$fileName,$fileType='application/csv'); 
							}

		$transport = $transport->getTransport();
		if($status!=0&&$mailTo!=''){
			$transport->sendMessage();
		}
	}

	public function sendSftp() { 
	
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
		$status = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('subscriber/subscriber/status',$storeScope);
		$host = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('subscriber/subscriber/host',$storeScope);
		$username = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('subscriber/subscriber/username',$storeScope);
		$password = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('subscriber/subscriber/password',$storeScope);
		$port = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('subscriber/subscriber/port',$storeScope);
		$objDate = $objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
		$currentDate = $objDate->gmtDate();
		$currentDate = date_format(date_create($currentDate), 'd-m-y');
		$fileName='subscriber_'.$currentDate.'.csv';
		$localfilename = "/var/www/html/var/subscriber/$fileName"; 
		$csvFile = 	"/var/www/html/var/subscriber/$fileName"; 
		$remotefilename="/subscriber/$fileName";
		$sftp = $objectManager->create('\Magento\Framework\Filesystem\Io\Ftp');
		echo $host='207.180.228.122';
		$username='zuba@zubacrafts.com';
		$password='mujtaba#1989';
		try{
			$sftp->open(
				array(
					'host'      => $host,
					'username'  => $username,
					'password'  => $password,
					'port'		=> '21'
				)
			);
			$sftp->write($remotefilename,$localfilename);
			$sftp->close();
			

		// $connection = ftp_connect("207.180.228.122");
		// $login = ftp_login($connection,"zuba@zubacrafts.com","mujtaba#1989");
		// if (!$connection)
				// {
				 // die('Connection attempt failed!');
				// }
			// else{
				 // echo "connection passed";
				 // }
		// if (!$login)
				// {
				 // die('Login attempt failed!');
				// }
				// else{
				// echo "login passed";
				// }
		// ftp_pasv($connection, true);

		}catch(Exception $e){
			echo $e->getMessage();
		}
	
	}
	public function execute() {
		$this->saveReport();
		$this->sendSftp();
		//test command line
        //php bin/magento cron:run --group="serole_subscriber_cron_group"
		$this->logger->debug('Serole\Subscriber\Cron\Subscriberlist');

	}
}