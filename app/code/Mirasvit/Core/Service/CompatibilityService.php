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



namespace Mirasvit\Core\Service;

use Magento\Framework\App\ObjectManager;

class CompatibilityService
{
    public static function is20()
    {
        list($a, $b,) = explode('.', self::getVersion());

        return $a == 2 && $b == 0;
    }

    public static function is21()
    {
        list($a, $b,) = explode('.', self::getVersion());

        return $a == 2 && $b == 1;
    }

    public static function is22()
    {
        list($a, $b,) = explode('.', self::getVersion());

        return $a == 2 && $b == 2;
    }

    public static function is23()
    {
        list($a, $b,) = explode('.', self::getVersion());

        return $a == 2 && $b == 3;
    }

    public static function getVersion()
    {
        /** @var \Magento\Framework\App\ProductMetadata $metadata */
        $metadata = self::getObjectManager()->get('Magento\Framework\App\ProductMetadata');

        return $metadata->getVersion();
    }

    /**
     * @return ObjectManager
     */
    public static function getObjectManager()
    {
        return ObjectManager::getInstance();
    }
}
