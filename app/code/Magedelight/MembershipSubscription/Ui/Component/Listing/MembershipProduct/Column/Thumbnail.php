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

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterfaceFactory;
use Magento\Framework\UrlInterface;

class Thumbnail extends Column
{
    /**
     * const fiels name
     */
    const FIELD_NAME = 'thumbnail';
    
    /**
     *
     * @var Magento\Catalog\Api\ProductRepositoryInterfaceFactory
     */
    protected $_productRepositoryFactory;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $_storeManager;
    
    /**
     *
     * @param ContextInterface $context
     * @param StoreManagerInterface $storemanager
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param ProductRepositoryInterfaceFactory $productRepositoryFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        StoreManagerInterface $storemanager,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        ProductRepositoryInterfaceFactory $productRepositoryFactory,
        array $components = [],
        array $data = []
    ) {
        $this->_storeManager = $storemanager;
        $this->_productRepositoryFactory = $productRepositoryFactory;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = self::FIELD_NAME;

            foreach ($dataSource['data']['items'] as & $item) {
                $url = '';
                if ($this->prepareItem($item)) {
                    $url = $this->prepareItem($item);
                }
                $item[$fieldName . '_src'] = $url;
                $item[$fieldName . '_alt'] = $this->getAlt($item) ?: '';
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'Grid/membership_product_id/edit',
                    ['membership_product_id' => $item['membership_product_id']]
                );
                $item[$fieldName . '_orig_src'] = $url;
            }
        }
        
        return $dataSource;
    }

    /**
     * @param array $row
     *
     * @return null|string
     */
    protected function getAlt($row)
    {
        $altField = $this->getData('config/altField') ?: self::ALT_FIELD;
        return isset($row[$altField]) ? $row[$altField] : null;
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
        $product_id = $item['product_id'];
        
        if (!empty($product_id)) {
            $product = $this->_productRepositoryFactory->create()->getById($product_id);
            
            $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            );
        
            $imageUrl = $mediaDirectory.'/catalog/product'.$product->getData('thumbnail');
            
            return $imageUrl;
        }
    }
}
