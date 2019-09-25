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


namespace Mirasvit\Helpdesk\Plugin;

/**
 * We use this plugin to insert our form on the Contact Us page.
 * @package Mirasvit\Helpdesk\Plugin
 */
class ContactForm
{
    /**
     * @param \Mirasvit\Helpdesk\Model\Config $config
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param \Magento\Contact\Block\ContactForm $subject
     * @param string $result
     * @return string
     */
    public function afterFetchView($subject, $result)
    {
        if (!$this->config->getGeneralContactUsIsActive()) {
            return $result;
        }
        /** @var \Mirasvit\Helpdesk\Block\Contact\Form $block */
        $block = $subject->getLayout()->createBlock('Mirasvit\Helpdesk\Block\Contact\Form');
        $block->setTemplate('Mirasvit_Helpdesk::contacts/form.phtml');

        return $block->toHtml();
    }
}