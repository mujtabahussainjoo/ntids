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



namespace Mirasvit\Kb\Block\Adminhtml\Article;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Mirasvit\Kb\Model\ArticleFactory
     */
    protected $articleFactory;

    /**
     * @var \Mirasvit\Kb\Helper\Data
     */
    protected $kbData;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    /**
     * @param \Mirasvit\Kb\Model\ArticleFactory     $articleFactory
     * @param \Mirasvit\Kb\Helper\Data              $kbData
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Backend\Helper\Data          $backendHelper
     * @param array                                 $data
     */
    public function __construct(
        \Mirasvit\Kb\Model\ArticleFactory $articleFactory,
        \Mirasvit\Kb\Helper\Data $kbData,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        $this->articleFactory = $articleFactory;
        $this->kbData = $kbData;
        $this->context = $context;
        $this->backendHelper = $backendHelper;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setData('id', 'grid')
            ->setDefaultSort('article_id')
            ->setDefaultDir('DESC')
            ->setSaveParametersInSession(true);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $collection = $this->articleFactory->create()
            ->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn('article_id', [
            'header'       => __('ID'),
            'index'        => 'article_id',
            'filter_index' => 'main_table.article_id',
        ]);
        $this->addColumn('name', [
            'header'       => __('Title'),
            'index'        => 'name',
            'filter_index' => 'main_table.name',
        ]);
        $this->addColumn('is_active', [
            'header'       => __('Active'),
            'index'        => 'is_active',
            'filter_index' => 'main_table.is_active',
            'type'         => 'options',
            'options'      => [
                0 => __('No'),
                1 => __('Yes'),
            ],
        ]);
        $this->addColumn('user_id', [
            'header'       => __('Author'),
            'index'        => 'user_id',
            'filter_index' => 'main_table.user_id',
            'type'         => 'options',
            'options'      => $this->kbData->getAdminUserOptionArray(),
        ]);
        $this->addColumn('created_at', [
            'header'       => __('Created At'),
            'index'        => 'created_at',
            'filter_index' => 'main_table.created_at',
            'type'         => 'date',
        ]);
        $this->addColumn('updated_at', [
            'header'       => __('Updated At'),
            'index'        => 'updated_at',
            'filter_index' => 'main_table.updated_at',
            'type'         => 'date',
        ]);

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('article_id');
        $this->getMassactionBlock()->setFormFieldName('article_id');
        $this->getMassactionBlock()->addItem('delete', [
            'label'   => __('Delete'),
            'url'     => $this->getUrl('*/*/massDelete'),
            'confirm' => __('Are you sure?'),
        ]);

        return $this;
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }

    /**
     * Add page loader. Generate list of grid buttons.
     *
     * @return string
     */
    public function getMainButtonsHtml()
    {
        $html = '
            <div data-role="spinner" class="admin__data-grid-loading-mask" data-bind="visible: window.loading">
                <div class="spinner">
                    <span></span><span></span><span></span><span></span>
                    <span></span><span></span><span></span><span></span>
                </div>
            </div>

            <script>
                require(["jquery"],function($) {
                    $(function(){
                        setTimeout(hideSpinner, 500);
                    });
                });
                function hideSpinner() {
                    jQuery(\'[data-role="spinner"]\').hide();
                }
            </script>
        ';
        return $html . parent::getMainButtonsHtml();
    }

    /************************/
}
