<?php
/**
 * Application entry point
 *
 * Example - run a particular store or website:
 * --------------------------------------------
 * require __DIR__ . '/app/bootstrap.php';
 * $params = $_SERVER;
 * $params[\Magento\Store\Model\StoreManager::PARAM_RUN_CODE] = 'website2';
 * $params[\Magento\Store\Model\StoreManager::PARAM_RUN_TYPE] = 'website';
 * $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);
 * \/** @var \Magento\Framework\App\Http $app *\/
 * $app = $bootstrap->createApplication(\Magento\Framework\App\Http::class);
 * $bootstrap->run($app);
 * --------------------------------------------
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

  require __DIR__ . '/../app/bootstrap.php';
  $params = $_SERVER;
  $params[\Magento\Store\Model\StoreManager::PARAM_RUN_CODE] = 'alinta';
  $params[\Magento\Store\Model\StoreManager::PARAM_RUN_TYPE] = 'website';
  $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);
  $app = $bootstrap->createApplication(\Magento\Framework\App\Http::class);
  $bootstrap->run($app);