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


namespace Mirasvit\Helpdesk\Api\Data;

interface FieldInterface
{
    const TABLE_NAME  = 'mst_helpdesk_field';

    const ID = 'field_id';

    const KEY_NAME        = 'name';
    const KEY_CODE        = 'code';
    const KEY_DESCRIPTION = 'description';
    const KEY_VALUES      = 'values';

    //@todo finish interface
}