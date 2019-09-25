<?php

declare(strict_types=1);

namespace Serole\BillingStep\Model;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Model\Order;
use Serole\BillingStep\Api\BillingemailRepositoryInterface;
use Serole\BillingStep\Api\Data\BillingemailInterface;

/**
 * Class BillingemailRepository
 *
 * @category Model/Repository
 * @package  Serole\BillingStep\Model
 */
class BillingemailRepository implements BillingemailRepositoryInterface
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
     * BillingemailInterface
     *
     * @var BillingemailInterface
     */
    protected $billingemail;

    /**
     * BillingemailRepository constructor.
     *
     * @param CartRepositoryInterface $cartRepository CartRepositoryInterface
     * @param ScopeConfigInterface    $scopeConfig    ScopeConfigInterface
     * @param BillingemailInterface   $billingaddress   BillingemailInterface
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        ScopeConfigInterface $scopeConfig,
        BillingemailInterface $billingemail
    ) {
        $this->cartRepository = $cartRepository;
        $this->scopeConfig    = $scopeConfig;
        $this->billingemail   = $billingemail;
    }
    /**
     * Save checkout custom fields
     *
     * @param int                                                      $cartId       Cart id
     * @param \Serole\BillingStep\Api\Data\BillingemailInterface $billingemail Custom fields
     *
     * @return \Serole\BilingStep\Api\Data\BillingemailInterface
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function saveBillingemail(
        int $cartId,
        BillingemailInterface $billingemail
     ): BillingemailInterface {
        $cart = $this->cartRepository->getActive($cartId);
        if (!$cart->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 is empty', $cartId));
        }

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($billingemail->getBillingemail());

        try {
            $cart->setData(
                BillingemailInterface::BILLINGEMAIL,
                $billingemail->getBillingemail()
            );

            $this->cartRepository->save($cart);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Custom order data could not be saved!'));
        }

        return $billingemail;
    }

    /**
     * Get checkout custom fields by given order id
     *
     * @param Order $order Order
     *
     * @return BillingemailInterface
     * @throws NoSuchEntityException
     */

    public function getBillingemail(Order $order): BillingemailInterface
    {
        if (!$order->getId()) {
            throw new NoSuchEntityException(__('Order %1 does not exist', $order));
        }

        $this->billingemail->setBillingemail(
            $order->getData(BillingemailInterface::BILLINGEMAIL)
        );
        return $this->billingemail;
    }
}
