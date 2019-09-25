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


namespace Mirasvit\Helpdesk\Controller\Adminhtml\Schedule;

class MassChange extends \Mirasvit\Helpdesk\Controller\Adminhtml\MassChange
{
    public function __construct(
        \Mirasvit\Helpdesk\Model\ResourceModel\Schedule $scheduleResource,
        \Mirasvit\Helpdesk\Model\ResourceModel\Schedule\CollectionFactory $collectionFactory,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->resource           = $scheduleResource;
        $this->collectionFactory  = $collectionFactory;
        $this->filter             = $filter;
        $this->context            = $context;
        $permission               = 'Mirasvit_Helpdesk::helpdesk_schedule';

        parent::__construct($filter, $context, $permission);
    }
}
