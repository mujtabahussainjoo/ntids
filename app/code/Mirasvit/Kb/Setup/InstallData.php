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



namespace Mirasvit\Kb\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Init.
     *
     * @param \Mirasvit\Kb\Model\CategoryFactory $categoryFactory
     */
    public function __construct(\Mirasvit\Kb\Model\CategoryFactory $categoryFactory)
    {
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter) 
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $category = $this->categoryFactory->create();
        $category
            ->setName('Root Category')
            ->setParentId(null)
            ->setPath('1')
            ->setLevel(0)
            ->setIsActive(true)
            ->setPosition(0)
            ->setChildrenCount(0)
            ->save();

        $category = $this->categoryFactory->create();
        $category
            ->setName('Knowledge base')
            ->setParentId(1)
            ->setPath('1/2')
            ->setLevel(1)
            ->setStoreIds([0])
            ->setIsActive(true)
            ->setPosition(1)
            ->save();
    }
}
