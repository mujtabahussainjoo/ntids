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
use Magento\Eav\Model\Config;

/**
 * Class Store.
 */
class Gender extends Column
{
    /**
     * eav config
     *
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;
    
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
     * @param Config $eavConfig
     * @param Escaper $escaper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Config $eavConfig,
        Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        $this->eavConfig = $eavConfig;
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
                $item['gender'] = $this->prepareItem($item);
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
        $gender = $item['gender'];
        if (!empty($gender)) {
            $attribute = $this->eavConfig->getAttribute('customer', 'gender');
            $options = $attribute->getSource()->getAllOptions();
            
            $key = array_search($gender, array_column($options, 'value'));
            
            return $options[$key]['label'];
        }
        return $content;
    }
}
