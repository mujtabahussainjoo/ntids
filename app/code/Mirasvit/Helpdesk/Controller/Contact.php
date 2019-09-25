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


namespace Mirasvit\Helpdesk\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Mirasvit\Helpdesk\Helper\Process as ProcessHelper;

abstract class Contact extends Action
{
    /**
     * @var ProcessHelper
     */
    protected $processHelper;

    /**
     * @var \Magento\Framework\App\Action\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    public function __construct(
        ProcessHelper $processHelper,
        Context $context
    ) {
        $this->processHelper = $processHelper;
        $this->context = $context;
        $this->resultFactory = $context->getResultFactory();

        parent::__construct($context);
    }

    /**
     * @todo move to core
     *
     * @return bool
     */
    protected function isSecure()
    {
        $isHTTPS = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443);

        return $isHTTPS;
    }

    //    /**
    //     * @param string $q
    //     * @return object
    //     */
    //    public function getArticleCollection($q)
    //    {
    //        $collection = $this->articleCollectionFactory->create()
    //            ->addFieldToFilter('main_table.is_active', true)
    //            ->addStoreIdFilter($this->storeManager->getStore()->getId())
    //            ;
    //        $this->kbData->addSearchFilter($collection, $q);
    //        $collection->setPageSize(4);
    //
    //        return $collection;
    //    }
}
