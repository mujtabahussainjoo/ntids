<?php

declare(strict_types=1);

namespace Serole\BillingStep\Model;

use Magento\Quote\Model\QuoteIdMaskFactory;
use Serole\BillingStep\Api\DeliveryemailGuestRepositoryInterface;
use Serole\BillingStep\Api\DeliveryemailRepositoryInterface;
use Serole\BillingStep\Api\Data\DeliveryemailInterface;
use Serole\BillingStep\Model\DeliveryemailRepository;

/**
 * Class DeliveryemailGuestRepository
 *
 * @category Model/Repository
 * @package  Serole\BillingStep\Model
 */
class DeliveryemailGuestRepository implements DeliveryemailGuestRepositoryInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var DeliveryemailRepositoryInterface
     */
    protected $deliveryemailRepository;

    /**
     * @param QuoteIdMaskFactory              $quoteIdMaskFactory
     * @param DeliveryemailRepositoryInterface $deliveryemailRepository
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        DeliveryemailRepositoryInterface $deliveryemailRepository
    ) {
        $this->quoteIdMaskFactory     = $quoteIdMaskFactory;
        $this->deliveryemailRepository = $deliveryemailRepository;
    }

    /**
     * @param string                $cartId
     * @param  DeliveryemailInterface $deliveryemail
     * @return DeliveryemailInterface
     */
    public function saveDeliveryemail(
        string $cartId,
        DeliveryemailInterface $deliveryemail
    ): DeliveryemailInterface {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->deliveryemailRepository->saveDeliveryemail((int)$quoteIdMask->getQuoteId(), $deliveryemail);
    }
}
