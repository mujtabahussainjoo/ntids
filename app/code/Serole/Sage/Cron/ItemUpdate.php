<?php
namespace Serole\Sage\Cron;

class ItemUpdate
{

    protected $_logger;
	
	protected $_itemUpdate;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(\Serole\Sage\Model\Itemupdate $Itemupdate)
    {
		
		$this->_itemUpdate = $Itemupdate;
		
		$this->createLog('sage_ItemUpdate.log');
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $this->_logger->info("Cronjob ItemUpdate is started.");
		
		$this->_itemUpdate->getUpdatedItems();
		
		$this->_logger->info("Cronjob ItemUpdate is ended.");

    }
	
	public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}
}
