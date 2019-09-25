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



namespace Mirasvit\Helpdesk\Model\Config\Source\Is;

use Mirasvit\Helpdesk\Model\Config as Config;

class Archive
{
    /**
     * @param bool $emptyOption
     *
     * @return array
     */
    public function toArray($emptyOption = false)
    {
        $result = [];
        if ($emptyOption) {
            $result[0] = __('-- Please Select --');
        }

        $result[Config::IS_ARCHIVE_TO_ARCHIVE] = __('Move to Archive');
        $result[Config::IS_ARCHIVE_FROM_ARCHIVE] = __('Move from Archive');

        return $result;
    }

    /**
     * @param bool $emptyOption
     *
     * @return array
     */
    public function toOptionArray($emptyOption = false)
    {
        $result = [];
        foreach ($this->toArray($emptyOption) as $k => $v) {
            $result[] = ['value' => $k, 'label' => $v];
        }

        return $result;
    }

    /************************/
}
