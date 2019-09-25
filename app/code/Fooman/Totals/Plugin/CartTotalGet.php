<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Totals
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Totals\Plugin;

class CartTotalGet
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Framework\Registry                $registry
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Registry $registry
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->registry = $registry;
    }

    /**
     * Workaround for Magento\Quote\Model\Cart\CartTotalRepository attempting to copy
     * extension attributes of incompatible types.
     *
     * @param  \Magento\Quote\Api\CartTotalRepositoryInterface $subject
     * @param  \Closure                                        $proceed
     * @param  int                                             $cartId
     *
     * @return \Magento\Quote\Api\Data\TotalsInterface Quote totals data.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundGet(
        \Magento\Quote\Api\CartTotalRepositoryInterface $subject,
        \Closure $proceed,
        $cartId
    ) {
        /** @var \Magento\Quote\Api\Data\CartInterface $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if ($quote->isVirtual()) {
            $extensionAttributes = $quote->getBillingAddress()->getExtensionAttributes();
            $quote->getBillingAddress()->unsExtensionAttributes();
        } else {
            $extensionAttributes = $quote->getShippingAddress()->getExtensionAttributes();
            $quote->getShippingAddress()->unsExtensionAttributes();
        }

        $this->registry->register('fooman_totals_quote_address_extension_attributes', $extensionAttributes);
        /** @var \Magento\Quote\Api\Data\TotalsInterface $result */
        $result = $proceed($cartId);
        $this->registry->unregister('fooman_totals_quote_address_extension_attributes');
        if ($extensionAttributes) {
            if ($quote->isVirtual()) {
                $quote->getBillingAddress()->setExtensionAttributes($extensionAttributes);
            } else {
                $quote->getShippingAddress()->setExtensionAttributes($extensionAttributes);
            }
        }

        return $result;
    }
}
