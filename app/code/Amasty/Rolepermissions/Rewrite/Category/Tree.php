<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Rewrite\Category;

use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Framework\Data\Tree\Node;

class Tree extends \Magento\Catalog\Block\Adminhtml\Category\Tree
{
    /**
     * @var \Amasty\Rolepermissions\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    private $jsonDecoder;

    /**
     * @var \Amasty\Rolepermissions\Model\Rule
     */
    public $currentRule;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTree,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\DB\Helper $resourceHelper,
        \Magento\Backend\Model\Auth\Session $backendSession,
        \Amasty\Rolepermissions\Helper\Data $helper,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->jsonDecoder = $jsonDecoder;
        parent::__construct(
            $context,
            $categoryTree,
            $registry,
            $categoryFactory,
            $jsonEncoder,
            $resourceHelper,
            $backendSession,
            $data
        );
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->setTemplate('Magento_Catalog::catalog/category/tree.phtml');

        parent::_prepareLayout();

        $rule = $this->helper->currentRule();

        if (($rule->getScopeWebsites() || $rule->getScopeStoreviews())
            && $this->getChildBlock('add_sub_button') === false
        ) {
            $addUrl = $this->getUrl("*/*/add", ['_current' => false, 'id' => null, '_query' => false]);
            $this->addChild(
                'add_sub_button',
                'Magento\Backend\Block\Widget\Button',
                [
                    'label' => __('Add Subcategory'),
                    'onclick' => "addNew('" . $addUrl . "', false)",
                    'class' => 'add',
                    'id' => 'add_subcategory_button',
                    'style' => $this->canAddSubCategory() ? '' : 'display: none;'
                ]
            );
        }

        if ($allowedCategories = $rule->getCategories()) {
            $this->unsetChild('add_root_button');

            $category = $this->_coreRegistry->registry('current_category');

            if ($category && !in_array($category->getId(), $allowedCategories)) {
                $this->unsetChild('add_sub_button');
            }
        }

        return $this;
    }

    protected function _getNodeJson($node, $level = 0)
    {
        $node = parent::_getNodeJson($node, $level);

        $rule = $this->helper->currentRule();

        if ($rule->getCategories() && !in_array($node['id'], $rule->getCategories())) {
            $node['disabled'] = true;
            $node['allowChildren'] = false;
        }

        return $node;
    }

    protected function _toHtml()
    {
        $html = parent::_toHtml();

        $code = "if (node.disabled) return;";
        $html = preg_replace('|(categoryClick\s*:\s*function[^{]+\{\s*)|s', "\\1$code\n", $html);

        $storeId = (int)$this->_request->getParam('store');
        $storeGroupId = $this->_storeManager->getStore($storeId)->getStoreGroupId();
        $root = $this->_storeManager->getGroup($storeGroupId)->getRootCategoryId();

        $rule = $this->helper->currentRule();
        if ($rule->getCategories()
            && !in_array(
                $root,
                $rule->getCategories()
            )
        ) {
            $defaultParams = "disabled:true,\nallowChildren:false,";
            $html =
                preg_replace('|defaultLoadTreeParams = {[\n\s]+parameters\s*:\s*{\n|s', "\\0$defaultParams\n", $html);
        }

        return $html;
    }

    public function getSuggestedCategoriesJson($namePart)
    {
        $this->currentRule = $this->helper->currentRule();

        $parentResult = parent::getSuggestedCategoriesJson($namePart);

        if (!$this->currentRule->getCategories()) {
            return $parentResult;
        }

        $decodedResult = $this->jsonDecoder->decode($parentResult);

        foreach ($decodedResult as &$root) {
            $this->deactivateCategories($root);
        }

        return $this->_jsonEncoder->encode($decodedResult);
    }

    private function deactivateCategories(&$node)
    {
        if ($this->currentRule->getCategories()
            && !in_array($node['id'], $this->currentRule->getCategories())
        ) {
            $node['is_active'] = 0;
        }

        if (!isset($node['children'])) {
            return;
        }

        foreach ($node['children'] as &$child) {
            $this->deactivateCategories($child);
        }
    }
}
