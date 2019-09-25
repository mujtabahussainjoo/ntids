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



namespace Mirasvit\Helpdesk\Block\Adminhtml\Sales\Order\View\Tabs;

class Ticket extends \Magento\Backend\Block\Widget implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Mirasvit\Helpdesk\Block\Adminhtml\Ticket\Grid $grid
     */
    protected $grid;

    /**
     * @var string
     */
    protected $gridHtml;

    /**
     * @return $this
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        /** @var \Mirasvit\Helpdesk\Block\Adminhtml\Ticket\Grid $grid */
        $grid = $this->getLayout()->createBlock('\Mirasvit\Helpdesk\Block\Adminhtml\Ticket\Grid');
        $grid->setId('helpdesk_grid_order');
        $grid->addCustomFilter('order_id', $this->getOrderId());
        $grid->setFilterVisibility(false);
        $grid->setPagerVisibility(0);
        $grid->setTabMode(true);
        $grid->setActiveTab('tickets');
        $this->grid = $grid;
        $this->gridHtml = $this->grid->toHtml();

        return parent::_prepareLayout();
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTabLabel()
    {
        return __('Help Desk Tickets (%1)', $this->grid->getFormattedNumberOfTickets());
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTabTitle()
    {
        return __('Help Desk Tickets');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return object
     */
    public function getOrderId()
    {
        return $this->getRequest()->getParam('order_id');
    }

    /**
     * @return string
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _toHtml()
    {
        $id = $this->getOrderId();
        $ticketNewUrl = $this->getUrl('helpdesk/ticket/add', ['order_id' => $id]);

        $button = $this->getLayout()->createBlock('\Magento\Backend\Block\Widget\Button');
        $button
            ->setClass('add')
            ->setType('button')
            ->setOnClick('window.location.href=\''.$ticketNewUrl.'\'')
            ->setLabel(__('Create ticket for this order'));

        return '<div>'.$button->toHtml().'<br><br>'.$this->gridHtml.'</div>';

        // return '<div class="content-buttons-placeholder" style="height:25px;">' .
        // '<p class="content-buttons form-buttons" >' . $button->toHtml() . '</p>' .
        // '</div>' . $grid->toHtml();
    }
}
