<?php
namespace Wizkunde\WebSSO\Helper;

use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Exception\LocalizedException;
use Wizkunde\WebSSO\Model\CustomerManagement;

class Frontend extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $serverHelper;
    private $customerFactory;
    private $customerManagement;
    private $customerSession;
    private $storeManager;

    /**
     * Frontend constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param Server $serverHelper
     * @param CustomerFactory $customerFactory
     * @param CustomerManagement $customerManagement
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Wizkunde\WebSSO\Helper\Server $serverHelper,
        CustomerFactory $customerFactory,
        CustomerManagement $customerManagement,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
    
        parent::__construct($context);

        $this->serverHelper = $serverHelper;
        $this->customerFactory = $customerFactory;
        $this->customerManagement = $customerManagement;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
    }

    /**
     * Login the user and create it if it doesnt exist yet
     *
     * @param $connection
     * @throws LocalizedException
     */
    public function loginUser($connection)
    {
        $userData = $this->serverHelper->getMappings($connection->getUserData());

        if (!isset($userData['email'])) {
            return false;
        }

        // Get the customer, but create it first if we have to
        $customerDataObject = $this->customerManagement->getCustomer($userData);
        $this->customerManagement->loginCustomer($customerDataObject);

        // Login user
        $customer = $this->customerFactory->create();
        $customer->load($customerDataObject->getId());
        $this->customerSession->setCustomerAsLoggedIn($customer);

        return $customer->getUsername();
    }
}
