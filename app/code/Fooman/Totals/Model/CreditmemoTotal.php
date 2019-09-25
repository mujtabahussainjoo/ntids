<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Totals
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Totals\Model;

use Fooman\Totals\Model\ResourceModel\CreditmemoTotal as ResourceCreditmemoTotal;

class CreditmemoTotal extends OrderTotal implements \Fooman\Totals\Api\Data\CreditmemoTotalInterface
{

    const KEY_CREDITMEMO_ID = 'creditmemo_id';
    const KEY_ORDER_ID = 'order_id';

    protected function _construct()
    {
        $this->_init(ResourceCreditmemoTotal::class);
    }

    /**
     * Get order id
     *
     * @return int|null
     */
    public function getOrderId()
    {
        return $this->getData(self::KEY_ORDER_ID);
    }

    /**
     * Set order id
     *
     * @param int $orderId
     *
     * @return void
     */
    public function setOrderId($orderId)
    {
        $this->setData(self::KEY_ORDER_ID, $orderId);
    }

    /**
     * Get credit memo id
     *
     * @return int|null
     */
    public function getCreditmemoId()
    {
        return $this->getData(self::KEY_CREDITMEMO_ID);
    }

    /**
     * Set credit memo id
     *
     * @param int $creditmemoId
     *
     * @return void
     */
    public function setCreditmemoId($creditmemoId)
    {
        $this->setData(self::KEY_CREDITMEMO_ID, $creditmemoId);
    }
}
