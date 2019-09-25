<?php

namespace Serole\Carreport\Observer;


use Magento\Framework\Event\ObserverInterface;


class Order implements ObserverInterface {

    protected $connector;

    protected $orderFactory;

    protected $product;

    protected $carReport;

    protected $storeManager;


    public function __construct(\Magento\Sales\Model\OrderFactory $orderFactory,
                                \Magento\Catalog\Model\Product $product,
                                \Serole\Carreport\Model\Carreport $carReport,
                                \Magento\Store\Model\StoreManagerInterface $storeManager
                               ) {
          $this->carReport = $carReport;
          $this->product = $product;
          $this->orderFactory = $orderFactory;
          $this->storeManager = $storeManager;
          $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $order = $observer->getEvent()->getOrder();
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/carreport-log.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        try{
            $orderData = $this->orderFactory->create()->load($order->getId());
            $orderIncreId = $orderData->getIncrementId();
            $items = $orderData->getAllItems();
            $pdftempro = 0;
            $websiteIdss = '1';
            $storeId = $this->storeManager->getStore()->getId();
            foreach ($items as $itemId => $item) {
                $productId = $item->getProductId();
                $isCarReport = $this->product->getResource()->getAttributeRawValue($productId, 'iscarreport', $storeId);
                if($isCarReport){
                    $pdftempro = 1;
                    $optionsArr = $item->getProductOptions();
                    $proOptionArr = array();
                    if(isset($optionsArr['options'])){
                        if(count($optionsArr['options']) > 0) {
                            foreach ($optionsArr['options'] as $option){
                                $optionTitle = $option['label'];
                                $optionId = $option['option_id'];
                                $optionType = $option['option_type'];
                                $optionValue = $option['option_value'];
                                if($optionTitle == 'Odometer') {
                                    $proOptionArr['Odometer'] = $optionValue;
                                    //$Odometer = $optionValue;
                                } else if($optionTitle == 'Vehicle VIN'
                                    || $optionTitle == 'VIN'
                                    || $optionTitle='Vehicle VIN (must be 17 characters and it is not your Registration number)') {
                                    //$Vehicle = $optionValue;
                                    $proOptionArr['Vehicle'] = $optionValue;
                                }
                            }
                        }
                    }
                    $carReportObj = $this->carReport;
                    $carReportObj->setOrderId($orderIncreId);
                    $carReportObj->setProductId($productId);
                    $carReportObj->setVin($proOptionArr['Vehicle']);
                    $carReportObj->setOdometer($proOptionArr['Odometer']);
                    $carReportObj->setStatus('pending');
                    $carReportObj->setCreatedAt(date("Y-m-d H:i:s"));
                    $carReportObj->setUpdatedAt(date("Y-m-d H:i:s"));
                    $carReportObj->save();
                }
            }

        }catch (\Exception $e){
            $logger->info($e->getMessage());
        }

    }
}