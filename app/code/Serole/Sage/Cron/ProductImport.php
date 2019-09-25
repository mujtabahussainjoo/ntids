<?php
namespace Serole\Sage\Cron;

class ProductImport
{

    protected $_logger;
	
	protected $_itemsImport;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(\Serole\Sage\Model\Itemimport $itemimport)
    {
		
		$this->_itemsImport = $itemimport;
		
		$this->createLog('sage_ImportItems.log');
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $this->_logger->info("Cronjob ProductImport is started.");
		
		$this->_itemsImport->getItemsFromSage();
		
		$this->_logger->info("Cronjob ProductImport is ended.");

    }
	
	public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}
}
