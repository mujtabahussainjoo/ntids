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



// namespace Mirasvit_Ddeboer\Imap\Search\Text;

// use Mirasvit_Ddeboer\Imap\Search\Text;

/**
 * Represents a body text contains condition. Messages must have a body
 * containing the specified text in order to match the condition.
 */
class Mirasvit_Ddeboer_Imap_Search_Text_Body extends Mirasvit_Ddeboer_Imap_Search_Text
{
    /**
     * Returns the keyword that the condition represents.
     *
     * @return string
     */
    public function getKeyword()
    {
        return 'BODY';
    }
}
