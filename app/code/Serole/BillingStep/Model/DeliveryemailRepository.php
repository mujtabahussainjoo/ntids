<?php

declare(strict_types=1);

namespace Serole\BillingStep\Model;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Model\Order;
use Serole\BillingStep\Api\DeliveryemailRepositoryInterface;
use Serole\BillingStep\Api\Data\DeliveryemailInterface;

/**
 * Class DeliveryemailRepository
 *
 * @category Model/Repository
 * @package  Serole\BillingStep\Model
 */
class DeliveryemailRepository implements DeliveryemailRepositoryInterface
{
    /**
     * Quote repository.
     *
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * ScopeConfigInterface
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * DeliveryemailInterface
     *
     * @var DeliveryemailInterface
     */
    protected $deliveryemail;

    /**
     * DeliveryemailRepository constructor.
     *
     * @param CartRepositoryInterface $cartRepository CartRepositoryInterface
     * @param ScopeConfigInterface    $scopeConfig    ScopeConfigInterface
     * @param DeliveryemailInterface   $deliveryemail   DeliveryemailInterface
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        ScopeConfigInterface $scopeConfig,
        DeliveryemailInterface $deliveryemail
    ) {
        $this->cartRepository = $cartRepository;
        $this->scopeConfig    = $scopeConfig;
        $this->deliveryemail   = $deliveryemail;
    }
    /**
     * Save checkout custom fields
     *
     * @param int                                                      $cartId       Cart id
     * @param \Serole\BillingStep\Api\Data\DeliveryemailInterface $deliveryemail Custom fields
     *
     * @return \Serole\BilingStep\Api\Data\DeliveryemailInterface
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function saveDeliveryemail(
        int $cartId,
        DeliveryemailInterface $deliveryemail
    ): DeliveryemailInterface {
        $cart = $this->cartRepository->getActive($cartId);
        if (!$cart->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 is empty', $cartId));
        }

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($deliveryemail->getDeliveryemail());

        try {
            $cart->setData(
                DeliveryemailInterface::DELIVERYEMAIL,
                $deliveryemail->getDeliveryemail()
            );
          

            $this->cartRepository->save($cart);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Custom order data could not be saved!'));
        }

        return $deliveryemail;
    }

    /**
     * Get checkout custom fields by given order id
     *
     * @param Order $order Order
     *
     * @return DeliveryemailInterface
     * @throws NoSuchEntityException
     */
    public function getDeliveryemail(Order $order): DeliveryemailInterface
    {
        if (!$order->getId()) {
            throw new NoSuchEntityException(__('Order %1 does not exist', $order));
        }

        $this->deliveryemail->setDeliveryemail(
            $order->getData(DeliveryemailInterface::DELIVEYEMAIL)
        );
        return $this->deliveryemail;
    }
}
