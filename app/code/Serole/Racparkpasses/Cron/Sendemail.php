<?php
namespace Serole\Racparkpasses\Cron;
 
class Sendemail
{
	protected $logger;
	
	protected $_layout;
	
	protected $_transportBuilder;
	
	protected $_storeConfig;
	
	protected $_dir;
 
	public function __construct(
		\Psr\Log\LoggerInterface $loggerInterface,
        \Magento\Framework\View\LayoutInterface $layout,
		\Serole\Pdf\Model\Mail\TransportBuilder $transportBuilder,
		\Magento\Framework\App\Config\ScopeConfigInterface $storeConfig,
		\Magento\Framework\Filesystem\DirectoryList $dir
	) {
		$this->_layout = $layout;
		$this->_transportBuilder = $transportBuilder;
		$this->logger = $loggerInterface;
		$this->_storeConfig  = $storeConfig;
		$this->_dir = $dir;
	}
 
	public function execute() {

		//test command line
        //php bin/magento cron:run --group="serole_racparkpasses_cron_group"
		//$this->logger->debug('Serole\Racparkpasses\Cron\Sendemail');
		
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/parkpass-sendemail.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
		
		$report_title		= "RAC Park Passes";
		$report_filename	= "RAC_Park_Passes.csv";		
		$to_emails = array('dhananjay.kumar@serole.com');
		$to_names = array('Dhananjay');

		$grid = $this->_layout->createBlock('Serole\Racparkpasses\Block\Adminhtml\Racparkpass\Grid');
		
		$fromDate = strtotime("first day of previous month");
		$toDate = strtotime("first day of this month");		
			
		
		$grid->setData('fromDate',date('Y-m-d',$fromDate)); 
		$grid->setData('toDate',date('Y-m-d',$toDate));		
		
		$range = date('d/m/Y',$fromDate)
					.'-'.
				date('d/m/Y',strtotime("-1 day",$toDate));	
		
		$report_title .=' - updated during '.$range;
		
		$report_csv_file = $grid->getCsvFile();
		
		$emailTemplateVariables = array(
					'report_title'	=> $report_title,
									
		);	

         $this->_transportBuilder->setTemplateIdentifier(10)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => 0,
                ]
            )
            ->setTemplateVars($emailTemplateVariables)
            ->setFrom([
                'name' => 'Neat Ideas',
                'email' => $this->getStoreEmail(),
            ])
            ->addTo($to_emails,$to_names)
             ->addAttachment(file_get_contents($this->_dir->getPath('var')."/".$report_csv_file['value']),$report_filename,$fileType='application/csv'); //Attachment goes here.
        try {
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
            $this->_inlineTranslation->resume();
			 $logger->info("Email Sent");
        } catch (\Exception $e) {
            $logger->info("Email Attachment issue".$e->getMessage());
        }		

	}
	
	public function getStoreEmail(){
        return $this->_storeConfig->getValue(
            'trans_email/ident_sales/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}