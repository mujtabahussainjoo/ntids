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

class Color implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'aqua' => __('Aqua'),
            'grey' => __('Grey'),
            'navy' => __('Navy'),
            'silver' => __('Silver'),
            'black' => __('Black'),
            'green' => __('Green'),
            'olive' => __('Olive'),
            'teal' => __('Teal'),
            'blue' => __('Blue'),
            'lime' => __('Lime'),
            'purple' => __('Purple'),
            'fuchsia' => __('Fuchsia'),
            'maroon' => __('Maroon'),
            'red' => __('Red'),
            'orange' => __('Orange'),
            'yellow' => __('Yellow'),
        ];
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

    /************************/
}
