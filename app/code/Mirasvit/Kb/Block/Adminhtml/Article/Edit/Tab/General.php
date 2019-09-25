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


namespace Mirasvit\Kb\Block\Adminhtml\Article\Edit\Tab;

use Mirasvit\Kb\Model\Article;

class General extends \Magento\Backend\Block\Widget\Form
{
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Mirasvit\Kb\Helper\Form\Article\Category $formCategoryHelper,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Mirasvit\Kb\Helper\Data $kbData,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->objectManager          = $objectManager;
        $this->formCategoryHelper     = $formCategoryHelper;
        $this->backendUrl             = $backendUrl;
        $this->kbData                 = $kbData;
        $this->formFactory            = $formFactory;
        $this->registry               = $registry;
        $this->context                = $context;
        $this->wysiwygConfig          = $wysiwygConfig;

        $this->articleManagement          = $this->getArticleMagagement();
        $this->articleFormHelper          = $this->getArticleFormHelper();
        $this->articleStoreviewFormHelper = $this->getArticleStoreviewFormHelper();

        parent::__construct($context, $data);
    }

    /**
     * @return $this
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create();
        $this->setForm($form);
        /** @var \Mirasvit\Kb\Model\Article $article */
        $article = $this->registry->registry('current_article');

        $fieldset = $form->addFieldset('edit_fieldset', [
            'class'  => 'fieldset-wide field-article-form',
            'legend' => __('General Information'),
        ]);
        if ($article->getId()) {
            $fieldset->addField('article_id', 'hidden', [
                'name'  => 'article_id',
                'value' => $article->getId(),
            ]);
        }
        $fieldset->addField('name', 'text', [
            'label'    => __('Title'),
            'required' => true,
            'name'     => 'name',
            'value'    => $article->getName(false),
            'after_element_js' => '
                <script>
                    require(["Magento_Ui/js/form/element/wysiwyg"], function () {});
                </script>
            ',
        ]);

        $fieldset->addField('text', 'editor', [
            'label'    => __('Text'),
            'required' => false,
            'name'     => 'text',
            'value'    => $article->getText(),
            'wysiwyg'  => true,
            'config'   => $this->wysiwygConfig->getConfig(),
            'style'    => 'height:35em',
        ]);
        $fieldset->addField('url_key', 'text', [
            'label' => __('URL Key'),
            'name'  => 'url_key',
            'value' => $article->getUrlKey(),

        ]);
        $fieldset->addField('is_active', 'select', [
            'label'  => __('Is Active'),
            'name'   => 'is_active',
            'value'  => $article->getId() ? $article->getIsActive() : true,
            'values' => [0 => __('No'), 1 => __('Yes')],

        ]);
        $fieldset->addField('position', 'text', [
            'label' => __('Sort Order'),
            'name'  => 'position',
            'value' => $article->getPosition(),

        ]);
        $this->addStoreField($fieldset, $article);

        $groups = $this->getGroupCollectionFactory()->create()->toOptionArray();
        array_unshift($groups, ['value' => Article::ALL_GROUPS_KEY, 'label' => __('All Groups')->getText()]);
        $fieldset->addField('customer_group_ids', 'multiselect', [
            'label'    => __('Customer Groups'),
            'required' => true,
            'name'     => 'customer_group_ids[]',
            'value'    => $article->getCustomerGroupIds(),
            'values'   => $groups,
        ]);

        $fieldset->addField('user_id', 'select', [
            'label'  => __('Author'),
            'name'   => 'user_id',
            'value'  => $article->getUserId(),
            'values' => $this->kbData->toAdminUserOptionArray(),

        ]);
        $tags = [];
        foreach ($article->getTags() as $tag) {
            $tags[] = $tag->getName();
        }
        $fieldset->addField('tags', 'text', [
            'label' => __('Tags'),
            'name'  => 'tags',
            'value' => implode(', ', $tags),
        ]);

        $container = 'kb_article_categories';
        $updateUrl = $this->getUrl('mui/index/render');
        $renderUrl = $this->getUrl('mui/index/render_handle', [
                'handle'  => 'kb_category_create',
                'buttons' => 1
            ]
        );

        $fieldset->addField('categories', 'hidden', [
            'name'             => 'categories',
            'value'            => implode(',', (array)$article->getData('category_ids')),
            'after_element_js' => $this->formCategoryHelper->getCategoryField(
                $article, $container, $updateUrl, $renderUrl
            ),
        ]);

        return $this;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\Fieldset $fieldset
     * @param \Mirasvit\Kb\Model\Article                    $article
     *
     * @return void
     */
    protected function addStoreField($fieldset, $article)
    {
        if ($this->context->getStoreManager()->isSingleStoreMode()) {
            $fieldset->addField('store_ids', 'hidden', [
                'name'  => 'store_ids[]',
                'value' => $this->context->getStoreManager()->getStore(true)->getId(),
            ]);
        } else {
            $container = 'kb_article_store_views';
            $fieldset->addField('store_ids', 'hidden', [
                'name'             => 'store_ids',
                'value'            => implode(',', $article->getStoreIds()),
                'after_element_js' => $this->articleStoreviewFormHelper->getField(
                    $article, $container
                ),
            ]);
        }
    }

    /**
     * @return \Mirasvit\Kb\Api\Service\Article\ArticleManagementInterface
     */
    private function getArticleMagagement()
    {
        return $this->objectManager->get('\Mirasvit\Kb\Api\Service\Article\ArticleManagementInterface');
    }

    /**
     * @return \Mirasvit\Kb\Helper\Form\Article
     */
    private function getArticleFormHelper()
    {
        return $this->objectManager->get('\Mirasvit\Kb\Helper\Form\Article');
    }

    /**
     * @return \Mirasvit\Kb\Helper\Form\Article\Storeview
     */
    private function getArticleStoreviewFormHelper()
    {
        return $this->objectManager->get('\Mirasvit\Kb\Helper\Form\Article\Storeview');
    }

    /**
     * @return \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    private function getGroupCollectionFactory()
    {
        return $this->objectManager->get('\Magento\Customer\Model\ResourceModel\Group\CollectionFactory');
    }
}
