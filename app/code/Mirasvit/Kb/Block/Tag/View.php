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

class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mirasvit\Kb\Helper\Tag
     */
    protected $kbTag;

    /**
     * @var \Mirasvit\Kb\Helper\Data
     */
    protected $kbData;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Kb\Helper\Tag                          $kbTag
     * @param \Mirasvit\Kb\Helper\Data                         $kbData
     * @param \Magento\Framework\Registry                      $registry
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Mirasvit\Kb\Helper\Tag $kbTag,
        \Mirasvit\Kb\Helper\Data $kbData,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->kbTag = $kbTag;
        $this->kbData = $kbData;
        $this->registry = $registry;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $tag = $this->getTag();
        if (!$tag) {
            return;
        }
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(__('Tag %1', $tag->getName()));
        }
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link' => $this->context->getUrlBuilder()->getBaseUrl(),
            ]);
            $root = $this->kbData->getRootCategory();
            $breadcrumbs->addCrumb('kbase'.$root->getUrlKey(), [
                'label' => $root->getName(),
                'title' => $root->getName(),
                'link' => $root->getUrl(),
            ]);
            $breadcrumbs->addCrumb('kbtagslist', [
                'label' => __('Tags'),
                'title' => __('Tags'),
                'link' => $this->kbTag->getListUrl(),
            ]);
            $breadcrumbs->addCrumb('kbase'.$tag->getUrlKey(), [
                'label' => $tag->getName(),
                'title' => $tag->getName(),
            ]);
        }
    }

    /**
     * @return \Mirasvit\Kb\Model\Tag
     */
    public function getTag()
    {
        return $this->registry->registry('current_tag');
    }

    /************************/
}
