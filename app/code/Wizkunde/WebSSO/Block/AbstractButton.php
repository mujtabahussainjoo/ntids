<?php

namespace Wizkunde\WebSSO\Block;

class AbstractButton extends \Magento\Framework\View\Element\Template
{
    protected $serverHelper;
    protected $customerSession;

    protected $title = 'Login with Single Sign-On';

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     * @param \Wizkunde\WebSSO\Helper\Server $serverHelper
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = [],
        \Wizkunde\WebSSO\Helper\Server $serverHelper,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        parent::__construct($context, $data);

        $this->serverHelper = $serverHelper;
        $this->customerSession = $customerSession;
    }


    public function getTitle()
    {
        return $this->title;
    }

    public function getServerHelper()
    {
        return $this->serverHelper;
    }

    public function getCustomerSession()
    {
        return $this->customerSession;
    }
}