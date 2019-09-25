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

use Magento\Framework\App\Action\Action;
use Magento\Customer\Model\Session;
use Magento\Backend\Model\View\Result\ForwardFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class Article extends Action
{
    public function __construct(
        ForwardFactory $resultForwardFactory,
        \Mirasvit\Kb\Api\Service\Article\ArticleManagementInterface $articleManagementInterface,
        \Mirasvit\Kb\Model\ArticleFactory $articleFactory,
        \Mirasvit\Kb\Model\ResourceModel\Article\CollectionFactory $articleCollectionFactory,
        \Magento\Catalog\Model\Session $session,
        \Mirasvit\Kb\Helper\Data $kbData,
        \Mirasvit\Kb\Helper\Vote $kbVote,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->resultForwardFactory       = $resultForwardFactory;
        $this->articleManagementInterface = $articleManagementInterface;
        $this->articleFactory             = $articleFactory;
        $this->articleCollectionFactory   = $articleCollectionFactory;
        $this->session                    = $session;
        $this->kbData                     = $kbData;
        $this->kbVote                     = $kbVote;
        $this->registry                   = $registry;
        $this->context                    = $context;
        $this->resultFactory              = $context->getResultFactory();

        parent::__construct($context);
    }

    /**
     * @return \Magento\Catalog\Model\Session
     */
    protected function _getSession()
    {
        return $this->session;
    }

    /**
     * @return \Mirasvit\Kb\Model\Article
     */
    protected function _initArticle()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $article = $this->articleFactory->create()->load($id);
            $isAvailable = $this->articleManagementInterface->isAvailableForStore($article);

            if ($article->getId() > 0 && $isAvailable) {
                $this->registry->register('current_article', $article);

                return $article;
            }
        }
    }
}
