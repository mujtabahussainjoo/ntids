<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/31/2016
 * Time: 4:21 PM
 */
namespace PL\Competition\Block\Adminhtml\Competition\Grid\Renderer;
class TotalCompetitors extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $competitorFactory;

    protected $storeManager;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \PL\Competition\Model\CompetitorFactory $competitorFactory
    )
    {
        parent::__construct($context);
        $this->competitorFactory = $competitorFactory;
        $this->storeManager = $storeManager;
    }

    public function render(\Magento\Framework\DataObject $row){
        $collection = $this->competitorFactory->create()->getCollection();
        //$this->storeManager->getStore()->getId()
        $collection->addFieldToFilter('competition_id',$row->getEntityId());
        if($collection->count()>0){
            return $collection->count();
        }
        return '0';
    }

}