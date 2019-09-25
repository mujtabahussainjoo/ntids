<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/20/2016
 * Time: 12:37 PM
 */
namespace PL\Competition\Block\Adminhtml\Competition\Edit\Tab;
use Magento\Framework\App\ResourceConnection;
use Magento\Newsletter\Model\Subscriber;
class Competitor extends \Magento\Backend\Block\Widget\Grid\Extended{

    protected $_competitorCollectionFactory;

    protected $compHelper;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \PL\Competition\Helper\Data $compHelper,
        \PL\Competition\Model\Resource\Competitor\CollectionFactory $competitorCollectionFactory,
        array $data = []
    ) {
        $this->_competitorCollectionFactory = $competitorCollectionFactory;
        $this->compHelper = $compHelper;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct(){
        parent::_construct();
        $this->setId('competitorGrid');
        $this->setDefaultSort('competitor_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('entity_id')) {
            $this->setDefaultFilter(array('in_competitor' => 1));
        }
    }

    protected function _prepareCollection(){
        $competition_id = (int)$this->getRequest()->getParam('entity_id',0);
        $collection = $this->_competitorCollectionFactory->create();
        $collection->getSelect()->joinLeft(
            ['ns'=>$collection->getTable('newsletter_subscriber')],
            'ns.subscriber_email = main_table.email',
            ['ns.subscriber_status']
        );
        $collection->addFieldToFilter('main_table.competition_id',$competition_id);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns(){
        /*
        $this->addColumn(
            'competitor_id',
            [
                'header' => __('Competitor ID'),
                'type' => 'number',
                'index' => 'competitor_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        */
        $this->addColumn(
            'firstname',
            [
                'header' => __('First Name'),
                'index' => 'firstname',
                'filter' => false,
                'sortable' => false
            ]
        );
        $this->addColumn(
            'lastname',
            [
                'header' => __('Last Name'),
                'index' => 'lastname',
                'filter' => false,
                'sortable' => false
            ]
        );
        $this->addColumn(
            'email',
            [
                'header' => __('Email'),
                'index' => 'email',
                'filter' => false,
                'sortable' => false
            ]
        );
        if($this->compHelper->isDob()){
            $this->addColumn(
                'dob',
                [
                    'header' => __('Date of Birth'),
                    'index' => 'dob',
                    'filter' => false,
                    'sortable' => false
                ]
            );
        }

        $this->addColumn(
            'comment',
            [
                'header' => __('Comment'),
                'index' => 'comment',
                'filter' => false,
                'sortable' => false
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'subscriber_status',
                'type' => 'options',
                'filter' => false,
                'sortable' => false,
                'options'   => [
                    Subscriber::STATUS_NOT_ACTIVE   => __('Not Activated'),
                    Subscriber::STATUS_SUBSCRIBED   => __('Subscribed'),
                    Subscriber::STATUS_UNSUBSCRIBED => __('Unsubscribed'),
                    Subscriber::STATUS_UNCONFIRMED => __('Unconfirmed'),
                ]
            ]
        );
        $this->addColumn(
            'winner',
            [
                'header' => __('Winner'),
                'index' => 'winner',
                'type' => 'options',
                'filter' => false,
                'sortable' => false,
                'options'  =>[
                    0=>__('No'),
                    1=>__('Yes')
                ]
            ]
        );
    }

}