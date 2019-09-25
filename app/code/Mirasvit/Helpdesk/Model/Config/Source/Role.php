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

class Role implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Roles.
     *
     * @var \Magento\Authorization\Model\ResourceModel\Role\Collection
     */
    protected $roleCollection;

    /**
     * @param \Magento\Authorization\Model\ResourceModel\Role\Collection $roleCollection
     */
    public function __construct(\Magento\Authorization\Model\ResourceModel\Role\Collection $roleCollection)
    {
        $this->roleCollection = $roleCollection;
    }

    /**
     * Options array.
     *
     * @var array
     */
    protected $options;

    /**
     * Return options array.
     *
     * @param bool $emptyOption
     *
     * @return array
     */
    public function toOptionArray($emptyOption = false)
    {
        if (!$this->options) {
            $this->options = [];
            foreach ($this->roleCollection as $item) {
                $this->options[] = ['value' => $item->getId(), 'label' => $item->getName()];
            }
        }

        $options = $this->options;
        if ($emptyOption) {
            array_unshift($options, ['value' => '', 'label' => __('--Please Select--')]);
        }

        return $options;
    }
}
