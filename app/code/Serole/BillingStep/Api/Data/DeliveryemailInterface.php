<?php

//declare(strict_types=1);

namespace Serole\BillingStep\Api\Data;

/**
 * Interface DeliveryemailInterface
 *
 * @category Api/Data/Interface
 * @package  Serole\BillingStep\Api\Data
 */
interface DeliveryemailInterface
{
    const DELIVERYEMAIL = 'deliveryemail';

    /**
     * Get deliveryemail
     *
     * @return string|null
     */
    public function getDeliveryemail();

    /**
     * Set deliveryemail
     *
     * @param string|null $deliveryemail Delivery email
     *
     * @return DeliveryemailInterface
     */
    public function setDeliveryemail(string $deliveryemail = null);

}
