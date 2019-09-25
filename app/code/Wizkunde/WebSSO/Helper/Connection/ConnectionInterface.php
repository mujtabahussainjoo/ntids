<?php

namespace Wizkunde\WebSSO\Connection;

interface ConnectionInterface
{
    /**
     * Authenticate with the auth resource
     * @return mixed
     */
    public function authenticate();

    /**
     * Logout from the SSO connection
     * @return mixed
     */
    public function logout();

    /**
     * Fetch the User Data needed to login
     * @return mixed
     */
    public function getUserData();

    /**
     * Store the SSO Session Details
     * @return mixed
     */
    public function persistSession();
}
