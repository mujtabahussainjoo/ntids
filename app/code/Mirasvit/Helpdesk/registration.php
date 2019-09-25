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


$path = __DIR__;
$ds = DIRECTORY_SEPARATOR;
if (strpos($path, 'app'.$ds.'code'.$ds.'Mirasvit') === false) {
    $basePath = dirname(dirname(dirname(__DIR__)));
} else {
    $basePath = dirname(dirname(dirname(dirname(__DIR__))));
}
$registration = $basePath.$ds.'vendor'.$ds.'mirasvit'.$ds.'module-helpdesk'.$ds.'src'.$ds.'Helpdesk'.$ds.
    'registration.php';
if (file_exists($registration)) {
    # module was already installed via composer
    return;
}

$libPath = $basePath.$ds.'app'.$ds.'code'.$ds.'Mirasvit'.$ds.'lib'.$ds;
if (is_dir($libPath)) {
    /** @var \Composer\Autoload\ClassLoader $loader */
    $loader = require $basePath.$ds.'vendor'.$ds.'autoload.php';
    $loader->add('Mirasvit_', $libPath);
}

\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Mirasvit_Helpdesk',
    __DIR__
);