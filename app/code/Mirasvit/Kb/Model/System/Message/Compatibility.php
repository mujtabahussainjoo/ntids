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


namespace Mirasvit\Kb\Model\System\Message;

use Magento\Framework\Notification\MessageInterface;
use Magento\Framework\Module\Manager;
use Mirasvit\Core\Model\ModuleFactory;

class Compatibility implements MessageInterface
{
    /**
     * @param Manager $moduleManager
     * @param ModuleFactory $moduleFactory
     */
    public function __construct(
        Manager $moduleManager,
        ModuleFactory $moduleFactory
    ) {
        $this->moduleManager = $moduleManager;
        $this->moduleFactory = $moduleFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentity()
    {
        return 'm__seo_compatibility';
    }

    /**
     * {@inheritdoc}
     */
    public function isDisplayed()
    {
        $moduleMainName = 'Mirasvit_Seo';
        $moduleName = 'Mirasvit_SeoSitemap';
        if ($this->moduleManager->isEnabled($moduleName)) {
            $module = $this->moduleFactory->create()->load($moduleMainName);
            if ($module->getInstalledVersion() && version_compare($module->getInstalledVersion(), '2.0.1', '<')) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getText()
    {
        return 'For full compatibility with current Mirasvit Knowledge Base'
            . ' version please update Mirasvit Seo to version 2.0.1 or higher';
    }

    /**
     * {@inheritdoc}
     */
    public function getSeverity()
    {
        return self::SEVERITY_MAJOR;
    }
}
