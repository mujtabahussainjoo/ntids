<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Block\Adminhtml\Banner\Edit\Tab;

use Magento\Framework\App\ObjectManager;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Aheadworks\Rbslider\Api\BannerRepositoryInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Backend\Block\Widget\Form\Renderer\FieldsetFactory;
use Magento\Rule\Block\Conditions as BlockConditions;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Aheadworks\Rbslider\Api\Data\BannerInterface;
use Aheadworks\Rbslider\Model\Rule\Product;
use Aheadworks\Rbslider\Model\Rule\ProductFactory;
use Magento\Rule\Model\Condition\AbstractCondition as RuleAbstractCondition;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\DataObject\Factory as DataObjectFactory;

/**
 * Class Conditions
 * @package Aheadworks\Rbslider\Block\Adminhtml\Banner\Edit\Tab
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Conditions extends Generic implements TabInterface
{
    /**
     * @var string
     */
    const FORM_NAME = 'aw_rbslider_banner_form';

    /**
     * @var BannerRepositoryInterface
     */
    private $bannerRepository;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var FieldsetFactory
     */
    private $rendererFieldsetFactory;

    /**
     * @var BlockConditions
     */
    private $conditions;

    /**
     * @var ProductFactory
     */
    private $productRuleFactory;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var string
     */
    protected $nameInLayout = 'conditions_apply_to';

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param BannerRepositoryInterface $bannerRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param BlockConditions $conditions
     * @param FieldsetFactory $rendererFieldsetFactory
     * @param ProductFactory $productRuleFactory
     * @param DataPersistorInterface $dataPersistor
     * @param DataObjectFactory $dataObjectFactory
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        BannerRepositoryInterface $bannerRepository,
        DataObjectProcessor $dataObjectProcessor,
        BlockConditions $conditions,
        FieldsetFactory $rendererFieldsetFactory,
        ProductFactory $productRuleFactory,
        DataPersistorInterface $dataPersistor,
        DataObjectFactory $dataObjectFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->bannerRepository = $bannerRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->conditions = $conditions;
        $this->rendererFieldsetFactory = $rendererFieldsetFactory;
        $this->productRuleFactory = $productRuleFactory;
        $this->dataPersistor = $dataPersistor;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Get data for banner
     *
     * @return array|null
     */
    protected function getFormData()
    {
        $formData = [];
        if (!empty($this->dataPersistor->get('aw_rbslider_banner'))) {
            $formData = $this->dataObjectFactory->create(
                $this->dataPersistor->get('aw_rbslider_banner')
            );
        } elseif ($id = $this->getRequest()->getParam('id')) {
            $formData = $this->bannerRepository->get($id);
        }
        if ($formData) {
            $formData = $this->dataObjectProcessor->buildOutputDataArray(
                $formData,
                BannerInterface::class
            );
        }
        return $formData;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $formData = $this->getFormData();
        $productRule = $this->productRuleFactory->create();
        if (isset($formData['product_condition'])) {
            $productRule->setConditions([])
                ->getConditions()
                ->loadArray($formData['product_condition']);
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset(
            'conditions_fieldset',
            [
                'comment' => __(
                    'Please specify products where the block should be displayed. '
                    . 'Leave blank to display the block on all product pages.'
                )
            ]
        )->setRenderer(
            $this->rendererFieldsetFactory->create()
                ->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
                ->setNewChildUrl(
                    $this->getUrl(
                        '*/*/newConditionHtml',
                        [
                            'form'   => $form->getHtmlIdPrefix() . 'conditions_fieldset',
                            'prefix' => 'rbslider',
                            'rule'   => base64_encode(Product::class),
                            'form_namespace' => self::FORM_NAME
                        ]
                    )
                )
        );
        $productRule->setJsFormObject($form->getHtmlIdPrefix() . 'conditions_fieldset');
        $fieldset
            ->addField(
                'conditions',
                'text',
                [
                    'name'           => 'conditions',
                    'label'          => __('Conditions'),
                    'title'          => __('Conditions'),
                    'data-form-part' => self::FORM_NAME
                ]
            )
            ->setRule($productRule)
            ->setRenderer($this->conditions);

        $this->setConditionFormName($productRule->getConditions(), self::FORM_NAME);
        $form = $this->addCategoryFieldset($form, $formData);

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Add category fieldset
     *
     * @param \Magento\Framework\Data\Form $form
     * @param array $formData
     * @return \Magento\Framework\Data\Form
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addCategoryFieldset($form, $formData)
    {
        $categoryTreeBlock = $this->getLayout()->createBlock(
            \Magento\Catalog\Block\Adminhtml\Category\Checkboxes\Tree::class,
            null,
            ['data' => ['js_form_object' => 'сategoryIds']]
        );

        $catalogFieldset = $form->addFieldset('category_fieldset', []);
        $catalogFieldset->addField(
            'category_ids',
            'hidden',
            [
                'name' => 'category_ids',
                'data-form-part' => self::FORM_NAME,
                'after_element_js' => $this->getCategoryIdsJs(),
                'value' => isset($formData['category_ids']) ? $formData['category_ids'] : ''
            ]
        );

        if (isset($formData['category_ids'])) {
            $categoryTreeBlock->setCategoryIds(explode(',', $formData['category_ids']));
        }

        $catalogFieldset->addField(
            'category_tree_container',
            'note',
            [
                'label' => __('Category'),
                'title' => __('Category'),
                'text' => $categoryTreeBlock->toHtml()
            ]
        );

        return $form;
    }

    /**
     * Handles addition of form name to condition and its conditions
     *
     * @param RuleAbstractCondition $conditions
     * @param string $formName
     * @return void
     */
    private function setConditionFormName(RuleAbstractCondition $conditions, $formName)
    {
        $conditions->setFormName($formName);
        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName);
            }
        }
    }

    /**
     * Retrive js code for CategoryIds input field
     *
     * @return string
     */
    private function getCategoryIdsJs()
    {
        return <<<HTML
    <script type="text/javascript">
        сategoryIds = {updateElement : {value : "", linkedValue : ""}};
        Object.defineProperty(сategoryIds.updateElement, "value", {
            get: function() {
                return сategoryIds.updateElement.linkedValue
            },
            set: function(v) {
                сategoryIds.updateElement.linkedValue = v;
                jQuery("#rule_category_ids").val(v)
            }
        });
    </script>
HTML;
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Conditions');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Conditions');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
