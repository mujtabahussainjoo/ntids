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



namespace Mirasvit\Helpdesk\Model\Config\Source;

class GatewayIds implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Gateway\CollectionFactory
     */
    protected $gatewayCollectionFactory;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Gateway\CollectionFactory $gatewayCollectionFactory
     * @param \Magento\Framework\Model\Context                                 $context
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\ResourceModel\Gateway\CollectionFactory $gatewayCollectionFactory,
        \Magento\Framework\Model\Context $context
    ) {
        $this->gatewayCollectionFactory = $gatewayCollectionFactory;
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->gatewayCollectionFactory->create()->getOptionArray();
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];
        foreach ($this->toArray() as $k => $v) {
            $result[] = ['value' => $k, 'label' => $v];
        }

        return $result;
    }
}
