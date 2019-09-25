<?php

namespace Serole\Itemsales\Cron;

class SendMonthyReport{

    protected $helperData;

    protected $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectmanager,
                                \Serole\HelpData\Helper\Data $helperData){
        $this->helperData = $helperData;
        $this->objectManager = $objectmanager;
    }

    public function execute(){
        try {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cron-itemsales-sendmonthly-report.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);

            $d = new \DateTime();
            $toDate = $d->format('Y-m-01');
            $d->modify('-1 MONTH');
            $fromDate = $d->format('Y-m-01');

            $resource = $this->objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();

            $sql = "SELECT Date_format(ord.created_at + HOUR( 8 ),'%d-%m-%Y %h:%i %p') , 
					ord.store_name, 
					itm.name, 
					itm.sku, 
					SUM( itm.qty_invoiced ) , 
					SUM( itm.qty_refunded ) 
				FROM sales_order_item itm
				JOIN sales_order ord 
					ON ord.entity_id = itm.ordeR_id
				WHERE ord.status = 'complete'
					AND ord.created_at >= ( DATE( '".$fromDate."' ) - HOUR( 8 ) ) 
					AND ord.created_at < ( DATE( '".$toDate."' ) - HOUR( 8 ) ) 
				GROUP BY DATE( ord.created_at ) , ord.store_name, itm.name, itm.sku
				ORDER BY itm.sku, ord.created_at, ord.store_name";

            $today = new \DateTime();
            $fileName = "MonthlyProductInvoicedRefunded-".$today->format('Y-m-d').".csv";
            $reportsFolderName = $this->helperData->getMediaBaseDir().'/montlyreports/';

            if(!file_exists($reportsFolderName)){
                mkdir($reportsFolderName,0777,true);
                chmod($reportsFolderName,0777);
            }

            if(!is_writable($reportsFolderName)){
                chmod($reportsFolderName,0777);
            }

            $outFile = $reportsFolderName.$fileName;
            $fp = fopen($outFile, 'w');

            $row = array('Created At',
                'Store Name',
                'Product Name',
                'Product SKU',
                'Total Invoiced',
                'Total Refunded');
            fputcsv($fp, $row);

            $items = $connection->query($sql);
            while ($row = $items->fetch()) {
                fputcsv($fp, $row);
            }
            fclose($fp);

            $senderEmail = $this->helperData->getStoreEmail('trans_email/ident_sales/email');
            $senderName = $this->helperData->getStoreEmail('trans_email/ident_sales/name');

            $templateParams =  array();

            $transportBuilder = $this->objectManager->create('\Serole\Pdf\Model\Mail\TransportBuilder');

            $transportBuilder->setTemplateIdentifier(4)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => 1,
                    ]
                )
                ->setTemplateVars($templateParams)
                ->setFrom([
                    'name' => 'Monthly Report', //$senderName,
                    'email' => $senderEmail
                ])
                ->addTo($senderEmail, $senderName)
                ->addAttachment(file_get_contents($outFile),$fileName,$fileType = 'application/csv'); //Attachment goes here.

            $transport = $transportBuilder->getTransport();
            $transport->sendMessage();
        }catch (\Exception $e){
           $logger->info($e->getMessage());
        }

    }
}