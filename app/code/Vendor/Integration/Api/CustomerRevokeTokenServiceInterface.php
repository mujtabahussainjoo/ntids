<?php

namespace Vendor\Integration\Api;
/**
 * Interface CustomerRevokeTokenServiceInterface
 * @package Vendor\Integration\Api
 */
interface  CustomerRevokeTokenServiceInterface 
{
    /**
     * Revoke token by customer id.
     *
     * @api
     * @param int $customerId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function revokeCustomerAccessToken($customerId);
}