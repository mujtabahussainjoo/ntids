<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eecom\Connector\Api;
/**
 * Interface WishListMethodManagementInterface
 * @api
 */
interface WishListMethodManagementInterface
{
    /**
     * Return Product added to wishlist true/false.
     *
     * @api
     * @param int $customerId
     * @param int $productId
     * @return  \Eecom\Connector\Api\WishListMethodManagementInterface true/false
     */
    public function add($customerId, $productId);
    /**
     * Return Product removed from wishlist true/false.
     *
     * @api
     * @param int $customerId
     * @param int $itemId
     * @return \Eecom\Connector\Api\WishListMethodManagementInterface true/false
     */
    public function remove($customerId, $itemId);
}
