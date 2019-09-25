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


// @codingStandardsIgnoreFile
// namespace Mirasvit_Ddeboer\Imap;

// use Mirasvit_Ddeboer\Imap\Search\Condition;

/**
 * Defines a search expression that can be used to look up email messages.
 */
class Mirasvit_Ddeboer_Imap_SearchExpression
{
    /**
     * The conditions that together represent the expression.
     *
     * @var array
     */
    protected $conditions = array();

    /**
     * Adds a new condition to the expression.
     *
     * @param Condition $condition The condition to be added.
     *
     * @return SearchExpression
     */
    public function addCondition(Mirasvit_Ddeboer_Imap_Search_Condition $condition)
    {
        $this->conditions[] = $condition;

        return $this;
    }

    /**
     * Converts the expression to a string that can be sent to the IMAP server.
     *
     * @return string
     */
    public function __toString()
    {
        return implode(' ', $this->conditions);
    }
}
