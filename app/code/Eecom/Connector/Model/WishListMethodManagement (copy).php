<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eecom\Connector\Model;
use Eecom\Connector\Api\WishListMethodManagementInterface;

use Magento\Wishlist\Controller\WishlistProvider;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Helper\ImageFactory as ProductImageHelper;
use Magento\Store\Model\App\Emulation as AppEmulation;
class WishListMethodManagement implements WishListMethodManagementInterface
{
     /**
     * Object manager
     *
     * @var Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * Wishlist Repository.
     *
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $_wishlistRepository;
    /**
     * Product Repository.
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $_productRepository;
     /**
     * Item Repository.
     *
     * @var \Magento\Wishlist\Model\ResourceModel\Item\Collection
     */
     
    private $wishlist1;
     
    protected $_itemRepository;
    protected $_wishlistCollectionFactory;
    
    
    
     /**
     * @var CollectionFactory
     */

    /**
     * Wishlist item collection
     *
     * @var \Magento\Wishlist\Model\ResourceModel\Item\Collection
     */
    protected $_itemCollection;

    


    /**
     * @var WishlistFactory
     */
    protected $_wishlistFactory;

    /**
     * @var Item
     */
    protected $_itemFactory;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     *@var \Magento\Catalog\Helper\ImageFactory
     */
    protected $productImageHelper;

    /**
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storemanagerinterface;

    /**
     *@var \Magento\Store\Model\App\Emulation
     */
    protected $appEmulation;

    /**
     *@var \Magento\Catalog\Model\Product
     */
    protected $_productload;

    /**
     *@var \Magento\Directory\Model\CountryFactory
     */
    protected $countryfactory;

    
    
    public function __construct(
         \Magento\Framework\ObjectManagerInterface $objectManager, 
        \Magento\Wishlist\Model\WishlistFactory $wishlistRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Wishlist\Model\ResourceModel\Item\Collection $itemRepository,
        \Magento\Wishlist\Model\Wishlist $wishlist1,
        CollectionFactory $wishlistCollectionFactory,
        WishlistFactory $wishlistFactory,
        \Magento\Customer\Model\Customer $customer,
        AppEmulation $appEmulation,
        \Magento\Directory\Model\CountryFactory $countryfactory,
        \Magento\Store\Model\StoreManagerInterface $storemanagerinterface,
        ProductImageHelper $productImageHelper,
        \Magento\Catalog\Model\Product $productload,
        \Magento\Wishlist\Model\ItemFactory $itemFactory
    ) {
        //$this->_objectManager = 'Eecom\Connector\Api\WishListMethodManagementInterface';  
        $this->_objectManager = $objectManager;
        $this->_wishlistRepository= $wishlistRepository;
        $this->_productRepository = $productRepository;
        $this->_itemRepository = $itemRepository;    
        $this->wishlist1 = $wishlist1;
        $this->_wishlistCollectionFactory = $wishlistCollectionFactory;
        $this->_wishlistRepository = $wishlistRepository;
        $this->_productRepository = $productRepository;
        $this->_wishlistFactory = $wishlistFactory;
        $this->countryfactory = $countryfactory;
        $this->storemanagerinterface = $storemanagerinterface;
        $this->_itemFactory = $itemFactory;
        $this->_customer = $customer;
        $this->_productload = $productload;
        $this->appEmulation = $appEmulation;
        $this->productImageHelper = $productImageHelper;
        $this->_customer = $customer;
     }
    /**
     * Return Product added to wishlist true/false.
     *
     * @api
     * @param int $customerId
     * @param int $productId
     * @return  \Eecom\Connector\Model\WishListMethodManagement true/false
     */
    public function add($customerId, $productId){
        try{
            if($customerId && $productId){
				
                try {
                    $product = $this->_productRepository->getById($productId);
                } catch (NoSuchEntityException $e) {
                    $product = null;
                    return false;
                }
                
                $wishlist = $this->_wishlistRepository->create()->loadByCustomerId($customerId, true);
                $wishlist->addNewItem($product);
                $wishlist->save();
                
                
				$collection = $this->_wishlistCollectionFactory->create()->addCustomerIdFilter($customerId);
				$baseurl = $this->storemanagerinterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'catalog/product';
				
				$wishlistData = [];
				foreach ($collection as $item) {
					$productInfo = $item->getProduct()->toArray();
					if ($productInfo['small_image'] == 'no_selection') {
					  $currentproduct = $this->_productload->load($productInfo['entity_id']);
					  $imageURL = $this->getImageUrl($currentproduct, 'product_base_image');
					  $productInfo['small_image'] = $imageURL;
					  $productInfo['thumbnail'] = $imageURL;
					}else{
					  $imageURL = $baseurl.$productInfo['small_image'];
					  $productInfo['small_image'] = $imageURL;
					  $productInfo['thumbnail'] = $imageURL;
					}
					
					// $item->getProduct()->getName();
					
					$data = [
						//"wishlist_item_id" => $item->getWishlistItemId(),
						//"wishlist_id"      => $item->getWishlistId(),
						"product_id"       => $item->getProductId(),
						//"store_id"         => $item->getStoreId(),
						//"added_at"         => $item->getAddedAt(),
						"description"      => $item->getDescription(),
						//"qty"              => round($item->getQty()),
						//"product"          => $productInfo
					];
					$wishlistData[] = $data;
				}
				return $wishlistData;
            }else{
                return false;
            }      
         }catch (\Exception $e) {
                return 1;
        }        
    }
    /**
     * Return item removed from wishlist true/false
     *
     * @api
     * @param int $customerId
     * @param int $itemId
     * @return  \Eecom\Connector\Model\WishListMethodManagement true/false
     */
    public function remove($customerId, $itemId){
        try{
            if($itemId){
                $item = $this->_objectManager->create('Magento\Wishlist\Model\Item')->load($itemId);
                if (!$item->getId()) {
                    return false;
                }
               $wishlist = $this->_wishlistRepository->create()->loadByCustomerId($customerId, true);
                if (!$wishlist) {
                     return false;
                }
                try{
                    $item->delete();
                    $wishlist->save();
                    return true;
                }catch (\Exception $e) {
                   return false;
                } 
            }else{
                return false;
            }
         }catch (\Exception $e) {
                return false;
        }
    }
}
