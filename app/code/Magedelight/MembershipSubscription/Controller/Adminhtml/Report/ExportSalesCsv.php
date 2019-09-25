<?php
/**
* Magedelight
* Copyright (C) 2017 Magedelight <info@magedelight.com>
*
* @category Magedelight
* @package Magedelight_MembershipSubscription
* @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
* @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
* @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\MembershipSubscription\Controller\Adminhtml\Report;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class ExportSalesCsv extends \Magento\Reports\Controller\Adminhtml\Report\Sales
{
    /**
     * Export sales report by category grid to CSV format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $fileName = 'membership_sales.csv';
        $grid = $this->_view->getLayout()->createBlock('Magedelight\MembershipSubscription\Block\Adminhtml\Report\Sales\Grid');
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getCsvFile(), DirectoryList::VAR_DIR);
    }
}
