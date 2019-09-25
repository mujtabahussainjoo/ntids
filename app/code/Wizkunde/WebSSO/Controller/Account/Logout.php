<?php
namespace Wizkunde\WebSSO\Controller\Account;

class Logout extends \Magento\Customer\Controller\Account\Logout
{
    protected $connection;

    protected $frontendHelper;
    protected $serverHelper;
    protected $mappingHelper;
    protected $customerSession;
    protected $redirectInterface;
    protected $accountRedirect;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Account\Redirect $accountRedirect,
        \Wizkunde\WebSSO\Helper\Frontend $ssoHelper,
        \Wizkunde\WebSSO\Helper\Server $serverHelper,
        \Magento\Framework\App\State $mappingHelper,
        \Wizkunde\WebSSO\Connection\Connection $connection
    ) {
        $this->frontendHelper = $ssoHelper;
        $this->serverHelper = $serverHelper;
        $this->mappingHelper = $mappingHelper;
        $this->connection = $connection;
        $this->customerSession = $customerSession;
        $this->accountRedirect = $accountRedirect;

        parent::__construct($context,$customerSession);
    }

    public function execute()
    {
        if ($this->serverHelper->checkFrontendEnabled() === true &&
            $this->mappingHelper->getAreaCode() !== \Magento\Framework\App\Area::AREA_ADMINHTML
        ) {
            if ($this->customerSession->isLoggedIn() === true) {
                $this->connection->logout();

                /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('/customer/account/logout');
                return $resultRedirect;
            } else {
                $message = __('You have been logged out');
                $this->messageManager->addSuccess($message);

                /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('/');
                return $resultRedirect;
            }
        }
    }
}