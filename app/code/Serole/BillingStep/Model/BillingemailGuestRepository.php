<?php

declare(strict_types=1);

namespace Serole\BillingStep\Model;

use Magento\Quote\Model\QuoteIdMaskFactory;
use Serole\BillingStep\Api\BillingemailGuestRepositoryInterface;
use Serole\BillingStep\Api\BillingemailRepositoryInterface;
use Serole\BillingStep\Api\Data\BillingemailInterface;
use Serole\BillingStep\Model\BillingemailRepository;

/**
 * Class BillingemailGuestRepository
 *
 * @category Model/Repository
 * @package  Serole\BillingStep\Model
 */
class BillingemailGuestRepository implements BillingemailGuestRepositoryInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var BillingemailRepositoryInterface
     */
    protected $billingemailRepository;

    /**
     * @param QuoteIdMaskFactory              $quoteIdMaskFactory
     * @param BillingemailRepositoryInterface $billingemailRepository
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        BillingemailRepositoryInterface $billingemailRepository
    ) {
        $this->quoteIdMaskFactory     = $quoteIdMaskFactory;
        $this->billingemailRepository = $billingemailRepository;
    }

    /**
     * @param string                $cartId
     * @param  BillingemailInterface $billingemail
     * @return BillingemailInterface
     */
    public function saveBillingemail(
        string $cartId,
        BillingemailInterface $billingemail
    ):  BillingemailInterface {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->billingemailRepository->saveBillingemail((int)$quoteIdMask->getQuoteId(), $billingemail);
    }
}
