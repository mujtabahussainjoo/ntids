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



// namespace Mirasvit_Ddeboer\Imap\Search;

/**
 * Represents a condition that can be used in a search expression.
 */
abstract class Mirasvit_Ddeboer_Imap_Search_Condition
{
    /**
     * Converts the condition to a string that can be sent to the IMAP server.
     *
     * @return string.
     */
    public function __toString()
    {
        return $this->getKeyword();
    }

    /**
     * Returns the keyword that the condition represents.
     *
     * @return string
     */
    abstract protected function getKeyword();
}
