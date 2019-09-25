<?php

//declare(strict_types=1);

namespace Serole\BillingStep\Api\Data;

/**
 * Interface BillingemailInterface
 *
 * @category Api/Data/Interface
 * @package  Serole\BillingStep\Api\Data
 */
interface BillingemailInterface
{
    const BILLINGEMAIL = 'billingemail';

    /**
     * Get billingemail
     *
     * @return string|null
     */
    public function getBillingemail();

    /**
     * Set billingemail
     *
     * @param string|null $billingemail Billing email
     *
     * @return BillingemailInterface
     */
    public function setBillingemail(string $billingemail = null);

}
