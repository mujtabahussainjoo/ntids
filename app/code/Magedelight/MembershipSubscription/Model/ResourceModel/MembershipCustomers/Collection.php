<?php

/**
* Magedelight
* Copyright (C) 2017 Magedelight <info@magedelight.com>
*
* @category Magedelight
* @package Magedelight_MembershipSubscription
* @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
* @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
* @author Magedelight <info@magedelight.com>
 */

//namespace Magedelight\MembershipSubscription\Model\ResourceModel\MembershipProducts;
namespace Magedelight\MembershipSubscription\Model\ResourceModel\MembershipCustomers;

use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entiti_id';
 
    /**
     *
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param AdapterInterface $connection
     * @param AbstractDb $resource
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        AdapterInterface $connection = null,
        AbstractDb $resource = null,
        StoreManagerInterface $storeManager
    ) {
        $this->_init('Magedelight\MembershipSubscription\Model\MembershipCustomers', 'Magedelight\MembershipSubscription\Model\ResourceModel\MembershipCustomers');
        //Class naming structure
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
    }
    
    protected function _initSelect()
    {
        parent::_initSelect();
 
        $this->getSelect()->joinInner(
            ['customer' => $this->getTable('customer_grid_flat')], //2nd table name by which you want to join mail table
            'main_table.customer_id = customer.entity_id AND main_table.membership_product_id!="" AND main_table.plan_expiry_status=0 GROUP BY main_table.customer_id', // common column which available in both table
            '*' // '*' define that you want all column of 2nd table. if you want some particular column then you can define as ['column1','column2']
        );
        
//        echo $this->getSelect()->__toString();
//        exit;
    }
}
