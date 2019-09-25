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

class Cloud extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mirasvit\Kb\Model\ResourceModel\Tag\CollectionFactory
     */
    protected $tagCollectionFactory;

    /**
     * @var \Mirasvit\Kb\Helper\Tag
     */
    protected $kbTag;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Kb\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory
     * @param \Mirasvit\Kb\Helper\Tag                                $kbTag
     * @param \Magento\Framework\Registry                            $registry
     * @param \Magento\Framework\View\Element\Template\Context       $context
     * @param array                                                  $data
     */
    public function __construct(
        \Mirasvit\Kb\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory,
        \Mirasvit\Kb\Helper\Tag $kbTag,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->tagCollectionFactory = $tagCollectionFactory;
        $this->kbTag = $kbTag;
        $this->registry = $registry;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @return \Mirasvit\Kb\Model\ResourceModel\Tag\Collection
     */
    public function getTagCollection()
    {
        $tagIds = $this->kbTag->getStoreTagIds();
        $collection = $this->tagCollectionFactory->create()
            ->addFieldToFilter('tag_id', (count($tagIds)) ? $tagIds : '%')
            ->joinNotEmptyFields()
            ->setOrder('ratio')
            ->setPageSize(20);

        return $collection;
    }

    /**
     * @return \Mirasvit\Kb\Model\Category
     */
    public function getCategory()
    {
        return $this->registry->registry('kb_current_category');
    }
}
