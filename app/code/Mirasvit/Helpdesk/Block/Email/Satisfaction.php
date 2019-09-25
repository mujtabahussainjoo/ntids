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



namespace Mirasvit\Helpdesk\Block\Email;

/**
 * @method \Mirasvit\Helpdesk\Model\Ticket getTicket()
 * @method $this setTicket(\Mirasvit\Helpdesk\Model\Ticket $param)
 */
class Satisfaction extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Mirasvit\Helpdesk\Model\Config $config,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->config       = $config;
        $this->storeManager = $context->getStoreManager();

        parent::__construct($context, $data);
    }

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setData('area', 'frontend');
    }

    /**
     * @param string $rate
     * @return string
     */
    public function getRateUrl($rate)
    {
        $message = $this->getTicket()->getLastMessage();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $urlBuilder = $this->_appState->emulateAreaCode(
            'frontend',
            [$objectManager, 'create'],
            ['\Magento\Framework\Url']
        );
        if ($this->getTicket()->getStore()) {
            $urlBuilder->setScope($this->getTicket()->getStore()->getId());
        }
        $url = $urlBuilder->getUrl(
            'helpdesk/satisfaction/rate',
            ['rate' => $rate, 'uid' => $message->getUid()]
        );

        return $url;
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->config->getSatisfactionIsActive()) {
            return '';
        }

        return parent::_toHtml();
    }
}
