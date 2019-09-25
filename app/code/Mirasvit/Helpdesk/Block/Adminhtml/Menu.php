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

use Magento\Backend\Block\Template\Context;
use Mirasvit\Core\Block\Adminhtml\AbstractMenu;

class Menu extends AbstractMenu
{
    /**
     * {@inheritdoc}
     */
    public function __construct(
        Context $context
    ) {
        $this->visibleAt(['helpdesk']);

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildMenu()
    {
        $this->addItem([
            'resource' => 'Mirasvit_Helpdesk::helpdesk_ticket',
            'title'    => __('Tickets'),
            'url'      => $this->urlBuilder->getUrl('helpdesk/ticket'),
        ])->addItem([
            'resource' => 'Mirasvit_Helpdesk::helpdesk_department',
            'title'    => __('Departments'),
            'url'      => $this->_urlBuilder->getUrl('helpdesk/department'),
        ])->addItem([
            'resource' => 'Mirasvit_Helpdesk::helpdesk_gateway',
            'title'    => __('Gateways'),
            'url'      => $this->_urlBuilder->getUrl('helpdesk/gateway'),
        ])->addItem([
            'resource' => 'Mirasvit_Helpdesk::helpdesk_priority',
            'title'    => __('Priorities'),
            'url'      => $this->_urlBuilder->getUrl('helpdesk/priority'),
        ])->addItem([
            'resource' => 'Mirasvit_Helpdesk::helpdesk_status',
            'title'    => __('Statuses'),
            'url'      => $this->_urlBuilder->getUrl('helpdesk/status'),
        ])->addItem([
            'resource' => 'Mirasvit_Helpdesk::helpdesk_template',
            'title'    => __('Quick Responses'),
            'url'      => $this->_urlBuilder->getUrl('helpdesk/template'),
        ])->addItem([
            'resource' => 'Mirasvit_Helpdesk::helpdesk_field',
            'title'    => __('Custom Fields'),
            'url'      => $this->_urlBuilder->getUrl('helpdesk/field'),
        ])->addItem([
            'resource' => 'Mirasvit_Helpdesk::helpdesk_pattern',
            'title'    => __('SPAM patterns'),
            'url'      => $this->_urlBuilder->getUrl('helpdesk/pattern'),
        ])->addItem([
            'resource' => 'Mirasvit_Helpdesk::helpdesk_permission',
            'title'    => __('Permissions'),
            'url'      => $this->_urlBuilder->getUrl('helpdesk/permission'),
        ])->addItem([
            'resource' => 'Mirasvit_Helpdesk::helpdesk_rule',
            'title'    => __('Workflow Rules'),
            'url'      => $this->_urlBuilder->getUrl('helpdesk/rule'),
        ])->addItem([
            'resource' => 'Mirasvit_Helpdesk::helpdesk_satisfaction',
            'title'    => __('Satisfaction Survey Results'),
            'url'      => $this->_urlBuilder->getUrl('helpdesk/satisfaction'),
        ])->addItem([
            'resource' => 'Mirasvit_Helpdesk::helpdesk_schedule',
            'title'    => __('Working Hours'),
            'url'      => $this->_urlBuilder->getUrl('helpdesk/schedule'),
        ])->addItem([
            'resource' => 'Mirasvit_Helpdesk::helpdesk_report',
            'title'    => __('Reports'),
            'url'      => $this->urlBuilder->getUrl('helpdesk/report/view'),
        ])->addItem([
            'resource' => 'Mirasvit_Helpdesk::helpdesk_aggregate',
            'title'    => __('Refresh Statistics'),
            'url'      => $this->urlBuilder->getUrl('helpdesk/report/aggregate'),
        ])
        ;
        $this->addSeparator();

        $this->addItem([
            'resource' => 'Mirasvit_Helpdesk::helpdesk_settings',
            'title'    => __('Settings'),
            'url'      => $this->_urlBuilder->getUrl('adminhtml/system_config/edit/section/helpdesk'),
        ]);

        return $this;
    }
}
