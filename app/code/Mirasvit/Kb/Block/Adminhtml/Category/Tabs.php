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



namespace Mirasvit\Kb\Block\Adminhtml\Category;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Default Attribute Tab Block.
     *
     * @var string
     */
    protected $attributeTabBlock =
        'Magento\Catalog\Block\Adminhtml\Category\Tab\Attributes';

    /**
     * @var string
     */
    protected $_template = 'Magento_Backend::widget/tabshoriz.phtml';//@codingStandardsIgnoreLine

    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Catalog helper.
     *
     * @var \Magento\Catalog\Helper\Catalog
     */
    protected $helperCatalog;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context                                   $context
     * @param \Magento\Framework\Json\EncoderInterface                                  $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session                                       $authSession
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Helper\Catalog                                           $helperCatalog
     * @param \Magento\Framework\Registry                                               $registry
     * @param array                                                                     $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $collectionFactory,
        \Magento\Catalog\Helper\Catalog $helperCatalog,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->coreRegistry = $registry;
        $this->helperCatalog = $helperCatalog;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    /**
     * Initialize Tabs.
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('category_info_tabs');
        $this->setDestElementId('category_tab_content');
        $this->setTitle(__('Category Data'));
    }

    /**
     * Retrieve category object.
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory()
    {
        return $this->coreRegistry->registry('current_category');
    }

    /**
     * Getting attribute block name for tabs.
     *
     * @return string
     */
    public function getAttributeTabBlock()
    {
        if ($block = $this->helperCatalog->getCategoryAttributeTabBlock()) {
            return $block;
        }

        return $this->attributeTabBlock;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $this->addTab('general', [
            'label'   => __('General'),
            'content' => $this->getLayout()->createBlock('Mirasvit\Kb\Block\Adminhtml\Category\Edit\Tab\General')
                ->toHtml(),
        ]);

        $this->addTab('design', [
            'label'   => __('Design & Content'),
            'content' => $this->getLayout()->createBlock('Mirasvit\Kb\Block\Adminhtml\Category\Edit\Tab\Design')
                ->toHtml(),
        ]);

        if ($this->getCategory()->getId() > 1) {
            $this->addTab('seo', [
                'label'   => __('Meta Information'),
                'content' => $this->getLayout()->createBlock('Mirasvit\Kb\Block\Adminhtml\Category\Edit\Tab\Seo')
                    ->toHtml(),
            ]);
        }

        // dispatch event add custom tabs
        $this->_eventManager->dispatch('adminhtml_kb_category_tabs', ['tabs' => $this]);

        return parent::_prepareLayout();
    }
}
