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



namespace Mirasvit\Kb\Helper;

class Vote extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $session;

    /**
     * @param \Magento\Catalog\Model\Session        $session
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Catalog\Model\Session $session,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->session = $session;
        parent::__construct($context);
    }

    /**
     * @param \Mirasvit\Kb\Model\Article $article
     *
     * @return int
     */
    public function getVoteResult($article)
    {
        $result = $this->session->getData('kbvote'.$article->getId());
        if ($result) {
            return $result;
        }
    }

    /**
     * @param \Mirasvit\Kb\Model\Article $article
     * @param int                        $vote
     * @return void
     */
    public function markAsVoted($article, $vote)
    {
        $this->session->setData('kbvote'.$article->getId(), $vote);
    }
}
