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
 * @package   mirasvit/module-core
 * @version   1.2.75
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Core\Controller\Lc;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Mirasvit\Core\Model\ModuleFactory;
use Mirasvit\Core\Model\LicenseFactory;

class Index extends Action
{
    /**
     * @var ModuleFactory
     */
    private $moduleFactory;

    /**
     * @var LicenseFactory
     */
    private $licenseFactory;

    public function __construct(
        ModuleFactory $moduleFactory,
        LicenseFactory $licenseFactory,
        Context $context
    ) {
        $this->moduleFactory = $moduleFactory;
        $this->licenseFactory = $licenseFactory;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD)
     */
    public function execute()
    {
        echo '<pre>';
        $module = $this->moduleFactory->create();
        foreach ($module->getInstalledModules() as $moduleName) {
            $moduleName = str_replace('Mirasvit_', '', $moduleName);

            echo $moduleName;

            $info = $module->getComposerInformation('Mirasvit_' . $moduleName);
            if ($info) {
                echo ' ' . $info['version'];
            }

            $license = $this->licenseFactory->create();

            echo ' ' . $license->load('\\' . $moduleName);

            $license->clear();

            echo ' = ' . $license->getStatus('\\' . $moduleName);

            echo PHP_EOL;
        }

        exit;
    }
}
