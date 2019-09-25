<?php

namespace Serole\Racvportal\Controller\Customer;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\View\Result\PageFactory;


    class Loginpost extends \Magento\Customer\Controller\Account\LoginPost {

        protected $customer;

        protected $storeManager;

        public function __construct(Context $context,
                                    Session $customerSession,
                                    AccountManagementInterface $customerAccountManagement,
                                    CustomerUrl $customerHelperData,
                                    Validator $formKeyValidator,
                                    AccountRedirect $accountRedirect,
                                    \Magento\Store\Model\StoreManagerInterface $storeManager,
                                    \Magento\Customer\Model\Customer $customer)
        {
            parent::__construct($context, $customerSession, $customerAccountManagement, $customerHelperData, $formKeyValidator, $accountRedirect);
            $this->context = $context;
            $this->customer = $customer;
            $this->storeManager = $storeManager;
            $this->customerAccountManagement  = $customerAccountManagement;
            $this->session = $customerSession;
        }

        public function execute(){
            if ($this->session->isLoggedIn() || !$this->formKeyValidator->validate($this->getRequest())) {
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/');
                return $resultRedirect;
            }
            //echo "<pre>"; print_r($this->getRequest()->getParams());
            if ($this->getRequest()->isPost()) {
                $login = $this->getRequest()->getPost('login');
                if (!empty($login['username']) && !empty($login['password']) && !empty($login['shop'])) {
                    try {
                        $customerObj = $this->customer->getCollection();
                        $customerObj->addAttributeToSelect('*')
                                    ->addAttributeToFilter('memberno',$login['username'])
                                    ->load();
                        $customer = $customerObj->getFirstItem();
                        $customerData = $customer->getData();
                        if(!empty($customerData)){
                            //echo "<pre>"; print_r($customerData); exit;
                            if($customerData['is_suspended'] != 1 || $customerData['block_fraud_customer'] != 1) {
                                $customerEmail = $customerData['email'];
                                $customer = $this->customerAccountManagement->authenticate($customerEmail, $login['password']);
                                $this->session->setCustomerDataAsLoggedIn($customer);
                                $this->session->regenerateId();
                                $storeData = $this->storeManager->getStore();
                                $storeUrl = $storeData->getUrl();
                                if ($this->session->isLoggedIn()) {
                                    $this->session->setRacvShop($login['shop']);
                                    $this->session->setMemberNo($login['username']);
                                    $this->accountRedirect->clearRedirectCookie();
                                    $this->context->getResponse()->setRedirect($storeUrl);
                                    return $this;
                                }
                            }else{
                                $this->messageManager->addError(
                                    __('This Account is suspended')
                                );
                            }
                        }else{
                            $this->messageManager->addError(
                                __('This customer does not exist. Please contact your administrator for assistance.')
                            );
                        }
                    } catch (EmailNotConfirmedException $e) {
                        $value = $this->customerUrl->getEmailConfirmationUrl($login['username']);
                        $message = __(
                            'This account is not confirmed. <a href="%1">Click here</a> to resend confirmation email.',
                            $value
                        );
                        $this->messageManager->addError($message);
                        $this->session->setUsername($login['username']);
                    } catch (UserLockedException $e) {
                        $message = __(
                            'You did not sign in correctly or your account is temporarily disabled.'
                        );
                        $this->messageManager->addError($message);
                        $this->session->setUsername($login['username']);
                    } catch (AuthenticationException $e) {
                        $message = __('You did not sign in correctly or your account is temporarily disabled.');
                        $this->messageManager->addError($message);
                        $this->session->setUsername($login['username']);
                    } catch (LocalizedException $e) {
                        $message = $e->getMessage();
                        $this->messageManager->addError($message);
                        $this->session->setUsername($login['username']);
                    } catch (\Exception $e) {
                        // PA DSS violation: throwing or logging an exception here can disclose customer password
                        $this->messageManager->addError(
                            __('User ID or password was incorrect. Please try again.')
                        );
                    }
                } else {
                    $this->messageManager->addError(__('A login and a password are required.'));
                }
            }

            return $this->accountRedirect->getRedirect();
        }
    }
