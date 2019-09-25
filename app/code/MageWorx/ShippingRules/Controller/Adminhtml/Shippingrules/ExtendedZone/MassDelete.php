<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\ExtendedZone;

use MageWorx\ShippingRules\Controller\Adminhtml\Shippingrules\Base\MassDeleteAbstract as BaseMassDelete;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use MageWorx\ShippingRules\Model\ResourceModel\ExtendedZone\CollectionFactory;

class MassDelete extends BaseMassDelete
{
    /**
     * MassDelete constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param string $aclResourceName
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        $aclResourceName = 'MageWorx_ShippingRules::extended_zones'
    ) {
        parent::__construct($context, $filter, $collectionFactory, $aclResourceName);
    }
}
