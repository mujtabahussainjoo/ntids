<?php


//declare(strict_types=1);

namespace Serole\BillingStep\Api;

use Magento\Sales\Model\Order;
use Serole\BillingStep\Api\Data\DeliveryemailInterface;

/**
 * Interface DeliveryemailInterface
 *
 * @category Api/Interface
 * @package  Serole\BillingStep\Api
 */
interface DeliveryemailRepositoryInterface
{
    /**
     * Save delivery email
     *
     * @param int                                                      $cartId       Cart id
     * @param \Serole\BillingStep\Api\Data\DeliveryemailInterface $deliveryemail Custom fields
     *
     * @return \Serole\BillingStep\Api\Data\DeliveryemailInterface
     */
    public function saveDeliveryemail(
        int $cartId,
        DeliveryemailInterface $deliveryemail
    ): DeliveryemailInterface;

    /**
     * Get delivery email
     *
     * @param Order $order Order
     *
     * @return DeliveryemailInterface
     */
    public function getDeliveryemail(Order $order) : DeliveryemailInterface;
}
