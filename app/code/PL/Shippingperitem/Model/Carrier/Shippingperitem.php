<?php
/**
 * PL Development.
 *
 * @category    PL
 * @author      Linh Pham <plinh5@gmail.com>
 * @copyright   Copyright (c) 2016 PL Development. (http://www.polacin.com)
 */
namespace PL\Shippingperitem\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use \Magento\Shipping\Model\Carrier\AbstractCarrier;

class Shippingperitem extends AbstractCarrier
{
    const PL_SHIPPING_ATTRIBUTE_CODE = 'shipping_per_item';

    protected $_code = 'shippingperitem';

    protected $_productFactory;

    protected $_logger;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Catalog\Model\ProductFactory  $productFactory,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_productFactory = $productFactory;
        $this->_logger = $logger;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }


    public function collectRates(RateRequest $request)
    {
        // skip if not enabled
        if (!$this->getConfigData('active')) {
            return false;
        }
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();

        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create();

        // record carrier information
        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        // record method information
        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));
        // load all products and check if they have a specific shipping data
        $ids = $this->getApplicapleItemIds($request);
        $collection = $this->_productFactory->create()->getCollection();
        $collection->addIdFilter(array_keys($ids));
        $collection->joinAttribute(
            self::PL_SHIPPING_ATTRIBUTE_CODE,
            'catalog_product/'.self::PL_SHIPPING_ATTRIBUTE_CODE,
            'entity_id',
            null,
            'left'
        );

        $price = floatVal($this->getConfigData('base_rate'));
        $hasIndividualRate = false;
        $rates = array();
        foreach ($collection as $product) {
            $rate = 0;
            if ($product->getData(self::PL_SHIPPING_ATTRIBUTE_CODE) > 0.00001) {
                $rate  = $product->getData(self::PL_SHIPPING_ATTRIBUTE_CODE);
                $hasIndividualRate = true;
            } elseif ($this->getConfigData('use_default_rate')) {
                $rate  = $this->getConfigData('default_rate');
            }
            $qty = 1;
            if ($this->getConfigData('calc_separately')) {
                $qty = $ids[$product->getId()];
            }
            $rates[] = (1.0 * $rate * $qty);
        }

        //$price = 0;
        if ($this->getConfigData('use_max')) {
            $price += max($rates);
        } else {
            $price += array_sum($rates);
        }

        if (!$hasIndividualRate && $this->getConfigData('individual_rate_only')) {
            return false;
        }

        // bounding
        $minBound = $this->getConfigData('min');
        if ($minBound) {
            $price = max($minBound, $price);
        }
        $maxBound = $this->getConfigData('max');
        if ($maxBound) {
            $price = min($maxBound, $price);
        }

      /*  if ($request->getFreeShipping() === true) {
            $price = 0.0;
        }*/

        $method->setCost($price);
        $method->setPrice($price);
        // add this rate to the result
        $result->append($method);
        return $result;
    }

    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    protected function getApplicapleItemIds($request)
    {
        // loop throug all items in the cart and collect applicapable items
        $ids = [];
        foreach ($request->getAllItems() as $item) {
            if ($item->getParentItem() || $item->getProduct()->isVirtual()) {
                continue;
            }
            if ($item->getHasChildren() && $item->isShipSeparately()) {
                foreach ($item->getChildren() as $child) {
                    if ($child->getFreeShipping() || $child->getProduct()->isVirtual()) {
                        continue;
                    }
                    $id = $child->getProduct()->getId();
                    if (isset($ids[$id])) {
                        $ids[$id] += $child->getQty();
                    } else {
                        $ids[$id] = $child->getQty();
                    }
                }
            } elseif (!$item->getFreeShipping()) {
                $id = $item->getProduct()->getId();
                if (isset($ids[$id])) {
                    $ids[$id] += $item->getQty();
                } else {
                    $ids[$id] = $item->getQty();
                }
            }
        }
        return $ids;
    }
}
