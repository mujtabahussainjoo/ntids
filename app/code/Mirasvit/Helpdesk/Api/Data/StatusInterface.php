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

interface StatusInterface
{
    const OPEN        = 1;
    const IN_PROGRESS = 2;
    const CLOSED      = 3;
    const TABLE_NAME  = 'mst_helpdesk_status';

    const ID = 'status_id';

    const KEY_NAME = 'name';

    //@todo finish interface
}