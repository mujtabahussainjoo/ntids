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
 * @package   mirasvit/module-kb
 * @version   1.0.49
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Kb\Controller;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;

abstract class Tag extends Action
{
    /**
     * @var \Mirasvit\Kb\Model\TagFactory
     */
    protected $tagFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @param \Mirasvit\Kb\Model\TagFactory         $tagFactory
     * @param ForwardFactory                        $resultForwardFactory
     * @param \Magento\Framework\Registry           $registry
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Mirasvit\Kb\Model\TagFactory $tagFactory,
        ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->tagFactory = $tagFactory;
        $this->registry = $registry;
        $this->context = $context;
        $this->resultFactory = $context->getResultFactory();
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    /**
     * @return $this
     */
    protected function _initTag()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $tag = $this->tagFactory->create()->load($id);
            if ($tag->getId() > 0) {
                $this->registry->register('current_tag', $tag);

                return $tag;
            }
        }
    }
}
