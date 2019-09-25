<?php
namespace Serole\Sage\Cron;

class BundleImport
{

    protected $_logger;
	
	protected $_bundleimport;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(\Serole\Sage\Model\Bundleimport $bundleimport)
    {
		
		$this->_bundleimport = $bundleimport;
		
		$this->createLog('sage_BundleItems.log');
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $this->_logger->info("Bundle Product Import is started.");
		
		$this->_bundleimport->getItemsFromSage();
		
		$this->_logger->info("Bundle Product Import is ended.");

    }
	
	public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}
}
