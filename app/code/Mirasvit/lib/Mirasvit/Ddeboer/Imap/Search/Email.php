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
 * Represents an email condition.
 */
abstract class Mirasvit_Ddeboer_Imap_Search_Email extends Mirasvit_Ddeboer_Imap_Search_Condition
{
    /**
     * Email address for the condition.
     *
     * @var string
     */
    protected $email;

    /**
     * Constructor.
     *
     * @param string $email Optional email address for the condition.
     */
    public function __construct($email = null)
    {
        if ($email) {
            $this->setEmail($email);
        }
    }

    /**
     * Sets the email address for the condition.
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Converts the condition to a string that can be sent to the IMAP server.
     *
     * @return string.
     */
    public function __toString()
    {
        return $this->getKeyword().' "'.$this->email.'"';
    }
}
