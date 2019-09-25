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



namespace Mirasvit\Helpdesk\Controller\Satisfaction;

use Magento\Framework\Controller\ResultFactory;

class Post extends \Mirasvit\Helpdesk\Controller\Satisfaction
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $uid = $this->getRequest()->getParam('uid');

        $comment = [];

        foreach ($this->getRequest()->getParams() as $key => $value) {
            if ($key != 'uid' && $key != 'satisfaction') {
                $comment[] = ucfirst($key).': '.$value;
            }
        }
        if (count($comment) > 1) {
            $comment = implode(PHP_EOL, $comment);
        } else {
            $comment = $this->getRequest()->getParam('comment');
        }

        if ($comment) {
            $this->helpdeskSatisfaction->addComment($uid, $comment);
        }

        ;

        return $resultPage;
    }
}
