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

// use DateTime;

/**
 * Represents a date condition.
 */
abstract class Mirasvit_Ddeboer_Imap_Search_Date extends Mirasvit_Ddeboer_Imap_Search_Condition
{
    /**
     * Format for dates to be sent to the IMAP server.
     *
     * @var string
     */
    const DATE_FORMAT = 'Y-m-d';

    /**
     * The date to be used for the condition.
     *
     * @var DateTime
     */
    protected $date;

    /**
     * Constructor.
     *
     * @param DateTime $date Optional date for the condition.
     */
    public function __construct(DateTime $date = null)
    {
        if ($date) {
            $this->setDate($date);
        }
    }

    /**
     * Sets the date for the condition.
     *
     * @param DateTime $date
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * Converts the condition to a string that can be sent to the IMAP server.
     *
     * @return string.
     */
    public function __toString()
    {
        return $this->getKeyword().' "'.$this->date->format(self::DATE_FORMAT).'"';
    }
}
