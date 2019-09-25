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
// namespace Mirasvit_Ddeboer\Imap\Search;

/**
 * Represents a text based condition. Text based conditions use a contains
 * restriction.
 */
abstract class Mirasvit_Ddeboer_Imap_Search_Text extends Mirasvit_Ddeboer_Imap_Search_Condition
{
    /**
     * Text to be used for the condition.
     *
     * @var string
     */
    protected $text;

    /**
     * Constructor.
     *
     * @param string $text Optional text for the condition.
     */
    public function __construct($text = null)
    {
        if (!is_null($text) && strlen($text) > 0) {
            $this->setText($text);
        }
    }

    /**
     * Sets the text for the condition.
     *
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Converts the condition to a string that can be sent to the IMAP server.
     *
     * @return string.
     */
    public function __toString()
    {
        return $this->getKeyword().' "'.str_replace('"', '\\"', $this->text).'"';
    }
}
