<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/


namespace Aheadworks\Rbslider\Model\Rule\Condition\Product;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Config\Model\Config\Backend\Admin\Custom;

/**
 * Class Attributes
 *
 * @package Aheadworks\Rbslider\Model\Rule\Condition\Product
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Attributes extends \Magento\Rule\Model\Condition\Product\AbstractProduct
{
    /**
     * Overwrite condition type for multiselect conditions
     *
     * @var array
     */
    private $multiselectOverwrite = [
        'eq' => 'mEq',
        'neq' => 'mNeq',
        'in' => 'finset',
        'nin' => 'nfinset',
    ];

    /**
     * Overwrite condition type for category conditions
     *
     * @var array
     */
    private $categoryOverwrite = [
        'eq' => 'finset',
        'neq' => 'nfinset',
        'in' => 'finset',
        'nin' => 'nfinset',
    ];

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    private $metadataPool;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Magento\Eav\Model\Config $config
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Eav\Model\Config $config,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $backendData,
            $config,
            $productFactory,
            $productRepository,
            $productResource,
            $attrSetCollection,
            $localeFormat,
            $data
        );
        $this->storeManager = $storeManager;
        $this->setType(Attributes::class);
        $this->setValue(null);
        $this->metadataPool = $metadataPool;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Prepare child rules option list
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $attributes = $this->loadAttributeOptions()->getAttributeOption();
        $conditions = [];
        foreach ($attributes as $code => $label) {
            $conditions[] = ['value' => $this->getType() . '|' . $code, 'label' => $label];
        }

        return ['value' => $conditions, 'label' => __('Product Attributes')];
    }

    /**
     * Retrieve value element chooser URL
     *
     * @return string
     */
    public function getValueElementChooserUrl()
    {
        $url = false;
        switch ($this->getAttribute()) {
            case 'sku':
            case 'category_ids':
                $url = 'catalog_rule/promo_widget/chooser/attribute/' . $this->getAttribute();
                if ($this->getJsFormObject()) {
                    $url .= '/form/' . $this->getJsFormObject();
                } elseif ($this->getRule()->getJsFormObject()) {
                    $url .= '/form/' . $this->getRule()->getJsFormObject();
                }
                break;
            default:
                break;
        }
        return $url !== false ? $this->_backendData->getUrl($url) : '';
    }

    /**
     * Collect the valid attributes
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     * @return $this
     * @throws \Exception
     */
    public function collectValidatedAttributes($productCollection)
    {
        $linkField = 'entity_id';
        $aliasLinkField = $this->metadataPool->getMetadata(CategoryInterface::class)->getLinkField();
        $configPath = Custom::XML_PATH_CATALOG_FRONTEND_FLAT_CATALOG_PRODUCT;
        if (!$this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE)) {
            $linkField = $aliasLinkField;
        }

        $attribute = $this->getAttributeObject();
        $attributeCode = $attribute->getAttributeCode();
        if ($attribute->getAttributeCode() == 'category_ids') {
            if (!$productCollection->getFlag('aw_rbslider_collection_category_joined')) {
                $catProductTable = $this->_productResource->getTable('catalog_category_product');
                $productCollection
                    ->getSelect()
                    ->joinLeft(
                        ['cat_product' => $catProductTable],
                        "e.{$linkField} = cat_product.product_id",
                        []
                    )
                    ->group("e.{$linkField}")
                ;
                $condition = $this->prepareSqlCondition("GROUP_CONCAT(cat_product.category_id)", $this->getValue());
                $productCollection->getSelect()->having($condition);
                $productCollection->setFlag('aw_rbslider_collection_category_joined', true);
            }
        } else {
            if ($attribute->isStatic()) {
                $condition = $this->prepareSqlCondition("e.{$attributeCode}", $this->getValue());
                $productCollection = $this->addWhereConditionToCollection($productCollection, $condition);
            } else {
                $table = $attribute->getBackendTable();
                $tableAlias = 'attr_table_' .$attribute->getId();
                if (!$productCollection->getFlag("aw_rbslider_{$tableAlias}_joined")) {
                    $productCollection
                        ->getSelect()
                        ->joinLeft(
                            [$tableAlias => $table],
                            "e.{$linkField} = {$tableAlias}.{$aliasLinkField}",
                            null
                        )
                    ;
                    $productCollection->setFlag("aw_rbslider_{$tableAlias}_joined", true);
                }
                $conditions = [];
                $conditions[] = $this->_productResource->getConnection()->prepareSqlCondition(
                    "{$tableAlias}.attribute_id",
                    ['eq' => $attribute->getId()]
                );
                if ($attribute->isScopeGlobal()) {
                    $conditions[] = $this->_productResource->getConnection()->prepareSqlCondition(
                        "{$tableAlias}.store_id",
                        ['eq' => 0]
                    );
                } else {
                    $conditions[] = $this->_productResource->getConnection()->prepareSqlCondition(
                        "{$tableAlias}.store_id",
                        [['eq' => $this->storeManager->getStore()->getId()], ['eq' => 0]]
                    );
                }
                $conditions[] = $this->prepareSqlCondition("{$tableAlias}.value", $this->getValue());
                $condition = join(' AND ', $conditions);

                $productCollection = $this->addWhereConditionToCollection($productCollection, $condition);
            }
        }
        return $this;
    }

    /**
     * Prepare condition for sql query
     *
     * @param string $field
     * @param string $value
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function prepareSqlCondition($field, $value)
    {
        $method = $this->getMethod();
        $callback = $this->getPrepareValueCallback();
        if ($callback) {
            $value = call_user_func([$this, $callback], $value);
        }

        if ($this->getAttributeObject()->getAttributeCode() == 'category_ids'
            && array_key_exists($method, $this->categoryOverwrite)
        ) {
            $method = $this->categoryOverwrite[$method];
        }

        if ($this->getAttributeObject()->getFrontendInput() == 'multiselect'
            && array_key_exists($method, $this->multiselectOverwrite)
        ) {
            $method = $this->multiselectOverwrite[$method];
        }

        if ($method =='in' || $method =='nin' || !is_array($value)) {
            $condition = $this->_productResource->getConnection()->prepareSqlCondition(
                $field,
                [$method => $value]
            );
            return $condition;
        }
        if ($method == 'mEq' || $method == 'mNeq') {
            $count = 0;
            if (is_array($value)) {
                $count = count($value);
                $value = implode('|', $value);
            }

            if ($count <= 1) {
                $condition = "REGEXP '^{$value}$'";
            } else {
                // Remove from count value first and last position for regexp
                $centerElementCount = $count - 2;
                $centerValue = str_repeat(",*({$value})", $centerElementCount);
                $condition = "REGEXP '^({$value}){$centerValue},*({$value})$'";
            }
            if ($method == 'mNeq') {
                $condition = 'NOT ' . $condition;
            }

            return $field . ' ' . $condition;
        }

        $conditions = [];
        foreach ($value as $item) {
            if ($method == 'nfinset') {
                $conditions[] = "NOT FIND_IN_SET('{$item}', {$field})";
                continue;
            }
            $conditions[] =  $this->_productResource->getConnection()->prepareSqlCondition(
                $field,
                [$method => $item]
            );
        }
        if ($method == 'nlike' || $method == 'nfinset') {
            $condition  = join(' AND ', $conditions);
        } else {
            $condition  = join(' OR ', $conditions);
        }
        return $condition;
    }

    /**
     * Get method for sql condition
     *
     * @return string
     */
    protected function getMethod()
    {
        $oppositeOperators = [
            '<' => '>=',
            '>' => '<=',
            '==' => '!=',
            '<=' => '>',
            '>=' => '<',
            '!=' => '==',
            '{}' => '!{}',
            '!{}' => '{}',
            '()' => '!()',
            '!()' => '()',
            '[]' => '![]',
            '![]' => '[]',
        ];

        $operator = $this->getOperator();
        if (true !== $this->getTrue()) {
            $operator = $oppositeOperators[$operator];
        }

        $methods = [
            '<' => 'lt',
            '>' => 'gt',
            '==' => 'eq',
            '<=' => 'lteq',
            '>=' => 'gteq',
            '!=' => 'neq',
            '{}' => 'like',
            '!{}' => 'nlike',
            '()' => 'in',
            '!()' => 'nin',
            '[]' => 'finset',
            '![]' => 'nfinset',
        ];

        $method = 'eq';
        if (array_key_exists($operator, $methods)) {
            $method = $methods[$operator];
        }
        return $method;
    }

    /**
     * Get callback for prepare values for sql conditions
     *
     * @return null|string
     */
    protected function getPrepareValueCallback()
    {
        $callbacks = [
            '==' => 'prepareValue',
            '<' => 'prepareValue',
            '>' => 'prepareValue',
            '<=' => 'prepareValue',
            '>=' => 'prepareValue',
            '!=' => 'prepareValue',
            '{}' => 'prepareLikeValue',
            '!{}' => 'prepareLikeValue',
            '()' => 'prepareValue',
            '!()' => 'prepareValue',
            '[]' => 'prepareValue',
            '![]' => 'prepareValue',
            'between' => 'prepareBetweenValue'
        ];
        $operator = $this->getOperator();

        $callback = null;
        if (array_key_exists($operator, $callbacks)) {
            $callback = $callbacks[$operator];
        }

        return $callback;
    }

    /**
     * Prepare value for sql conditions
     *
     * @param string $value
     * @return array
     */
    protected function prepareValue($value)
    {
        if (is_string($value)) {
            $value = explode(',', $value);
        }
        if (!is_array($value)) {
            $value = [$value];
        }
        $value = array_map('trim', $value);

        return $value;
    }

    /**
     * Prepare Like value for sql conditions
     *
     * @param string $value
     * @return array
     */
    protected function prepareLikeValue($value)
    {
        if (is_string($value)) {
            $value = explode(',', $value);
        }
        if (!is_array($value)) {
            $value = [$value];
        }
        $value = array_map('trim', $value);
        foreach ($value as $key => $item) {
            $value[$key] = '%' . $item . '%';
        }
        return $value;
    }

    /**
     * Add where conditions to collection
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param string $condition
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function addWhereConditionToCollection($collection, $condition)
    {
        if ($this->getAggregator() && $this->getAggregator() === 'all') {
            $collection->getSelect()->where($condition);
        } else {
            $collection->getSelect()->orWhere($condition);
        }
        return $collection;
    }

    /**
     * Return default operator
     *
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        if (null === $this->_defaultOperatorInputByType) {
            parent::getDefaultOperatorInputByType();
            $this->_defaultOperatorInputByType['multiselect'] = ['==', '!=', '()', '!()'];
        }
        return $this->_defaultOperatorInputByType;
    }
}
