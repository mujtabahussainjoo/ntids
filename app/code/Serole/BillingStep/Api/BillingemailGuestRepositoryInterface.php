<?php

//declare(strict_types=1);

namespace Serole\BillingStep\Api;

use Magento\Sales\Model\Order;
use Serole\BillingStep\Api\Data\BillingemailInterface;

/**
 * Interface BillingemailGuestRepositoryInterface
 *
 * @category Api/Interface
 * @package  Serole\BillingStep\Api
 */
interface BillingemailGuestRepositoryInterface
{
    /**
     * Save checkout custom fields
     *
     * @param string                                                   $cartId       Guest Cart id
     * @param \Serole\BillingStep\Api\Data\BillingemailInterface $billingemail Custom fields
     *
     * @return \Serole\BillingStep\Api\Data\BillingemailInterface
     */
    public function saveBillingemail(
        string $cartId,
        BillingemailInterface $billingemailemail
    ): BillingemailInterface;
}
