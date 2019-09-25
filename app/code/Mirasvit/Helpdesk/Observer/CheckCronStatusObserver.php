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


namespace Mirasvit\Helpdesk\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckCronStatusObserver implements ObserverInterface
{
    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Gateway\CollectionFactory
     */
    protected $gatewayCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Mirasvit\Core\Helper\Cron
     */
    protected $mstcoreCron;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Backend\Model\Url
     */
    protected $backendUrlManager;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $auth;


    /**
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Gateway\CollectionFactory $gatewayCollectionFactory
     * @param \Mirasvit\Helpdesk\Model\Config                                  $config
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                      $date
     * @param \Mirasvit\Core\Helper\Cron                                    $mstcoreCron
     * @param \Magento\Framework\App\Request\Http                              $request
     * @param \Magento\Framework\Message\ManagerInterface                      $messageManager
     * @param \Magento\Backend\Model\Auth                                      $auth
     * @param \Magento\Backend\Model\Url $backendUrlManager
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\ResourceModel\Gateway\CollectionFactory $gatewayCollectionFactory,
        \Mirasvit\Helpdesk\Model\Config $config,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Mirasvit\Core\Helper\Cron $mstcoreCron,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Backend\Model\Auth $auth,
        \Magento\Backend\Model\Url $backendUrlManager
    ) {
        $this->gatewayCollectionFactory = $gatewayCollectionFactory;
        $this->config = $config;
        $this->date = $date;
        $this->mstcoreCron = $mstcoreCron;
        $this->request = $request;

        $this->messageManager = $messageManager;
        $this->auth = $auth;
        $this->backendUrlManager = $backendUrlManager;
    }

    /**
     * @var bool
     */
    protected $cronChecked = false;

    /**
     * Save order into registry to use it in the overloaded controller.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $admin = $this->auth->getUser();
        if (!$admin ||
            $this->cronChecked ||
            $this->request->isAjax()) {
            return $this;
        }

        try {
            $gateways = $this->gatewayCollectionFactory->create()
                ->addFieldToFilter('is_active', true)
                ->addFieldToFilter(
                    'fetched_at',
                    ['lt' => $this->date->gmtDate(
                        null,
                        $this->date->timestamp() - 60 * 60 * 3
                    )
                    ]
                );
            if ($gateways->count() == 0) {
                return $this;
            }
        } catch (\Exception $e) { //it's possible that tables are not created yet. so we have to catch this error.
            return $this;
        }
        //if we here, then something wrong. we have not fetched emails during long time.

        if ($this->config->getGeneralIsDefaultCron()) {
            list($result, $message) = $this->mstcoreCron->checkCronStatus('mirasvit_helpdesk', false);
            if ($result) {
                $message = __('Help Desk can\'t fetch new emails.
                Please, try to run bin/magento mirasvit:helpdesk:run to find out what is going wrong.');
            } else {
                $message = __('Help desk can\'t fetch emails. ').$message;
                $message .= __(
                    '<br> To temporary hide this message, disable all <a href="%1">help desk gateways</a>.',
                    $this->backendUrlManager->getUrl('helpdesk/gateway')
                );
            }
        } else {
            $message = __(
                'Help Desk can\'t fetch new emails.
                 Please, check that you are running cron for bin/magento mirasvit:helpdesk:run.'
            );
        }
        $this->messageManager->addError($message);
        $this->cronChecked = true;
        return $this;
    }
}
