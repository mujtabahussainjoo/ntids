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



$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/** @var $repository \Magento\Customer\Api\CustomerRepositoryInterface */
$repository = $objectManager->create('Magento\Customer\Api\CustomerRepositoryInterface');
$customer = $objectManager->create('Magento\Customer\Model\Customer');

/* @var Magento\Customer\Model\Customer $customer */
$customer->setWebsiteId(1)
->setId(1)
->setEmail('customer@example.com')
->setPassword('password')
->setGroupId(1)
->setStoreId(1)
->setIsActive(1)
->setPrefix('Mr.')
->setFirstname('John')
->setMiddlename('A')
->setLastname('Smith')
->setSuffix('Esq.')
->setDefaultBilling(1)
->setDefaultShipping(1)
->setTaxvat('12')
->setGender(0);

$customer->isObjectNew(true);
$customer->save();
