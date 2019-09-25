<?php

namespace Wizkunde\WebSSO\Api;

/**
 * Expose login and logout via API
 *
 * Interface ConnectionInterface
 * @package Wizkunde\WebSSO\Api
 */
interface ConnectionInterface
{
    /**
     * @api
     * @return mixed[]
     */
    public function getLoginRequest();

    /**
     * @api
     * @param mixed $responseData
     * @return mixed
     */
    public function getCustomerToken($responseData);

    /**
     * @param string $customerId
     * @return mixed
     */
    public function revokeCustomerToken($customerId);

    /**
     * @api
     * @return mixed[]
     */
    public function logout();
}
