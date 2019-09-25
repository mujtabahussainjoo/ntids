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



namespace Mirasvit\Kb\Block\Adminhtml\Category\Edit;

class Form extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @param \Magento\Framework\Registry           $registry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->request = $context->getRequest();
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('category/edit/form.phtml');
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $category = $this->getCategory();
        $categoryId = $this->getCategoryId();

        // Save button
        if (!$category->isReadonly()) {
            $this->addButton(
                'save',
                [
                    'id' => 'save',
                    'label' => __('Save Category'),
                    'class' => 'save primary save-category',
                    'data_attribute' => [
                        'mage-init' => [
                            'Magento_Catalog/catalog/category/edit' => [ //name of .js file
                                'url' => $this->getSaveUrl(),
                                'ajax' => true,
                            ],
                        ],
                    ],
                ]
            );
        }

        // Delete button
        if ($categoryId) { //&& !in_array($categoryId, $this->getRootIds()) && $category->isDeleteable()
            $this->addButton(
                'delete',
                [
                    'id' => 'delete',
                    'label' => __('Delete Category'),
                    'onclick' => "categoryDelete('".$this->getUrl(
                        'kbase/*/delete',
                        ['_current' => true]
                    )."')",
                    'class' => 'delete',
                ]
            );
        }

        // Reset button
        $resetPath = $categoryId ? 'kbase/*/edit' : 'kbase/*/add';
        $this->addButton(
            'reset',
            [
                    'id' => 'reset',
                    'label' => __('Reset'),
                    'onclick' => "categoryReset('".$this->getUrl($resetPath, ['_current' => true])."',true)",
                    'class' => 'reset',
                ]
        );

        $this->setChild(
            'tabs',
            $this->getLayout()->createBlock('\Mirasvit\Kb\Block\Adminhtml\Category\Tabs', 'tabs'.rand(0, 10))
        );

        return parent::_prepareLayout();
    }

    /**
     * Add button block as a child block or to global Page Toolbar block if available.
     *
     * @param string $buttonId
     * @param array  $data
     *
     * @return void
     */
    protected function addButton($buttonId, array $data)
    {
        $childBlockId = $buttonId.'_button';
        $button = $this->getButtonChildBlock($childBlockId);
        $button->setData($data);
        $block = $this->getLayout()->getBlock('page.actions.toolbar');
        if ($block) {
            $block->setChild($childBlockId, $button);
        } else {
            $this->setChild($childBlockId, $button);
        }
    }
    /**
     * Adding child block with specified child's id.
     *
     * @param string      $childId
     * @param null|string $blockClassName
     *
     * @return \Magento\Backend\Block\Widget
     */
    protected function getButtonChildBlock($childId, $blockClassName = null)
    {
        if (null === $blockClassName) {
            $blockClassName = 'Magento\Backend\Block\Widget\Button';
        }

        return $this->getLayout()->createBlock($blockClassName, $this->getNameInLayout().'-'.$childId);
    }

    /**
     * @return string
     */
    public function getTabsHtml()
    {
        return $this->getChildHtml('tabs');
    }

    /**
     * @return \Mirasvit\Kb\Model\Category
     */
    public function getCategory()
    {
        return $this->registry->registry('current_category');
    }

    /**
     * @return int|null
     */
    public function getCategoryId()
    {
        if ($this->getCategory()) {
            return $this->getCategory()->getId();
        }
    }

    /**
     * Get parent category id.
     *
     * @return int
     */
    public function getParentCategoryId()
    {
        return (int) $this->templateContext->getRequest()->getParam('parent');
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getHeader()
    {
        if ($this->getCategoryId()) {
            return $this->getCategory()->getName();
        } else {
            return __('New Category');
        }
    }

    /**
     * @param array $args
     *
     * @return string
     */
    public function getDeleteUrl(array $args = [])
    {
        $params = ['_current' => true];
        $params = array_merge($params, $args);

        return $this->getUrl('*/*/delete', $params);
    }

    /**
     * @param array $args
     *
     * @return string
     */
    public function getSaveUrl(array $args = [])
    {
        $params = ['_current' => false, '_query' => false,
            'store' => $this->getStore()->getId(),
            ];
        $params = array_merge($params, $args);

        return $this->getUrl('kbase/*/save', $params);
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store');

        return $this->_storeManager->getStore($storeId);
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        return $this->request->getParam('isAjax');
    }
    /************************/
}
