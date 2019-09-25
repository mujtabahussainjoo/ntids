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



namespace Mirasvit\Kb\Model\ResourceModel\Category;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection implements
    \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Framework\Data\Collection\EntityFactoryInterface
     */
    protected $entityFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Data\Collection\Db\FetchStrategyInterface
     */
    protected $fetchStrategy;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|string|null
     */
    protected $connection;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected $resource;

    /**
     * @var \Mirasvit\Kb\Helper\Data
     */
    protected $kbData;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory
     * @param \Psr\Log\LoggerInterface                                     $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     * @param \Mirasvit\Kb\Helper\Data                                     $kbData
     * @param \Magento\Framework\DB\Adapter\AdapterInterface               $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb         $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mirasvit\Kb\Helper\Data $kbData,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->entityFactory = $entityFactory;
        $this->logger = $logger;
        $this->fetchStrategy = $fetchStrategy;
        $this->eventManager = $eventManager;
        $this->storeManager = $storeManager;
        $this->connection = $connection;
        $this->resource = $resource;
        $this->kbData = $kbData;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Kb\Model\Category', 'Mirasvit\Kb\Model\ResourceModel\Category', 'category_id');
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function addStoreIdFilter($storeId)
    {
        $rootId = (int) $this->kbData->getRootCategory($storeId)->getId();
        $this->getSelect()
            ->where("path LIKE '%/?/%'", $rootId);

        return $this;
    }

    /**
     * @param int $storeId
     *
     * @return $this
     */
    public function addRootStoreIdFilter($storeId)
    {
        $this->getSelect()
            ->where("EXISTS (SELECT * FROM `{$this->getTable('mst_kb_category_store')}`
                AS `category_store_table`
                WHERE main_table.category_id = category_store_table.as_category_id
                AND category_store_table.as_store_id in (?))", [0, $storeId]);

        return $this;
    }

    /**
     * @param int   $storeId
     *
     * @return $this
     */
    public function addCategoryStoreIdFilter($storeId)
    {
        $rootId = (int) $this->kbData->getRootCategory($storeId)->getId();
        $this->getSelect()
            ->where("EXISTS (SELECT * FROM `{$this->getTable('mst_kb_category_store')}`
                AS `category_store_table`
                WHERE category_store_table.as_category_id = ".$rootId."
                AND category_store_table.as_store_id in (?))", [0, $storeId])
            ->where("path LIKE '%?%'", [$rootId])
        ;

        return $this;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('category_id', 'name');
    }

    /**
     * @return array
     */
    public function getOptionArray()
    {
        $arr = [];
        foreach ($this as $item) {
            $arr[$item->getId()] = $item->getName();
        }

        return $arr;
    }

    /**
     * @param array $articleIds
     *
     * @return $this
     */
    public function withArticles($articleIds)
    {
        $this->getSelect()
            ->joinLeft(
                ['article_category' => $this->getTable('mst_kb_article_category')],
                'main_table.category_id = article_category.ac_category_id'
            )
            ->where('article_category.ac_article_id IN (?)', $articleIds);

        return $this;
    }

    /**
     * @param array $categoryIds
     *
     * @return $this
     */
    public function addCategoryIdFilter($categoryIds)
    {
        $this->getSelect()
            ->where('category_id in (?)', (array) $categoryIds);

        return $this;
    }

    /**
     * Add attribute to select result set.
     * Backward compatibility with EAV collection.
     *
     * @param string $attribute
     *
     * @return $this
     */
    public function addAttributeToSelect($attribute)
    {
        if (is_array($attribute)) {
            foreach ($attribute as $attr) {
                $this->addFieldToSelect($this->_attributeToField($attr));
            }
        } else {
            $this->addFieldToSelect($this->_attributeToField($attribute));
        }

        return $this;
    }

    /**
     * Specify collection select filter by attribute value
     * Backward compatibility with EAV collection.
     *
     * @param string|\Magento\Eav\Model\Entity\Attribute $attribute
     * @param array|int|string|null                      $condition
     *
     * @return $this
     */
    public function addAttributeToFilter($attribute, $condition = null)
    {
        $this->addFieldToFilter($this->_attributeToField($attribute), $condition);

        return $this;
    }

    /**
     * Check if $attribute is \Magento\Eav\Model\Entity\Attribute and convert to string field name.
     *
     * @param string|\Magento\Eav\Model\Entity\Attribute $attribute
     *
     * @return string
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _attributeToField($attribute)
    {
        $field = false;
        if (is_string($attribute)) {
            $field = $attribute;
        } elseif ($attribute instanceof \Magento\Eav\Model\Entity\Attribute) {
            $field = $attribute->getAttributeCode();
        }
        if (!$field) {
            throw new \Magento\Framework\Exception\LocalizedException(__('We cannot determine the field name.'));
        }

        return $field;
    }
}
