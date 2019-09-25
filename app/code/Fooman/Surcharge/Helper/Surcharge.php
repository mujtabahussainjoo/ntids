<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\Surcharge\Helper;

class Surcharge
{
    const XML_PATH_DISPLAY_SALES_SURCHARGE = 'tax/sales_display/fooman_surcharge';
    const XML_PATH_DISPLAY_SALES_SURCHARGE_ZERO = 'tax/sales_display/fooman_surcharge_zero';

    /**
     * @var \Fooman\Surcharge\Model\ResourceModel\Surcharge\CollectionFactory
     */
    private $surchargeCollectionFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param \Fooman\Surcharge\Model\ResourceModel\Surcharge\CollectionFactory $surchargeCollectionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface                $scopeConfig
     */
    public function __construct(
        \Fooman\Surcharge\Model\ResourceModel\Surcharge\CollectionFactory $surchargeCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->surchargeCollectionFactory = $surchargeCollectionFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param  string $typeId
     *
     * @return string
     */
    public function getTypeFromTypeId($typeId)
    {
        return preg_replace('/[0-9]*$/', '', $typeId);
    }

    /**
     * @param  string $typeId
     *
     * @return string
     */
    public function getIdFromTypeId($typeId)
    {
        $chars = str_split($typeId);
        $result = '';
        foreach ($chars as $char) {
            if (is_numeric($char)) {
                $result .= $char;
            }
        }

        if (strlen($result) > 0) {
            return $result;
        }
    }

    /**
     * @param  string $typeId
     *
     * @return \Fooman\Surcharge\Api\SurchargeInterface
     */
    public function getSurchargeByTypeId($typeId)
    {
        $surchargeId = $this->getIdFromTypeId($typeId);

        /** @var \Fooman\Surcharge\Api\SurchargeInterface $surcharge */
        $surcharge = $this->surchargeCollectionFactory->create()
                                                      ->addFieldToFilter('id', $surchargeId)
                                                      ->setPageSize(1)
                                                      ->getFirstItem();

        return $surcharge;
    }

    /**
     * @param  string $typeId
     *
     * @return string
     */
    public function getSurchargeTaxClassIdByTypeId($typeId)
    {
        $surcharge = $this->getSurchargeByTypeId($typeId);
        return $surcharge->getTaxClassId();
    }

    public function isSalesDisplayedTaxInclusive($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DISPLAY_SALES_SURCHARGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        ) == \Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX;
    }

    public function isSalesDisplayedTaxExclusive($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DISPLAY_SALES_SURCHARGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        ) == \Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    public function isSalesDisplayedBoth($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DISPLAY_SALES_SURCHARGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        ) == \Magento\Tax\Model\Config::DISPLAY_TYPE_BOTH;
    }

    public function isSalesZeroDisplayed($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DISPLAY_SALES_SURCHARGE_ZERO,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
