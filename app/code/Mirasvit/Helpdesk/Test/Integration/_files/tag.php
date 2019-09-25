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



/** @var $objectManager \Magento\TestFramework\ObjectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$currentStore = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
$otherStore = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore('fixturestore')->getId();

/** @var $tag \Mirasvit\Helpdesk\Model\Tag */
$tag = $objectManager->create('Mirasvit\Helpdesk\Model\Tag');
$tag->setStores(
    [$currentStore, $otherStore]
)->save();
