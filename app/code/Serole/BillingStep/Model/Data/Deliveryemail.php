<?php
/**
 * @package   Bodak\CheckoutCustomForm
 * @author    Slawomir Bodak <slawek.bodak@gmail.com>
 * @copyright © 2017 Slawomir Bodak
 * @license   See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Serole\BillingStep\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use Serole\BillingStep\Api\Data\DeliveryemailInterface;

/**
 * Class CustomFields
 *
 * @category Model/Data
 * @package  Serole\BillingStep\Model\Data
 */
class Deliveryemail extends AbstractExtensibleObject implements DeliveryemailInterface
{

    /**
     * Get deliveryemail
     *
     * @return string|null
     */
    public function getDeliveryemail()
    {
        return $this->_get(self::DELIVERYEMAIL);
    }

     /**
     * Set deliveryemail
     *
     * @param string|null $deliveryemail deliveryemail
     *
     * @return DeliveryemailInterface
     */
    public function setDeliveryemail(string $deliveryemail = null)
    {
        return $this->setData(self::DELIVERYEMAIL, $deliveryemail);
    }


}
