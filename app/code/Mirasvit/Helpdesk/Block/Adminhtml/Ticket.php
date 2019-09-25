<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Helpdesk\Block\Adminhtml;

class Ticket extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @var \Mirasvit\Helpdesk\Helper\User
     */
    protected $helpdeskUser;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Helpdesk\Helper\User        $helpdeskUser
     * @param \Magento\Framework\Registry           $registry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array                                 $data
     */
    public function __construct(
        \Mirasvit\Helpdesk\Helper\User $helpdeskUser,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->helpdeskUser = $helpdeskUser;
        $this->registry = $registry;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_ticket';
        $this->_blockGroup = 'Mirasvit_Helpdesk';
        $this->_headerText = __('Tickets');
        $this->_addButtonLabel = __('Create New Ticket');
        parent::_construct();

        if (!$this->context->getStoreManager()->isSingleStoreMode()) {
            $switcher = $this->getLayout()->createBlock('Magento\Backend\Block\Store\Switcher');
            $switcher->setUseConfirm(false)->setSwitchUrl(
                $this->getUrl(
                    '*/*/*/',
                    ['store' => -1, '_current' => true]
                ) //small hack here. i don't see other way to solve this.
            );
            if (!$this->getRequest()->getParam('store')) {
                $helpdeskUser = $this->helpdeskUser->getHelpdeskUser();
                $this->getRequest()->setParam('store', $helpdeskUser->getStoreId());
            }
        }
        $this->setTemplate('ticket/grid/container.phtml');
    }

    /**
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/add');
    }
    /************************/
}
