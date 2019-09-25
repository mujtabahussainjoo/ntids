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



namespace Mirasvit\Helpdesk\Block\Satisfaction;

class Form extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mirasvit\Helpdesk\Model\SatisfactionFactory
     */
    protected $satisfactionFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Helpdesk\Model\SatisfactionFactory     $satisfactionFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\SatisfactionFactory $satisfactionFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->satisfactionFactory = $satisfactionFactory;
        $this->request = $context->getRequest();
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     *
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('Thank you'));
    }

    /**
     * @return string
     */
    public function getPostUrl()
    {
        return $this->context->getUrlBuilder()->getUrl(
            'helpdesk/satisfaction/post',
            [
                'uid' => $this->getUid(),
                'satisfaction' => $this->getSatisfactionId()
            ]
        );
    }

    /**
     * @return object
     */
    public function getUid()
    {
        return $this->request->getParam('uid');
    }

    /**
     * @return object
     */
    public function getSatisfactionId()
    {
        return $this->request->getParam('satisfaction');
    }

    /**
     * @return object
     */
    public function getSatisfaction()
    {
        return $this->satisfactionFactory->create()->load($this->getSatisfactionId());
    }

    /**
     * @return object
     */
    public function getTicket()
    {
        return $this->getSatisfaction()->getTicket();
    }
}
