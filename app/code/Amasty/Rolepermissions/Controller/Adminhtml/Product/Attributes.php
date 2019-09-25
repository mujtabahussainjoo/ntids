<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;

class Attributes extends Action
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /** @var \Amasty\Rolepermissions\Model\Rule $rule */
    protected $rule;

    /**
     * Attributes constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Amasty\Rolepermissions\Model\Rule $rule
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Registry $registry,
        \Amasty\Rolepermissions\Model\Rule $rule
    ) {
        parent::__construct($context);

        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->_coreRegistry = $registry;
        $this->rule = $rule;
    }

    public function execute()
    {
        if ($rid = (int) $this->_request->getParam('rid')) {
            $this->rule->load($rid, 'role_id');
        }

        $this->_coreRegistry->register('amrolepermissions_current_rule', $this->rule);

        return $this->resultLayoutFactory->create();
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_User::acl_roles');
    }
}
