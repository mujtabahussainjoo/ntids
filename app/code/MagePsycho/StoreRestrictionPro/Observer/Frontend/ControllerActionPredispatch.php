<?php

namespace MagePsycho\StoreRestrictionPro\Observer\Frontend;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @category   MagePsycho
 * @package    MagePsycho_StoreRestrictionPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ControllerActionPredispatch implements ObserverInterface
{
    /**
     * @var \MagePsycho\StoreRestrictionPro\Helper\Data
     */
    private $storeRestrictionProHelper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    private $responseFactory;


    public function __construct(
        \MagePsycho\StoreRestrictionPro\Helper\Data $storeRestrictionProHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\App\ResponseFactory $responseFactory
    ) {
        $this->storeRestrictionProHelper = $storeRestrictionProHelper;
        $this->messageManager            = $messageManager;
        $this->customerSession           = $customerSession;
        $this->customerFactory           = $customerFactory;
        $this->responseFactory           = $responseFactory;
    }

    public function execute(Observer $observer)
    {
        $this->storeRestrictionProHelper->log(__METHOD__, true);

        $request        = $observer->getEvent()->getRequest();
        $fullActionName = $request->getFullActionName();

        if ($this->storeRestrictionProHelper->skipRestrictionByDefault()) {
            $this->storeRestrictionProHelper->log('restriction by-passed for request::' . $fullActionName);
            return $this;
        }

        // if new account creation is disabled and if the current request is registration page, if yes redirect to login page
        // also check for restriction type
        if ($this->storeRestrictionProHelper->isAccountRegistrationPage()
            && $this->storeRestrictionProHelper->isAccountRegistrationDisabled()
        ) {
            $this->messageManager->getMessages(true);
            $this->_redirect($this->storeRestrictionProHelper->getLoginUrl());
        }

        if ($this->storeRestrictionProHelper->getConfigHelper()->getRestrictionType() == \MagePsycho\StoreRestrictionPro\Model\System\Config\Source\RestrictionType::RESTRICTION_TYPE_RESTRICTED_ACCESSIBLE) { //Restricted (Only Configured Pages Accessible)

            if ($this->customerSession->isLoggedIn()
                && ! $this->storeRestrictionProHelper->isCustomerGroupAllowedForRestrictedStore()
            ) {
                $this->messageManager->getMessages(true);
                $this->customerSession
                    ->setCustomer($this->customerFactory->create())
                    ->setId(null)
                    ->setCustomerGroupId(\Magento\Customer\Model\Group::NOT_LOGGED_IN_ID);

                $customerGroupErrorMessage = $this->storeRestrictionProHelper
                    ->getConfigHelper()
                    ->getRestrictedCustomerGroupErrorMessage();
                if ( ! empty( $customerGroupErrorMessage )) {
                    $this->messageManager->addErrorMessage($customerGroupErrorMessage);
                }

                $this->_redirect($this->storeRestrictionProHelper->getLoginUrl());
            }

            if ( ! $this->customerSession->isLoggedIn()) {
                $isCurrentPageRestricted = false;
                if (in_array($fullActionName, [ 'cms_index_index', 'cms_page_view' ])) { //CMS page
                    $this->storeRestrictionProHelper->log('::CMS::', true);
                    if ( ! $this->storeRestrictionProHelper->isRestrictedCmsPageAccessible()) {
                        $isCurrentPageRestricted = true;
                    }
                } else if (in_array($fullActionName, [ 'catalog_category_view' ])) { //Category page
                    $this->storeRestrictionProHelper->log('::CATEGORY::', true);
                    if ( ! $this->storeRestrictionProHelper->isRestrictedCategoryPageAccessible()) {
                        $isCurrentPageRestricted = true;
                    }
                } else if (in_array($fullActionName, [ 'catalog_product_view' ])) { //Product page
                    $this->storeRestrictionProHelper->log('::PRODUCT::', true);
                    if ( ! $this->storeRestrictionProHelper->isRestrictedProductPageAccessible()) {
                        $isCurrentPageRestricted = true;
                    }
                } else {//Modules page
                    $this->storeRestrictionProHelper->log('::MODULE::', true);
                    //get all the list of allowed modules
                    if ( ! $this->storeRestrictionProHelper->isRestrictedModulePageAccessible()) {
                        $isCurrentPageRestricted = true;
                    }
                }

                $this->storeRestrictionProHelper->log('$isCurrentPageRestricted::' . $isCurrentPageRestricted);
                if ($isCurrentPageRestricted) {
                    //check if the current page is restricted, yes? then add the error message to session, get landing page and redirect
                    $this->messageManager->getMessages(true);
                    $storeErrorMessage = $this->storeRestrictionProHelper->getConfigHelper()->getRestrictedStoreErrorMessage();
                    if ( ! empty($storeErrorMessage)) {
                        $this->messageManager->addErrorMessage($storeErrorMessage);
                    }
                    $landingPage = $this->storeRestrictionProHelper->getRestrictedLandingPage();
                    $this->storeRestrictionProHelper->log('$storeErrorMessage::' . $storeErrorMessage . ', $landingPage::' . $landingPage);
                    $this->_redirect($landingPage);
                }
            }
        }
    }

    protected function _redirect($url)
    {
        $this->responseFactory->create()
            ->setRedirect($url)
            ->sendResponse();
        return $this;
    }
}