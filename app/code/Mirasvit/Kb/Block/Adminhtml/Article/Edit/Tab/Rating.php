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

class Rating extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $formFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @param \Magento\Framework\Data\FormFactory   $formFactory
     * @param \Magento\Framework\Registry           $registry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->formFactory = $formFactory;
        $this->registry = $registry;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create();
        $this->setForm($form);
        $article = $this->registry->registry('current_article');

        $fieldset = $form->addFieldset('rating_fieldset', ['legend' => __('Rating')]);
        if ($article->getId()) {
            $fieldset->addField('article_id', 'hidden', [
                'name' => 'article_id',
                'value' => $article->getId(),
            ]);
        }
        $fieldset->addField('votes_num', 'text', [
            'label' => __('Number of Votes'),
            'name' => 'votes_num',
            'value' => $article->getVotesNum(),

        ]);
        $fieldset->addField('rating', 'text', [
            'label' => __('Rating'),
            'name' => 'rating',
            'value' => round($article->getRating(), 1),

        ]);

        return parent::_prepareForm();
    }
}
