<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Block\Adminhtml\ActionsLog\Tabs;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

class Customer extends \Amasty\AdminActionsLog\Block\Adminhtml\ActionsLog\Tabs\DefaultItemColumns implements TabInterface
{
    protected function _prepareCollection()
    {
        $elementId = $this->getRequest()->getParam('id');
        $collection = $this->_objectManager->get('Amasty\AdminActionsLog\Model\Log')->getCollection();
        $collection->getSelect()
            ->joinLeft(array('r' => $this->_objectManager->get('Amasty\AdminActionsLog\Model\LogDetails')->getCollection()->getMainTable()), 'main_table.id = r.log_id', array('is_logged' => 'MAX(r.log_id)'))
            ->where("element_id = ?", $elementId)
            ->where("category = ?", 'customer/index')
            ->group('r.log_id')
        ;
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    public function getTabLabel()
    {
        return __('History of Changes');
    }

    public function getTabTitle()
    {
        return __('History of Changes');
    }

    public function canShowTab()
    {
        if ($this->getCustomerId()) {
            return true;
        }
        return false;
    }

    public function isHidden()
    {
        if ($this->getCustomerId()) {
            return false;
        }
        return true;
    }

    public function getTabClass()
    {
        return '';
    }

    public function getTabUrl()
    {
        return $this->getUrl('amaudit/actionslog/customer', ['_current' => true]);
    }

    public function isAjaxLoaded()
    {
        return false;
    }
}
