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



namespace Mirasvit\Kb\Block\Adminhtml\Helper\Form;

use Mirasvit\Kb\Model\ResourceModel\Category\Collection;
use Magento\Framework\AuthorizationInterface;

/**
 * Product form category field helper.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Category extends \Magento\Framework\Data\Form\Element\Multiselect
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * Backend data.
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendData;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var AuthorizationInterface
     */
    protected $authorization;

    /**
     * @param \Magento\Framework\Data\Form\Element\Factory                $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory      $factoryCollection
     * @param \Magento\Framework\Escaper                                  $escaper
     * @param \Magento\Backend\Helper\Data                                $backendData
     * @param \Magento\Framework\View\LayoutInterface                     $layout
     * @param \Magento\Framework\Json\EncoderInterface                    $jsonEncoder
     * @param AuthorizationInterface                                      $authorization
     * @param \Magento\Framework\Registry                                 $registry
     * @param array                                                       $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        AuthorizationInterface $authorization,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->jsonEncoder = $jsonEncoder;
        $this->backendData = $backendData;
        $this->authorization = $authorization;
        $this->registry = $registry;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->layout = $layout;
        if (!$this->isAllowed()) {
            $this->setType('hidden');
            $this->addClass('hidden');
        }
    }

    /**
     * Get values for select.
     *
     * @return array
     */
    public function getValues()
    {
        $collection = $this->_getCategoriesCollection();
        $values = $this->getValue();

        if (!is_array($values)) {
            $values = explode(',', $values);
        }
        $collection->addFieldToSelect(['category_id', 'name']);

        if ($values) {
            $collection->addCategoryIdFilter($values);
        }

        $options = [];
        foreach ($collection as $category) {
            $options[] = ['label' => $category->getName(), 'value' => $category->getId()];
        }

        return $options;
    }

    /**
     * Get categories collection.
     *
     * @return Collection
     */
    protected function _getCategoriesCollection()
    {
        $article = $this->registry->registry('current_article');

        return $article->getCategories();
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        return '<div class="addon">'.parent::getElementHtml().'</div>';
    }

    /**
     * Attach category suggest widget initialization.
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        if (!$this->isAllowed()) {
            return '';
        }
        $htmlId = $this->getHtmlId();
        $suggestPlaceholder = __('start typing to search category');
        $selectorOptions = $this->jsonEncoder->encode($this->_getSelectorOptions());
        $newCategoryCaption = __('New Category');

        $button = $this->layout->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'kbase_add_category_button',
                'label' => $newCategoryCaption,
                'title' => $newCategoryCaption,
                'onclick' => 'jQuery("#add-new-category").modal("openModal")',
                'disabled' => $this->getDisabled(),
            ]
        );
        $return = <<<HTML
    <input id="{$htmlId}-suggest" placeholder="$suggestPlaceholder" />
    <script>
        require(["jquery", "mage/mage"], function($){
            $('#{$htmlId}-suggest').mage('treeSuggest', {$selectorOptions});
        });
    </script>
HTML;

        return $return . parent::getAfterElementHtml() . $button->toHtml();
    }

    /**
     * Get selector options.
     *
     * @return array
     */
    protected function _getSelectorOptions()
    {
        return [
            'source' => $this->backendData->getUrl('kbase/category/suggestCategories'),
            'valueField' => '#'.$this->getHtmlId(),
            'className' => 'category-select',
            'multiselect' => true,
            'showAll' => true,
        ];
    }

    /**
     * Whether permission is granted.
     *
     * @return bool
     */
    protected function isAllowed()
    {
        return $this->authorization->isAllowed('Mirasvit_Kb::kb_category');
    }
}
