<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Catalog breadcrumbs
 */
namespace Serole\ExternalProduct\Block;


use Magento\Catalog\Helper\Data;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\Store;
class Breadcrumbs extends \Magento\Framework\View\Element\Template
{
    /**
     * Catalog data
     *
     * @var Data
     */
    protected $_catalogData = null;
    /**
     * @param Context $context
     * @param Data $catalogData
     * @param array $data
     */
    public function __construct(Context $context, 
                    Data $catalogData, 
                    \Magento\Framework\Registry $registry,
                    \Magento\Framework\ObjectManagerInterface $objectmanager,
                    array $data = [])
    {
        $this->_catalogData = $catalogData;
        $this->_coreRegistry = $registry;
        $this->_objectManager = $objectmanager;
        parent::__construct($context, $data);
    }
    /**
     * Retrieve HTML title value separator (with space)
     *
     * @param null|string|bool|int|Store $store
     * @return string
     */
    public function getTitleSeparator($store = null)
    {
        $separator = (string)$this->_scopeConfig->getValue('catalog/seo/title_separator', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        return ' ' . $separator . ' ';
    }
    public function getCategory ($product) {   
        $_categoryFactory = $this->_objectManager->create('Magento\Catalog\Model\CategoryFactory');
        // for multiple categories, select only the first one
        // remember, index = 0 is 'Default' category
        if (! $product->getCategoryIds())
            return null;
        if (isset ( $product->getCategoryIds()[1]))
            $categoryId = $product->getCategoryIds()[1] ;
        else
            $categoryId = $product->getCategoryIds()[0] ;
        // Additionally for other types of attributes (select, multiselect, ..)
        $category = $_categoryFactory->create()->load($categoryId);
       return ['label' => $category->getName(), 'url' => $category->getUrl() ] ;
        
    }
    /**
     * Preparing layout
     *
     * @return \Magento\Catalog\Block\Breadcrumbs
     */
    protected function _prepareLayout()
    {
        $product = $this->_coreRegistry->registry('current_product'); 
        //return parent::_prepareLayout();
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );
            $title = [];
            $path = $this->_catalogData->getBreadcrumbPath();
            
            // If we are at the product page and the $path does not include a category, 
            // then we will append the category link  here manually
            // Magento doesn't seem to append category paths to breadcrums consistently
            // Reported here; https://github.com/magento/magento2/issues/7967
            if($product != null ) {
                // check for category path
                $foundCatPath=false;
                foreach ($path as $name => $breadcrumb) {
                    if ( strpos ( $name, 'category' ) > -1 )  
                        $foundCatPath=true;
                }
                // append the category path
                if (! $foundCatPath) {
                    $productCategory = $this->getCategory($product) ;
                    if ($productCategory) {
                        $categoryPath = [ 'category' => ['label' =>  $productCategory['label'] , 'link' =>  $productCategory['url']]  ];
                        $path = array_merge ($categoryPath ,$path );
                    } 
                }
                
            }
            foreach ($path as $name => $breadcrumb) {
                $breadcrumbsBlock->addCrumb($name, $breadcrumb);
                $title[] = $breadcrumb['label'];
            }
             //print_r ($path );
            // die ();
            $this->pageConfig->getTitle()->set(join($this->getTitleSeparator(), array_reverse($title)));
        }
        return parent::_prepareLayout();
    }
}
