<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Block\Adminhtml\ActionsLog\Tabs;

class Order extends \Amasty\AdminActionsLog\Block\Adminhtml\ActionsLog\Tabs\DefaultItemColumns
{
    protected function _prepareCollection()
    {
        $elementId = $this->getRequest()->getParam('order_id');
        $collection = $this->_objectManager->get('Amasty\AdminActionsLog\Model\Log')->getCollection();
        $collection->getSelect()
            ->joinLeft(array('r' => $this->_objectManager->get('Amasty\AdminActionsLog\Model\LogDetails')->getCollection()->getMainTable()), 'main_table.id = r.log_id', array('is_logged' => 'MAX(r.log_id)'))
            ->where("element_id = ?", $elementId)
            ->where("category = ?", 'sales/order')
            ->group('r.log_id')
        ;
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
}
