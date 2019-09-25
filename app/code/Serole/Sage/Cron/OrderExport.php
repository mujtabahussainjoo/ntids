<?php


namespace Serole\Sage\Cron;

class OrderExport
{

    protected $_logger;
	
	protected $_orderexport;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(\Serole\Sage\Model\Exportorder $Exportorder)
    {	
		$this->_orderexport = $Exportorder;
		
		$this->createLog('sage_orderexport_cron.log');
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $this->_logger->info("Cronjob orderExport is started.");
		
		$date = date('Y-m-d');
		
		$this->_orderexport->getMagentoOrders($date);
		
		$this->_logger->info("Cronjob orderExport is ended.");
    }
	
	public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}
}
