<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/21/2016
 * Time: 1:16 AM
 */
namespace PL\Competition\Block;
class Competition extends \Magento\Framework\View\Element\Template{

    protected $_competitionFactory;

    protected $_competitionHelper;

    protected $storeManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \PL\Competition\Model\CompetitionFactory $competitionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \PL\Competition\Helper\Data $competitionHelper,
        array $data = []
    ) {
        $this->_competitionFactory = $competitionFactory;
        $this->_competitionHelper = $competitionHelper;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    protected  function _construct()
    {
        $storeId = $this->_storeManager->getStore()->getId(); //exit;
        parent::_construct();
        $storeIds = array(0,$storeId);
        $collection = $this->_competitionFactory->create()->getCollection()

            ->setOrder('date_to', 'DESC');
        if($storeId){
            $collection->getSelect()->joinLeft(array('storetable' => 'pl_competition_store'),
                'main_table.entity_id = storetable.competition_id');
            $collection->getSelect()->where('storetable.store_id IN (?)',$storeIds);
            $collection->getSelect()->__toString();
        }
        $this->setCollection($collection);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        /** @var \Magento\Theme\Block\Html\Pager */
        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'competition.list.pager'
        );
        $pager->setLimit(5)
            ->setShowAmounts(false)
            ->setCollection($this->getCollection());

        $this->setChild('pager', $pager);
        $this->getCollection()->load();

        return $this;
    }

    public function getImageUrl(\PL\Competition\Model\Competition $competition, $name='')
    {
        if($name!=""){
            return $this->_competitionHelper->getBaseUrlMedia($competition->getData($name));
        }

    }
}