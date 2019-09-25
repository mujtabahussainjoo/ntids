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

class Custom extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Rule\Model\Condition\Context     $context
     * @param \Magento\Framework\Registry               $registry
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param array                                     $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        $this->resource = $resource;
        $this->context = $context;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $attributes = [
            'clicks_7' => __('Last 7-days Clicks'),
            'orders_7' => __('Last 7-days Orders'),
            'revenue_7' => __('Last 7-days Revenue'),
            'cr_7' => __('Last 7-days Conversation Rate (%)'),
        ];

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @return $this
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    /**
     * @param object $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        $attribute = $this->getAttribute();

        $arr = explode('_', $attribute);
        $type = $arr[0];
        $period = $arr[1];

        $date = new \Zend_Date();
        $date->sub($period * 24 * 60 * 60);

        $resource = $this->resource;
        $connection = $resource->getConnection();

        switch ($type) {
            case 'clicks':
                $expr = new \Zend_Db_Expr('SUM(clicks)');
                break;

            case 'orders':
                $expr = new \Zend_Db_Expr('SUM(orders)');
                break;

            case 'revenue':
                $expr = new \Zend_Db_Expr('SUM(revenue)');
                break;

            case 'cr':
                $expr = new \Zend_Db_Expr('SUM(orders) / SUM(clicks) * 100');
                break;
        }

        $select = $connection->select();
        $select->from(['ta' => $resource->getTableName('mst_helpdesk_performance_aggregated')], [$expr])
            ->where('ta.product_id = e.entity_id')
            ->where('ta.period >= ?', $date->toString('YYYY-MM-dd'));

        $productCollection->getSelect()->columns([$attribute => $select]);

        return $this;
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        return 'string';
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return string
     */
    public function validate(\Magento\Framework\Model\AbstractModel $object)
    {
        $attrCode = $this->getAttribute();
        $value = $object->getData($attrCode);

        return $this->validateAttribute($value);
    }

    /**
     * @return string
     */
    public function getJsFormObject()
    {
        return 'rule_conditions_fieldset';
    }
}
