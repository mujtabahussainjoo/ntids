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


namespace Mirasvit\Helpdesk\Block;

/**
 * Customer account dropdown link
 */
class Link extends \Magento\Framework\View\Element\Html\Link
{
    public function __construct(
        \Mirasvit\Helpdesk\Model\Config $config,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->config      = $config;
        $this->httpContext = $httpContext;
    }

    /**
     * @var string
     */
    protected $_template = 'Mirasvit_Helpdesk::link.phtml';

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('helpdesk/ticket');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return $this->config->getDefaultFrontName($this->_storeManager->getStore());
    }

    /**
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('helpdesk/ticket/getopen');
    }

    /**
     * @return bool
     */
    public function isShow()
    {
        return (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH) &&
            $this->config->getGeneralShowInCustomerMenu();
    }
}
