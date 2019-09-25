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



namespace Mirasvit\Helpdesk\Block\Contact;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\ObjectManager;

class Kb extends Template
{
    /**
     * @var \Mirasvit\Kb\Model\ResourceModel\Article\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Context
     */
    protected $context;

    public function __construct(
        Context $context
    ) {
        $om = ObjectManager::getInstance();

        $this->collectionFactory = $om->create('Mirasvit\Kb\Model\ResourceModel\Article\CollectionFactory');
        $this->context           = $context;

        $this->setTemplate('Mirasvit_Helpdesk::contact/form/kb_result.phtml');

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Filter\FilterManager
     */
    public function getFilterManager()
    {
        return $this->filterManager;
    }

    /**
     * @return string
     */
    public function getSearchQuery()
    {
        return $this->context->getRequest()->getParam('s');
    }

    /**
     * @return \Mirasvit\Kb\Model\ResourceModel\Article\Collection
     */
    public function getCollection()
    {
        $search = $this->collectionFactory->create()->getSearchInstance();

        $collection = $this->collectionFactory->create();
        $collection->addStoreIdFilter($this->context->getStoreManager()->getStore()->getId());

        $search->joinMatched($this->getSearchQuery(), $collection, 'main_table.article_id');

        $collection->setPageSize(5);

        return $collection;
    }
}
