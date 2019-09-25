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

namespace Magedelight\MembershipSubscription\Ui\Component\Listing\MembershipProduct\Column;

use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Catalog\Model\ProductFactory;

/**
 * Class Store.
 */
class Status extends Column
{
    
    /**
     * Escaper.
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     *
     * @var Magento\Catalog\Api\ProductRepositoryInterfaceFactory
     */
    protected $_productloader;
    
    /**
     * Constructor.
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Escaper            $escaper
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ProductFactory $_productloader,
        Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        $this->escaper = $escaper;
         $this->_productloader = $_productloader;
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
                $item['status'] = $this->prepareItem($item);
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
        $product_id = $item['product_id'];
        if (!empty($product_id)) {
            $product = $this->_productloader->create()->load($product_id);
            $status = $product->getStatus();

            if (empty($status) && !isset($status)) {
                return '';
            }
            
            if ($status == 1) {
                $content .=  '<span class="grid-severity-notice"><span>Enabled</span></span>';
            } else {
                $content .=  '<span class="grid-severity-critical"><span>Disabled</span></span>';
            }
        }

        return $content;
    }
}
