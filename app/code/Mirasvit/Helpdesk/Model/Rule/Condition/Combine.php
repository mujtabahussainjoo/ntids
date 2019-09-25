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



namespace Mirasvit\Helpdesk\Model\Rule\Condition;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var \Mirasvit\Helpdesk\Model\Rule\Condition\ProductFactory
     */
    protected $ruleConditionProductFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\Rule\Condition\TicketFactory
     */
    protected $ruleConditionTicketFactory;

    /**
     * @param \Magento\Rule\Model\Condition\Context                  $context
     * @param \Mirasvit\Helpdesk\Model\Rule\Condition\ProductFactory $ruleConditionProductFactory
     * @param \Mirasvit\Helpdesk\Model\Rule\Condition\TicketFactory  $ruleConditionTicketFactory
     * @param array                                                  $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Mirasvit\Helpdesk\Model\Rule\Condition\ProductFactory $ruleConditionProductFactory,
        \Mirasvit\Helpdesk\Model\Rule\Condition\TicketFactory  $ruleConditionTicketFactory,
        array $data = []
    ) {
        $this->ruleConditionProductFactory = $ruleConditionProductFactory;
        $this->ruleConditionTicketFactory = $ruleConditionTicketFactory;
        parent::__construct($context, $data);
        $this->setType('Mirasvit\Helpdesk\Model\Rule\Condition\Combine');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $ticketCondition = $this->ruleConditionTicketFactory->create();
        $ticketAttributes = $ticketCondition->loadAttributeOptions()->getAttributeOption();

        $attributes = [];
        foreach ($ticketAttributes as $code => $label) {
            $attributes[] = [
                'value' => 'Mirasvit\Helpdesk\Model\Rule\Condition\Ticket|'.$code,
                'label' => $label,
            ];
        }
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => 'Mirasvit\Helpdesk\Model\Rule\Condition\Combine',
                    'label' => __('Conditions Combination'),
                ],
                ['label' => __('Ticket Attribute'), 'value' => $attributes],
            ]
        );

        return $conditions;
    }

    /**
     * @param array $productCollection
     *
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            /* @var Product|Combine $condition */
            $condition->collectValidatedAttributes($productCollection);
        }

        return $this;
    }
}
