<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Helpdesk\Helper;

/**
 * Class Customer.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Customer extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory
     */
    protected $orderAddressCollectionFactory;

    /**
     * @param \Magento\Eav\Model\Entity\AttributeFactory                         $entityAttributeFactory
     * @param \Magento\Customer\Model\CustomerFactory                            $customerFactory
     * @param \Magento\Sales\Model\OrderFactory                                  $orderFactory
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory   $customerCollectionFactory
     * @param \Mirasvit\Helpdesk\Model\SearchFactory                             $searchFactory
     * @param \Mirasvit\Helpdesk\Helper\Order                                    $helperOrder
     * @param \Magento\Framework\App\ResourceConnection                          $resource
     * @param \Magento\Framework\App\Helper\Context                              $context
     * @param \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $orderAddressCollectionFactory
     * @param \Magento\Customer\Model\Session                                    $customerSession
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Eav\Model\Entity\AttributeFactory $entityAttributeFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Mirasvit\Helpdesk\Model\SearchFactory $searchFactory,
        \Mirasvit\Helpdesk\Helper\Order $helperOrder,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $orderAddressCollectionFactory,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->entityAttributeFactory = $entityAttributeFactory;
        $this->customerFactory = $customerFactory;
        $this->searchFactory = $searchFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->orderAddressCollectionFactory = $orderAddressCollectionFactory;
        $this->context = $context;
        $this->customerSession = $customerSession;
        $this->orderFactory = $orderFactory;
        $this->helperOrder = $helperOrder;
        $this->resource = $resource;
        parent::__construct($context);
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Email $email
     *
     * @return \Magento\Framework\DataObject
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerByEmail(\Mirasvit\Helpdesk\Model\Email $email)
    {
        $customers = $this->customerCollectionFactory->create();
        $customers
            ->addAttributeToSelect('*')
            ->addFieldToFilter('email', $email->getFromEmail());
        if ($email->getGateway()) {
            $customers->addFieldToFilter('store_id', $email->getGateway()->getStoreId());
        }
        if ($customers->count()) {
            return $customers->getFirstItem();
        }

        // customer may be registered in store A, but sends email to gateway of store B
        $customers = $this->customerCollectionFactory->create();
        $customers
            ->addAttributeToSelect('*')
            ->addFieldToFilter('email', $email->getFromEmail());
        if ($customers->count()) {
            return $customers->getFirstItem();
        }

        /** @var \Magento\Customer\Model\Customer $address */
        $address = $customers->getLastItem();
        if ($address->getId()) {
            $customer = new \Magento\Framework\DataObject();
            $customer->setName($address->getName());
            $customer->setEmail($address->getEmail());
            $customer->setQuoteAddressId($address->getId());

            return $customer;
        }
        $customer = new \Magento\Framework\DataObject();
        if ($email->getSenderName() == '') {
            $customer->setName($email->getFromEmail());
        } else {
            $customer->setName($email->getSenderName());
        }
        $customer->setEmail($email->getFromEmail());

        return $customer;
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    protected function _getCustomer()
    {
        return $this->customerFactory->create()->load($this->customerSession->getCustomerId());
    }

    /**
     * @param [] $params
     *
     * @return \Magento\Customer\Model\Customer|\Magento\Framework\DataObject
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerByPost($params)
    {
        $customer = $this->_getCustomer();
        // Patch for custom Contact Us form with ability to change email or name of customer (HDMX-98)
        if ($customer->getId() > 0 && !isset($params['customer_email']) && !isset($params['customer_name'])) {
            return $customer;
        }
        $email = $params['customer_email'];
        $name = $params['customer_name'];
        $customers = $this->customerCollectionFactory->create();
        $customers
                        ->addAttributeToSelect('*')
                        ->addAttributeToFilter('email', $email);
        if ($customers->count() > 0) {
            return $customers->getFirstItem();
        }
        $c = $this->customerFactory->create();
        $c->getEmail();
        $c->setEmail('aaa');
        /** @var \Magento\Customer\Model\Customer $address */
        $address = $customers->getFirstItem();
        if ($address->getId()) {
            $customer = new \Magento\Framework\DataObject();

            $customer->setName($address->getName());

            $customer->setEmail($address->getEmail());
            $customer->setQuoteAddressId($address->getId());

            return $customer;
        }
        $customer = new \Magento\Framework\DataObject();
        $customer->setName($name);
        $customer->setEmail($email);

        return $customer;
    }

    /**
     * Traverses two-dimensional array $haystack, and if element (which is also array)
     * has $key, tests, whether it equals to $needle.
     *
     * @param string $needle
     * @param array  $haystack
     * @param string $key
     *
     * @return bool
     */
    private function checkValueByKey($needle, $haystack, $key)
    {
        foreach ($haystack as $element) {
            if (isset($element[$key]) && $element[$key] == $needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param bool|string $q
     * @param bool|int    $customerId
     * @param bool|int    $addressId
     *
     * @return array
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerArray($q = false, $customerId = false, $addressId = false)
    {
        $firstnameId = $this->entityAttributeFactory->create()->loadByCode(1, 'firstname')->getId();
        $lastnameId = $this->entityAttributeFactory->create()->loadByCode(1, 'lastname')->getId();

        $collection = $this->customerCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->getSelect()->limit(20);

        if ($q) {
            $resource = $this->resource;
            $collection->getSelect()
                ->joinLeft(
                    ['varchar1' => $resource->getTableName('customer_entity').'_varchar'],
                    'e.entity_id = varchar1.entity_id and varchar1.attribute_id = '.$firstnameId,
                    ['firstname1' => 'varchar1.value']
                )
                ->joinLeft(
                    ['varchar2' => $resource->getTableName('customer_entity').'_varchar'],
                    'e.entity_id = varchar2.entity_id and varchar2.attribute_id = '.$lastnameId,
                    ['lastname1' => 'varchar2.value']
                )->joinLeft(
                    ['orders' => $resource->getTableName('sales_order')],
                    'e.entity_id = orders.customer_id',
                    ['order' => 'orders.increment_id']
                )->group('e.entity_id');
            //echo $collection->getSelect();die;
            $search = $this->searchFactory->create();
            $search->setSearchableCollection($collection);
            $search->setSearchableAttributes([
                'e.entity_id' => 0,
                'e.email' => 0,
                'firstname1' => 0,
                'lastname1' => 0,
                'order' => 0,
            ]);
            $search->setPrimaryKey('entity_id');
            $search->joinMatched($q, $collection, 'e.entity_id');
        }

        if ($customerId !== false) {
            $collection->addFieldToFilter('entity_id', $customerId);
        }

        $result = [];
        foreach ($collection as $customer) {
            $result[] = [
                'id' => $customer->getId(),
                'name' => $customer->getFirstname().' '.$customer->getLastname(),
                'label2' => $customer->getFirstname().' '.$customer->getLastname().' ('.$customer->getEmail().')',
                'label' => $customer->getEmail(),
                'email' => $customer->getEmail(),
            ];
        }

        //unregistered search
        $collection = $this->orderAddressCollectionFactory->create();
        $collection
            ->getSelect()
            ->group('email')
            ->limit(20);
        if ($q) {
            $search = $this->searchFactory->create();
            $search->setSearchableCollection($collection);
            $search->setSearchableAttributes([
                'email' => 0,
                'firstname' => 0,
                'lastname' => 0,
            ]);
            $search->setPrimaryKey('entity_id');
            $search->joinMatched($q, $collection, 'main_table.entity_id');
        }
        if ($addressId !== false) {
            $collection->addFieldToFilter('main_table.entity_id', $addressId);
        }

        // This collection maybe used by other 3rd party extensions. To avoid conflicts, we must skip “*load” events.
        // That’s why we use method getData().
        foreach ($collection->getData() as $address) {
            if (!$address['email']) {
                continue;
            }
            if (!$this->checkValueByKey($address['email'], $result, 'email')) {
                $orderId = $address['parent_id'];
                $result[] = [
                    // Fix to have proper order id extraction
                    'id' => 'address_'.$address['entity_id'],
                    'order_id' => $orderId,
                    'label2' => $address['firstname'].' '.$address['lastname']
                        .' ('.$address['email'].') [unregistered]',
                    'label' => $address['email'],
                    'name' => $address['firstname'].' '.$address['lastname'],
                    'email' => $address['email'],
                ];
            }
        }

        return $result;
    }

    /**
     * Find customer by email.
     *
     * @param string $q
     *
     * @return array
     */
    public function findCustomer($q)
    {
        $customers = $this->getCustomerArray($q);
        foreach ($customers as $key => $customer) {
            $customerId = false;
            if (isset($customer['id'])) {
                $customerId = (int) $customer['id'];
            }
            $orders = $this->helperOrder->getOrderArray($customer['email'], $customerId);
            array_unshift($orders, ['id' => 0, 'name' => __('Unassigned')]);
            $customers[$key]['orders'] = $orders;
        }

        return $customers;
    }
}
