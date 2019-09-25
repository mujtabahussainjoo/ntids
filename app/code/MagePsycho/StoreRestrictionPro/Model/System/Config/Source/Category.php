<?php

namespace MagePsycho\StoreRestrictionPro\Model\System\Config\Source;

/**
 * @category   MagePsycho
 * @package    MagePsycho_StoreRestrictionPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Category implements  \Magento\Framework\Option\ArrayInterface
{
    private $_options;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    private $categoryHelper;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    private $categoryFactory;

    private $storeManager;

    public function __construct(
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->categoryHelper   = $categoryHelper;
        $this->categoryFactory  = $categoryFactory;
        $this->storeManager     = $storeManager;
    }

    public function buildCategoryOptions($categoryId)
    {
        #return $this->categoryHelper->getStoreCategories(true, false, true);
        $collection = $this->categoryFactory->create()
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter(
                'parent_id',
                ['eq' => $categoryId]
            )
            ->addAttributeToSort('position')
        ;

        foreach ($collection as $category) {
            $indentation = '';
            if ($category->getLevel() > 2) {
                $indentation .= str_repeat('... ', $category->getLevel() - 2);
            }
            $this->_options[] = [
                'label' => $indentation . $category->getName(),
                'value' => $category->getId()
            ];
            $this->buildCategoryOptions($category->getId());
        }
    }

    public function getAllOptions($withEmpty = false)
    {
        if (is_null($this->_options)) {
            // @todo add category tree as you see in Products > Categories
            $storeId         = 1;
            $rootCategoryId  = $this->storeManager->getStore($storeId)->getRootCategoryId();
            $this->buildCategoryOptions($rootCategoryId);
        }
        $options = $this->_options;
        if ($withEmpty) {
            array_unshift($options, ['value' => '', 'label' => '']);
        }
        return $options;
    }

    public function getOptionsArray($withEmpty = true)
    {
        $options = array();
        foreach ($this->getAllOptions($withEmpty) as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }

    public function getOptionText($value)
    {
        $options = $this->getAllOptions(false);
        foreach ($options as $item) {
            if ($item['value'] == $value) {
                return $item['label'];
            }
        }
        return false;
    }

    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

    public function toOptionHash($withEmpty = true)
    {
        return $this->getOptionsArray($withEmpty);
    }
}