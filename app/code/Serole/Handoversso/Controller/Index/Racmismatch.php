<?php
/**
 * Created by Serole(Dk) on 16/11/2018.
 * For SSO integration
 */
namespace Serole\Handoversso\Controller\Index;

use Magento\Framework\App\Action\Context;

class Racmismatch extends \Magento\Framework\App\Action\Action
{
	
	const XML_PATH_EMAIL_TEMPLATE_FIELD = 'contact/email/email_template';

    /**
     * Sender email config path - from default CONTACT extension
     */
    const XML_PATH_EMAIL_SENDER = 'contact/email/sender_email_identity';
	
	
	/**
     * Recipient email
     */
    const XML_PATH_EMAIL_RECIPIENT = 'contact/email/recipient_email';
	
	/* logging */
	protected $_logger;
	
	protected $_storeManager;
	
	Protected $_helper;

	
	   /**
     * @param Context $context
     */
    public function __construct(
        Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Serole\Handoversso\Helper\Data $Helper
    ) 
	{
		$this->_storeManager = $storeManager;
        $this->_helper = $Helper;		
        parent::__construct($context);
    }
	
	 /**
     * Return email for Recipient
     * @return mixed
     */
    public function emailRecipient()
    {
        return $this->_helper->getConfigValue(
            self::XML_PATH_EMAIL_RECIPIENT,
            $this->_helper->getStoreId()
        );
    }
	
	 /**
     * Return email Template
     * @return mixed
     */
    public function emailTemplate()
    {
        return $this->_helper->getConfigValue(
            self::XML_PATH_EMAIL_TEMPLATE_FIELD,
            $this->_helper->getStoreId()
        );
    }
	
	/**
     * SSO integration for different store
     */
    public function execute()
    {
		   $this->createLog('rac_handoversso.log');
		   if(isset($_POST) && !empty($_POST))
		   {
		      $variable = $_POST;
			  $variable['website'] = $this->_storeManager->getWebsite()->getName();
			  $receiverInfo = [
						'name' => "Rac Admin",
						'email' => $this->emailRecipient()
					];
		     $templateId = $this->emailTemplate();
			 $this->_helper->notifyUser($receiverInfo, $variable, $templateId);
			 $this->_logger->info("Email Sent Successfully");
			 $this->messageManager->addSuccess("Thanks for your email. We will get back to you soon");
			 $this->_redirect("/");
		   }
			else
			{
				 $this->_logger->info("Missing Required Fileds");
				 $this->messageManager->addError("Missing Required Fileds");
				 $this->_redirect("*/*/");
			}
		 
    }
	
	
	public function createLog($file)
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/'.$file);
		$this->_logger = new \Zend\Log\Logger();
		$this->_logger->addWriter($writer);
	}
}