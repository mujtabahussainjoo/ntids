<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Wizkunde\WebSSO\Model;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\AuthenticationInterface;
use Wizkunde\WebSSO\Helper\Eav;

class CustomerManagement
{
    private $customer;
    private $accountManagement;
    private $customerRepository;
    private $addressRepository;
    private $authentication;
    private $eventManager;
    private $eavHelper;

    private $optionalAddressFields = [
        'billing'   => [
            'billing_firstname',
            'billing_lastname',
            'billing_company',
            'billing_region',
            'billing_region_id',
            'billing_vat'
        ],
        'shipping'  => [
            'shipping_firstname',
            'shipping_lastname',
            'shipping_company',
            'shipping_region',
            'shipping_region_id',
            'shipping_vat'
        ]
    ];

    /**
     * CustomerManagement constructor.
     *
     * @param CustomerInterface $customer
     * @param AccountManagementInterface $accountManagement
     * @param CustomerRepositoryInterface $customerRepository
     * @param AddressRepositoryInterface $addressRepository
     * @param AuthenticationInterface $authentication
     * @param ManagerInterface $manager
     * @param Eav $eavHelper
     */
    public function __construct(
        CustomerInterface $customer,
        AccountManagementInterface $accountManagement,
        CustomerRepositoryInterface $customerRepository,
        AddressRepositoryInterface $addressRepository,
        AuthenticationInterface $authentication,
        ManagerInterface $manager,
        Eav $eavHelper
    ) {
        $this->customer = $customer;
        $this->accountManagement = $accountManagement;
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
        $this->authentication = $authentication;
        $this->eventManager = $manager;
        $this->eavHelper = $eavHelper;
    }

    /**
     * @return CustomerRepositoryInterface
     */
    public function getCustomerRepository()
    {
        return $this->customerRepository;
    }

    /**
     * @return AddressRepositoryInterface
     */
    public function getAddressRepository()
    {
        return $this->addressRepository;
    }

    /**
     * @return \Magento\Customer\Model\AuthenticationInterface
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }

    /**
     * @return AccountManagementInterface
     */
    public function getAccountManagement()
    {
        return $this->accountManagement;
    }

    /**
     * @return ManagerInterface
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * @param $email
     * @return bool
     */
    public function isExistingCustomer($email)
    {
        return ($this->getAccountManagement()->isEmailAvailable($email) === false);
    }

    /**
     * @param array $userData
     * @return bool|CustomerInterface
     * @throws InvalidEmailOrPasswordException
     */
    public function getCustomer(array $userData)
    {
        if ($this->isExistingCustomer($userData['email']) === false) {
            if($this->isComplete($userData) == false) {
                throw new LocalizedException('Cannot create user account, missing mappings: ' . $this->getMissingMappings($userData));
                return false;
            }

            $this->customer->setEmail($userData['email']);
            $this->customer->setFirstname($userData['firstname']);
            $this->customer->setLastname($userData['lastname']);

            if (isset($userData['group_id'])) {
                $this->customer->setGroupId($userData['group_id']);
            }

            if (isset($userData['website_id'])) {
                $this->customer->setWebsiteId($userData['website_id']);
            }

            if (isset($userData['password']) && $userData['password'] != '') {
                $this->getAccountManagement()->createAccountWithPasswordHash($this->customer, $userData['password']);
            } else {
                $this->getAccountManagement()->createAccount($this->customer);
            }
        }

        try {
            $customer = $this->getCustomerRepository()->get($userData['email']);
        } catch (NoSuchEntityException $e) {
            throw new InvalidEmailOrPasswordException(__('Invalid login or password.'));
        }

        $this->updateAddress($customer, 'billing', $userData);
        $this->updateAddress($customer, 'shipping', $userData);

        return $customer;
    }

    /**
     * @param array $userData
     * @return bool
     */
    public function canAccountBeCreated(array $userData)
    {
        if (!isset($userData['email']) || !isset($userData['firstname']) || !isset($userData['lastname'])) {
            return false;
        }

        return true;
    }

    /**
     * @param array $userData
     * @return bool
     */
    public function isComplete(array $userData)
    {
        if (!isset($userData['email'])) {
            return false;
        }

        if (!isset($userData['firstname'])) {
            return false;
        }

        if (!isset($userData['lastname'])) {
            return false;
        }

        return true;
    }

    /**
     * @param array $userData
     * @return array
     */
    public function getMissingMappings(array $userData)
    {
        $missing = array();

        if (!isset($userData['email'])) {
            $missing[] = 'email';
        }

        if (!isset($userData['firstname'])) {
            $missing[] = 'firstname';
        }

        if (!isset($userData['lastname'])) {
            $missing[] = 'lastname';
        }

        return $missing;
    }

    private function updateAddress($customer, $type = 'billing', $userData = [])
    {
        switch ($type) {
            case 'billing':
                $address = $this->getAccountManagement()->getDefaultBillingAddress($customer->getId());
                break;
            default:
                $address = $this->getAccountManagement()->getDefaultShippingAddress($customer->getId());
                break;
        }

        foreach ($this->eavHelper->getCustomerAddressAttributes($type) as $attribute) {
            if (!isset($userData[$attribute['code']]) && !isset($this->optionalAddressFields[$type][$attribute['code']])) {
                return false;
            }
        }


        // We may fall back on the original firstname/lastname to save the admin some extra settings
        $firstname = (isset($userData[$type . '_firstname'])) ? $userData[$type . '_firstname'] : $userData['firstname'];
        $lastname = (isset($userData[$type . '_lastname'])) ? $userData[$type . '_lastname'] : $userData['lastname'];

        $address->setFirstname($firstname);
        $address->setLastname($lastname);
        $address->setStreet($userData[$type . '_street']);
        $address->setPostcode($userData[$type . '_postcode']);
        $address->setCity($userData[$type . '_city']);
        $address->setTelephone($userData[$type . '_telephone']);

        if (isset($userData[$type . '_country_id'])) {
            $address->setCountryId($userData[$type . '_country_id']);
        }

        if (isset($userData[$type . '_vat_id'])) {
            $address->setVatId($userData[$type . '_vat_id']);
        }

        if (isset($userData[$type . '_region'])) {
            $address->setRegionId($userData[$type . '_region']);
        }

        if (isset($userData[$type . '_company'])) {
            $address->setCompany($userData[$type . '_company']);
        }

        $this->getAddressRepository()->save($address);
    }

    /**
     * @param CustomerInterface $customer
     * @return CustomerInterface
     * @throws UserLockedException
     */
    public function loginCustomer(CustomerInterface $customer)
    {
        $customerId = $customer->getId();
        if ($this->getAuthentication()->isLocked($customerId)) {
            throw new UserLockedException(__('The account is locked.'));
        }


        $this->getEventManager()->dispatch('customer_data_object_login', ['customer' => $customer]);

        return $customer;
    }
}
