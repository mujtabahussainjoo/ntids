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



namespace Mirasvit\Helpdesk\Service\Config;

class RmaConfig implements \Mirasvit\Helpdesk\Api\Config\RmaConfigInterface
{
    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->moduleManager = $moduleManager;
    }

    /**
     * {@inheritdoc}
     */
    public function isRmaActive()
    {
        if ($this->moduleManager->isEnabled('Mirasvit_Rma')) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

            return $objectManager->create('Mirasvit\Rma\Service\Config\HelpdeskConfig')->isHelpdeskActive();
        }

        return false;
    }
}
