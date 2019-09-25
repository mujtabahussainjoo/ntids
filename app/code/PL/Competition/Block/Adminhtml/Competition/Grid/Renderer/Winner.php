<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/31/2016
 * Time: 2:58 PM
 */
namespace PL\Competition\Block\Adminhtml\Competition\Grid\Renderer;
class Winner extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $competitionFactory;

    protected $storeManager;
    
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \PL\Competition\Model\CompetitorFactory $competitorFactory
    )
    {
        parent::__construct($context);
        $this->_competitorFactory = $competitorFactory;
        $this->storeManager = $storeManager;
    }

    public function render(\Magento\Framework\DataObject $row){
       // $this->storeManager->getStore()->getId();
        $collection =  $this->_competitorFactory->create()->getCollection();
        $collection->addFieldToFilter('competition_id',$row->getEntityId());
        $collection->addFieldToFilter('winner',1);
        return $collection->count().'/'.$row->getNumberOfWinners();

    }

}