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



namespace Mirasvit\Kb\Block\Tag;

class Listing extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mirasvit\Kb\Model\ResourceModel\Tag\CollectionFactory
     */
    protected $tagCollectionFactory;

    /**
     * @var \Mirasvit\Kb\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Kb\Helper\Tag
     */
    protected $kbTag;

    /**
     * @var \Mirasvit\Kb\Helper\Data
     */
    protected $kbData;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Kb\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory
     * @param \Mirasvit\Kb\Model\Config $config
     * @param \Mirasvit\Kb\Helper\Tag $kbTag
     * @param \Mirasvit\Kb\Helper\Data $kbData
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Mirasvit\Kb\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory,
        \Mirasvit\Kb\Model\Config $config,
        \Mirasvit\Kb\Helper\Tag $kbTag,
        \Mirasvit\Kb\Helper\Data $kbData,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->tagCollectionFactory = $tagCollectionFactory;
        $this->config = $config;
        $this->kbTag = $kbTag;
        $this->kbData = $kbData;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @var \Mirasvit\Kb\Model\ResourceModel\Tag\CollectionFactory
     */
    protected $collection;

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            /* @noinspection PhpUndefinedMethodInspection */
            $headBlock->setTitle(__('Tags List'));
        }
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link'  => $this->context->getUrlBuilder()->getBaseUrl(),
            ]);
            $root = $this->kbData->getRootCategory();
            $breadcrumbs->addCrumb('kbase' . $root->getUrlKey(), [
                'label' => $root->getName(),
                'title' => $root->getName(),
                'link'  => $root->getUrl(),
            ]);
            $breadcrumbs->addCrumb('kbtagslist', [
                'label' => __('Tags'),
                'title' => __('Tags'),
            ]);
        }
    }

    /**
     * @return \Mirasvit\Kb\Model\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return $this
     */
    public function getTagCollection()
    {
        if (!$this->collection) {
            $this->collection = $this->tagCollectionFactory->create()
                ->joinNotEmptyFields();
        }

        return $this->collection;
    }
}
