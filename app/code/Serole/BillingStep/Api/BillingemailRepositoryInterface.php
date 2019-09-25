<?php


//declare(strict_types=1);

namespace Serole\BillingStep\Api;

use Magento\Sales\Model\Order;
use Serole\BillingStep\Api\Data\BillingemailInterface;

/**
 * Interface BillingemailInterface
 *
 * @category Api/Interface
 * @package  Serole\BillingStep\Api
 */
interface BillingemailRepositoryInterface
{
    /**
     * Save Billing email
     *
     * @param int                                                      $cartId       Cart id
     * @param \Serole\BillingStep\Api\Data\BillingemailInterface $billingemail Custom fields
     *
     * @return \Serole\BillingStep\Api\Data\BillingemailInterface
     */
    public function saveBillingemail(
        int $cartId,
        BillingemailInterface $billingemail
    ): BillingemailInterface;

    /**
     * Get billing email
     *
     * @param Order $order Order
     *
     * @return BillingemailInterface
     */
    public function getBillingemail(Order $order) : BillingemailInterface;
}
