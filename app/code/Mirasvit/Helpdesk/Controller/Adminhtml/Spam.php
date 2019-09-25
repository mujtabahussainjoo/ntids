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

abstract class Spam extends \Magento\Backend\App\Action
{
    /**
     * @var \Mirasvit\Helpdesk\Model\TicketFactory
     */
    protected $ticketFactory;

    /**
     * @var \Mirasvit\Core\Helper\Date
     */
    //protected $mstcoreDate;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Permission
     */
    protected $helpdeskPermission;

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
     * @param \Mirasvit\Helpdesk\Model\TicketFactory               $ticketFactory
     * @param \Mirasvit\Helpdesk\Helper\Permission                 $helpdeskPermission
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Registry                          $registry
     * @param \Magento\Backend\App\Action\Context                  $context
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\TicketFactory $ticketFactory,
        \Mirasvit\Helpdesk\Helper\Permission $helpdeskPermission,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->ticketFactory = $ticketFactory;
        $this->helpdeskPermission = $helpdeskPermission;
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

    // public function addAction ()
    // {
    //     $resultPage->getConfig()->getTitle()->prepend(__('New Spam'));

    //     $this->_initModel();

    //     $this->_initAction();
    //     $this->_addBreadcrumb(__('Spam  Manager'),
    //             __('Spam Manager'), $this->getUrl('*/*/'));
    //     $this->_addBreadcrumb(__('Add Spam '), __('Add Spam'));

    //     $this->getLayout()
    //         ->getBlock('head')
    //         ;
    //     $this->_addContent($this->getLayout()->createBlock('\Mirasvit\Helpdesk\Block\Adminhtml\Spam\Edit'));
    //     $this->renderLayout();
    // }


    // public function saveAction ()
    // {
    //     if ($data = $this->getRequest()->getParams()) {

    //         $model = $this->_initModel();
    //         $model->addData($data);

    //         //format date to standart
    //         // $format = $this->localeDate->getDateFormat(\IntlDateFormatter::SHORT);
    //         // $this->mstcoreDate->formatDateForSave($model, 'active_from', $format);
    //         // $this->mstcoreDate->formatDateForSave($model, 'active_to', $format);

    //         try {
    //             $model->save();

    //             $this->messageManager->addSuccess(__('Spam was successfully saved'));
    //             $this->backendSession->setFormData(false);

    //             if ($this->getRequest()->getParam('back')) {
    //                 $this->_redirect('*/*/edit', ['id' => $model->getId()]);
    //                 return;
    //             }
    //             $this->_redirect('*/*/');
    //             return;
    //         } catch (\Exception $e) {
    //             $this->messageManager->addError($e->getMessage());
    //             $this->backendSession->setFormData($data);
    //             $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
    //             return;
    //         }
    //     }
    //     $this->messageManager->addError(__('Unable to find spam to save'));
    //     $this->_redirect('*/*/');
    // }


    /**
     * @return object
     */
    public function _initModel()
    {
        $model = $this->ticketFactory->create();
        if ($this->getRequest()->getParam('id')) {
            $model->load($this->getRequest()->getParam('id'));
        }

        $this->registry->register('current_ticket', $model);

        return $model;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Helpdesk::helpdesk_spam');
    }

    /************************/
}
