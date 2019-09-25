<?php


namespace Serole\Sage\Cron;

class ProcessingOrders
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
		
		$this->createLog('sage_processingOrderExport_cron.log');
    }

    /**
     * Execute the cron
     *
     * @return void
     */
     public function execute()
    {
        $this->_logger->info("Cronjob processingOrderExport is started.");
		
		$this->_orderexport->processProcessingOrders();
		
		$this->_logger->info("Cronjob processingOrderExport is ended.");
    }
	
	public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}
}
