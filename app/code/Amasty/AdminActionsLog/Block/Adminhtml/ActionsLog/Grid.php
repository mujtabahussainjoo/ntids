<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Block\Adminhtml\ActionsLog;

class Grid extends \Amasty\AdminActionsLog\Block\Adminhtml\ActionsLog\Tabs\DefaultItemColumns
{
    protected function _prepareCollection()
    {
        $this->setDefaultSort('date_time');
        $this->setDefaultDir('desc');

        /**
         * @var \Amasty\AdminActionsLog\Model\Log $log
         */
        $log = $this->_objectManager->get('Amasty\AdminActionsLog\Model\Log');
        $collection = $log->getCollection();
        $collection->getSelect()
            ->joinLeft(
                array('u' => $this->_objectManager->get('Magento\User\Model\User')->getCollection()->getMainTable()),
                'main_table.username = u.username',
                array('fullname' => "CONCAT(firstname, ' ' ,lastname)", 'firstname', 'lastname')
            )
        ;
        $collection->addFilterToMap('username', 'main_table.username');
        $this->setCollection($collection);
        return parent::_prepareCollection();

    }
}
