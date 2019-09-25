<?php
/**
* Magedelight
* Copyright (C) 2017 Magedelight <info@magedelight.com>
*
* @category Magedelight
* @package Magedelight_MembershipSubscription
* @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
* @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
* @author Magedelight <info@magedelight.com>
 */
namespace Magedelight\MembershipSubscription\Ui\Component\Listing\MembershipCustomer\Column;

use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Directory\Model\CountryFactory;

/**
 * Class Store.
 */
class Country extends Column
{
    
    /**
     * Country Id
     *
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $_countryFactory;
    
    /**
     * Escaper.
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    
    /**
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CountryFactory $countryFactory
     * @param Escaper $escaper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CountryFactory $countryFactory,
        Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        $this->_countryFactory = $countryFactory;
        $this->escaper = $escaper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item['billing_country_id'] = $this->prepareItem($item);
            }
        }
        
        return $dataSource;
    }

    /**
     * Get data.
     *
     * @param array $item
     *
     * @return string
     */
    protected function prepareItem(array $item)
    {
       
        $content = '';
        $countryCode = $item['billing_country_id'];
        if (!empty($countryCode)) {
            $country = $this->_countryFactory->create()->loadByCode($countryCode);
            return $country->getName();
        }
        return $content;
    }
}
