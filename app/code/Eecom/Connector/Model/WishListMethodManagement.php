<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eecom\Connector\Model;
use Eecom\Connector\Api\WishListMethodManagementInterface;
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
    public function __construct(
         \Magento\Framework\ObjectManagerInterface $objectManager, 
        \Magento\Wishlist\Model\WishlistFactory $wishlistRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Wishlist\Model\ResourceModel\Item\Collection $itemRepository,
        \Magento\Wishlist\Model\Wishlist $wishlist1
    ) {
        //$this->_objectManager = 'Eecom\Connector\Api\WishListMethodManagementInterface';  
        $this->_objectManager = $objectManager;
        $this->_wishlistRepository= $wishlistRepository;
        $this->_productRepository = $productRepository;
        $this->_itemRepository = $itemRepository;    
        $this->wishlist1 = $wishlist1;
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
                    return $e->getMessage();
                    return false;
                }
                $wishlist = $this->_wishlistRepository->create()->loadByCustomerId($customerId, true);
                $wishlist->addNewItem($product);
                $wishlist->save();
               return true;
            }else{
                return false;
            }      
         }catch (\Exception $e) {
                return false;
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
            
            if(!empty($itemId)){
               $wishlist = $this->wishlist1->loadByCustomerId($customerId);
               $items = $wishlist->getItemCollection();
                if (!$wishlist) {
                     return false;
                }
                try{
					foreach ($items as $item) {
                        if ($item->getProductId() == $itemId) {
                            $item->delete();
                            $wishlist->save();
                            return true;
                        }
                    }
                    
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
