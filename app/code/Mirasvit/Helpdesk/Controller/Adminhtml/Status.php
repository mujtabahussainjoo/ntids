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

use Mirasvit\Helpdesk\Api\Data\StatusInterface;

abstract class Status extends \Magento\Backend\App\Action
{
    /**
     * @var \Mirasvit\Helpdesk\Model\StatusFactory
     */
    protected $statusFactory;

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
     * @param \Mirasvit\Helpdesk\Model\StatusFactory               $statusFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Registry                          $registry
     * @param \Magento\Backend\App\Action\Context                  $context
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\StatusFactory $statusFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->statusFactory = $statusFactory;
        $this->localeDate = $localeDate;
        $this->registry = $registry;
        $this->context = $context;
        $this->backendSession = $context->getSession();
        $this->resultFactory = $context->getResultFactory();
        parent::__construct($context);
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
     * @return \Mirasvit\Helpdesk\Model\Status
     */
    public function _initStatus()
    {
        $status = $this->statusFactory->create();
        if ($statusId = $this->getRequest()->getParam('id')) {
            $status->load($statusId);
            if ($storeId = (int) $this->getRequest()->getParam('store')) {
                $status->setStoreId($storeId);
            }
        }
        $this->registry->register('current_status', $status);

        return $status;
    }

    /**
     * @param array $data
     * @return array
     */
    public function prepareData($data)
    {
        if (empty($data[StatusInterface::ID])) {
            unset($data[StatusInterface::ID]);
            unset($data['id']);
        }

        return $data;
    }

    /**
     *
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Helpdesk::helpdesk_status');
    }

    /************************/
}
