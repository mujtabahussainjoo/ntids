<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

namespace Amasty\Rolepermissions\Plugin\Catalog\Ui\Category;

/**
 * Class Tree
 * @package Amasty\Rolepermissions\Plugin\Catalog\Ui\Category
 */
class Tree
{
    /** @var \Amasty\Rolepermissions\Helper\Data */
    private $helper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * Tree constructor.
     * @param \Amasty\Rolepermissions\Helper\Data $helper
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\State $appState
     */
    public function __construct(
        \Amasty\Rolepermissions\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\State $appState
    ) {
        $this->helper = $helper;
        $this->request = $request;
        $this->appState = $appState;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Category\Collection $collection
     * @return void
     */
    public function beforeLoad(\Magento\Catalog\Model\ResourceModel\Category\Collection $collection)
    {
        if ($this->request->getModuleName() == 'api') {
            return;
        }

        if ($this->appState->getAreaCode() != \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
            return;
        }

        $rule = $this->helper->currentRule();
        $ruleCategories = $rule->getCategories();

        if ($ruleCategories) {
            $ruleCategories = $this->helper->getParentCategoriesIds($ruleCategories);
            $collection->addFieldToFilter('entity_id', ['in' => $ruleCategories]);
        }
    }
}
