<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/20/2016
 * Time: 11:24 AM
 */
namespace PL\Competition\Block\Adminhtml\Competition;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended{

    protected $_competitionCollectionFactory;

    protected $_helper;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \PL\Competition\Model\Resource\Competition\CollectionFactory $competitionCollectionFactory,
        \PL\Competition\Helper\Data $helper,
        array $data = []
    ) {

        parent::__construct($context, $backendHelper, $data);
        $this->_competitionCollectionFactory = $competitionCollectionFactory;
        $this->_helper = $helper;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('competitionGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = $this->_competitionCollectionFactory->create();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('#ID'),
                //'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'filter' => false,
                'sortable' => false
            ]
        );


        $this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'index' => 'title',
                'filter' => false,
                'sortable' => false
            ]
        );
        $this->addColumn(
            'number_of_winners',
            [
                'header' => __('Number of Winners'),
                'index' => 'number_of_winners',
                'filter' => false,
                'sortable' => false
            ]
        );
        $this->addColumn(
            'date_from',
            [
                'type'  =>'date',
                'timezone' => true,
                'header' => __('From Date'),
                'index' => 'date_from',
                'filter' => false,
                'sortable' => false
            ]
        );
        $this->addColumn(
            'date_to',
            [
                'header' => __('To Date'),
                'type'  =>'date',
                'timezone' => true,
                'index' => 'date_to',
                'filter' => false,
                'sortable' => false
            ]
        );

        $this->addColumn(
            'winner',
            [
                'header' => __('Winner'),
                //'index' => 'number_of_winners',
                'renderer' => 'PL\Competition\Block\Adminhtml\Competition\Grid\Renderer\Winner',
                'filter' => false,
                'sortable' => false
            ]
        );
        $this->addColumn(
            'competitor',
            [
                'header' => __('Competitor'),
                //'index' => 'number_of_winners',
                'renderer' => 'PL\Competition\Block\Adminhtml\Competition\Grid\Renderer\TotalCompetitors',
                'filter' => false,
                'sortable' => false
            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'renderer' => 'PL\Competition\Block\Adminhtml\Competition\Grid\Renderer\Status',
                'filter' => false,
                'sortable' => false
            ]
        );




        $this->addColumn(
            'edit',
            [
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit',
                        ],
                        'field' => 'entity_id',
                    ],
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action',
            ]
        );


        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     * get row url
     * @param  object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit',
            array('entity_id' => $row->getId())
        );
    }

}