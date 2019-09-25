<?php

    namespace Serole\Pdf\Cron;

    class Pdf{

        protected $orderPdf;

        protected $createPdf;

        public function __construct(\Serole\Pdf\Model\Pdf $orderPdf,
                                    \Serole\Pdf\Model\Createpdf $createPdf
                                  ){
            $this->orderPdf = $orderPdf;
            $this->createPdf = $createPdf;
        }

        public function execute() {
            try {
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cronjob-pdf-create.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);

                $presentTime = date("Y-m-d H:i:s");
                $fiveMinBackTime = date("Y-m-d H:i:s", strtotime("-5 minutes"));

                $orderColl = $this->orderPdf->getCollection();
                $orderColl->addFieldToFilter('status', 'pending');
                /*$orderColl->addFieldToFilter('created_at', array(
                        'lt' => $fiveMinBackTime
                    )
				
                );*/
                foreach ($orderColl->getData() as $orderItem) {
                    $logger->info("Cron at =>".$presentTime.' , with Id =>'.$orderItem['order_id']);
                    $this->createPdf->createPdfConcept($orderItem['order_id'], $emailStauts = True, $reqType = 'backend');
                }
				return true;
				
            }catch (\Exception $e){
                $logger->info($e->getMessage());
            }
			
        }
    }
