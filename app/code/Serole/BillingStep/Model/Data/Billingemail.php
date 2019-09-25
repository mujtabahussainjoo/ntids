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
use Serole\BillingStep\Api\Data\BillingemailInterface;
use Serole\BillingStep\Api\Data\DeliveryemailInterface;

/**
 * Class Billingemail
 *
 * @category Model/Data
 * @package  Serole\BillingStep\Model\Data
 */
class Billingemail extends AbstractExtensibleObject implements BillingemailInterface
{

    /**
     * Get billingemail
     *
     * @return string|null
     */
    public function getBillingemail()
    {
        return $this->_get(self::BILLINGEMAIL);
    }

    /**
     * Set billingemail
     *
     * @param string|null $billingemail billingemail
     *
     * @return BillingemailInterface
     */
    public function setBillingemail(string $billingemail = null)
    {
        return $this->setData(self::BILLINGEMAIL, $billingemail);
    }


}
