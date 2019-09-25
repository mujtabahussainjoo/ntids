<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Controller\Adminhtml\ActionsLog;

class Preview extends \Magento\Backend\App\Action
{
    protected $resultRawFactory;
    protected $layoutFactory;
    protected $_registryManager;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        $this->layoutFactory = $layoutFactory;
        $this->resultRawFactory = $resultRawFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $elementId = (int)$this->getRequest()->getParam('element_id', md5(microtime()));

        $content = $this->layoutFactory->create()->createBlock(
            'Amasty\AdminActionsLog\Block\Adminhtml\ActionsLog\Edit\Details',
            '',
            [
                'data' => [
                    'editor_element_id' => $elementId,
                ]
            ]
        );

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents($content->toHtml());
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_AdminActionsLog::actions_log');
    }
}
