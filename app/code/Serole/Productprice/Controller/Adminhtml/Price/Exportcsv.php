<?php

namespace Serole\Productprice\Controller\Adminhtml\Price;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;

class Exportcsv extends \Magento\Reports\Controller\Adminhtml\Report\AbstractReport
{

    protected $_fileFactory;

   public function execute()
    {
        $this->_view->loadLayout(false);

        $fileName = 'productprice.csv';

        $exportBlock = $this->_view->getLayout()->createBlock('Serole\Productprice\Block\Adminhtml\Price\Grid');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $this->_fileFactory = $objectManager->create('Magento\Framework\App\Response\Http\FileFactory');

        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getCsvFile(),
            DirectoryList::VAR_DIR
        );
    }
}
