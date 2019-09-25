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

use Mirasvit\Helpdesk\Api\Data\PatternInterface;

abstract class Pattern extends \Magento\Backend\App\Action
{
    /**
     * @var \Mirasvit\Helpdesk\Model\PatternFactory
     */
    protected $patternFactory;

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
     * @param \Mirasvit\Helpdesk\Model\PatternFactory              $patternFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Registry                          $registry
     * @param \Magento\Backend\App\Action\Context                  $context
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\PatternFactory $patternFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->patternFactory = $patternFactory;
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
     * @return object
     */
    public function _initPattern()
    {
        $pattern = $this->patternFactory->create();
        if ($this->getRequest()->getParam('id')) {
            $pattern->load($this->getRequest()->getParam('id'));
        }
        // 3. Set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $pattern->setData($data);
        }

        $this->registry->register('current_pattern', $pattern);

        return $pattern;
    }

    /**
     * @param array $data
     * @return array
     */
    public function prepareData($data)
    {
        if (empty($data[PatternInterface::ID])) {
            unset($data[PatternInterface::ID]);
            unset($data['id']);
        }

        return $data;
    }

    /**
     *
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Helpdesk::helpdesk_pattern');
    }

    /************************/
}
