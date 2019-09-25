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


namespace Mirasvit\Helpdesk\Controller\Adminhtml;

use Mirasvit\Helpdesk\Api\Data\GatewayInterface;

abstract class Gateway extends \Magento\Backend\App\Action
{
    /**
     * @var \Mirasvit\Helpdesk\Model\GatewayFactory
     */
    protected $gatewayFactory;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Fetch
     */
    protected $helpdeskFetch;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Checkenv
     */
    protected $helpdeskCheckenv;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\App\Action\Context
     */
    protected $context;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @param \Mirasvit\Helpdesk\Model\GatewayFactory              $gatewayFactory
     * @param \Mirasvit\Helpdesk\Helper\Fetch                      $helpdeskFetch
     * @param \Mirasvit\Helpdesk\Helper\Checkenv                   $helpdeskCheckenv
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Registry                          $registry
     * @param \Magento\Backend\App\Action\Context                  $context
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\GatewayFactory $gatewayFactory,
        \Mirasvit\Helpdesk\Helper\Fetch $helpdeskFetch,
        \Mirasvit\Helpdesk\Helper\Checkenv $helpdeskCheckenv,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->gatewayFactory = $gatewayFactory;
        $this->helpdeskFetch = $helpdeskFetch;
        $this->helpdeskCheckenv = $helpdeskCheckenv;
        $this->localeDate = $localeDate;
        $this->registry = $registry;
        $this->context = $context;
        $this->backendSession = $context->getSession();
        $this->resultFactory = $context->getResultFactory();
        parent::__construct($context);
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        if (!extension_loaded('imap')) {
            $this->messageManager->addErrorMessage(__(
                'Please, ask your hosting provider to enable IMAP extension in PHP configuration of your server. ' .
                'Otherwise, helpdesk will not be able to fetch emails.'
            ));
        }

        return parent::dispatch($request);
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->_setActiveMenu('helpdesk');

        return $this;
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\Gateway
     */
    public function _initGateway()
    {
        $gateway = $this->gatewayFactory->create();
        if ($this->getRequest()->getParam('id')) {
            $gateway->load($this->getRequest()->getParam('id'));
        }

        $this->registry->register('current_gateway', $gateway);

        return $gateway;
    }

    /**
     * @param array $data
     * @return array
     */
    public function prepareData($data)
    {
        if (empty($data[GatewayInterface::ID])) {
            unset($data[GatewayInterface::ID]);
            unset($data['id']);
        }
        if (isset($data['password']) && $data['password'] == '*****') {
            unset($data['password']);
        }

        return $data;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Helpdesk::helpdesk_gateway');
    }

    /************************/
}
