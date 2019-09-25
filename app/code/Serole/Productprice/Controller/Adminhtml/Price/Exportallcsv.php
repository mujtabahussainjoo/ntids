<?php

  namespace Serole\Productprice\Controller\Adminhtml\Price;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Exportallcsv extends \Magento\Reports\Controller\Adminhtml\Report\AbstractReport
{

    protected $resultPageFactory;

    protected $_fileFactory;

    protected $_dateFilter;

    protected $store;

    protected $product;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Store\Model\Store $store,
        \Magento\Catalog\Model\Product $product,
        TimezoneInterface $timezone
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_fileFactory       = $fileFactory;
        $this->_dateFilter = $dateFilter;
        $this->timezone = $timezone;
        $this->store = $store;
        $this->product = $product;
        parent::__construct($context,$fileFactory,$dateFilter,$timezone);
    }


    public function execute()
    {
        $fileName   = 'productpriceAll.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=productpriceAll.csv');
        $output = fopen("php://output", "w");
        fputcsv($output, array('Store', 'Sku', 'Name', 'Price', 'Special Price', 'Subsidy Price'));
        $content = array();
        $allStores = $this->store->getCollection();
        //echo "<pre>";print_r($allStores->getData());
        foreach ($allStores as $store) {
            $_storeId = $store->getId();
            $_storeName = $store->getName();
            $_storeCode = $store->getCode();
            $content['Store'] = $_storeName;
            //if($_storeId == 60){
            $collection = $this->product->getCollection();
            $collection->addAttributeToSelect('*');
            $collection->setStore($_storeId);
            foreach($collection as $item){
                $content['sku'] = $item->getSku();
                $content['name'] = $item->getName();
                $content['price'] = $item->getPrice();
                $content['specialprice'] = $item->getSpecialPrice();
                $content['subsidy'] = $item->getSubsidy();
                fputcsv($output, $content);
            }

        }
        fclose($output);
    }

}
