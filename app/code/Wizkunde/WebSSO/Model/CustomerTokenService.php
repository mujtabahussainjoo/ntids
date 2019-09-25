<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Wizkunde\WebSSO\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Integration\Model\CredentialsValidator;
use Magento\Integration\Model\Oauth\TokenFactory as TokenModelFactory;
use Magento\Integration\Model\ResourceModel\Oauth\Token\CollectionFactory as TokenCollectionFactory;
use Magento\Framework\Exception\AuthenticationException;

class CustomerTokenService implements \Wizkunde\WebSSO\Api\CustomerTokenServiceInterface
{
    /**
     * Token Model
     *
     * @var TokenModelFactory
     */
    private $tokenModelFactory;

    /**
     * Customer Account Service
     *
     * @var CustomerManagement
     */
    private $customerManagement;

    /**
     * Token Collection Factory
     *
     * @var TokenCollectionFactory
     */
    private $tokenModelCollectionFactory;

    /**
     * Initialize service
     *
     * @param TokenModelFactory $tokenModelFactory
     * @param CustomerManagement $customerManagement
     * @param TokenCollectionFactory $tokenModelCollectionFactory
     */
    public function __construct(
        TokenModelFactory $tokenModelFactory,
        CustomerManagement $customerManagement,
        TokenCollectionFactory $tokenModelCollectionFactory,
        CredentialsValidator $validatorHelper
    ) {
        $this->tokenModelFactory = $tokenModelFactory;
        $this->customerManagement = $customerManagement;
        $this->tokenModelCollectionFactory = $tokenModelCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createCustomerAccessToken($userData)
    {
        try {
            $customer = $this->customerManagement->getCustomer($userData);
            $customerDataObject = $this->customerManagement->loginCustomer($customer);
        } catch (\Exception $e) {
            throw new AuthenticationException(
                __('You did not sign in correctly or your account is temporarily disabled.')
            );
        }
        return $this->tokenModelFactory->create()->createCustomerToken($customerDataObject->getId())->getToken();
    }

    /**
     * {@inheritdoc}
     */
    public function revokeCustomerAccessToken($customerId)
    {
        $tokenCollection = $this->tokenModelCollectionFactory->create()->addFilterByCustomerId($customerId);
        if ($tokenCollection->getSize() == 0) {
            throw new LocalizedException(__('This customer has no tokens.'));
        }
        try {
            foreach ($tokenCollection as $token) {
                $token->setRevoked(1)->save();
            }
        } catch (AuthenticationException $e) {
            throw new LocalizedException(__('The tokens could not be revoked.'));
        }
        return true;
    }
}
