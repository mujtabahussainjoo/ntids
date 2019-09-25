<?php

//declare(strict_types=1);

namespace Serole\BillingStep\Api;

use Magento\Sales\Model\Order;
use Serole\BillingStep\Api\Data\DeliveryemailInterface;

/**
 * Interface DeliveryemailGuestRepositoryInterface
 *
 * @category Api/Interface
 * @package  Serole\BillingStep\Api
 */
interface DeliveryemailGuestRepositoryInterface
{
    /**
     * Save checkout custom fields
     *
     * @param string                                                   $cartId       Guest Cart id
     * @param \Serole\BillingStep\Api\Data\DeliveryemailInterface $deliveryemail Custom fields
     *
     * @return \Serole\BillingStep\Api\Data\DeliveryemailInterface
     */
    public function saveDeliveryemail(
        string $cartId,
        DeliveryemailInterface $deliveryemail
    ): DeliveryemailInterface;
}
