<?php
namespace Serole\Sage\Cron;

class PriceImport
{

    protected $_logger;
	
	protected $_priceImport;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(\Serole\Sage\Model\Priceimport $priceimport)
    {
		
		$this->_priceImport = $priceimport;
		
		$this->createLog('sage_PriceItems.log');
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $this->_logger->info("Cronjob PriceImport is started.");
		
		$this->_priceImport->getPriceFromSage();
		
		$this->_logger->info("Cronjob PriceImport is ended.");

    }
	
	public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}
}
