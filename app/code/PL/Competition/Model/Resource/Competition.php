<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/20/2016
 * Time: 11:14 AM
 */
namespace PL\Competition\Model\Resource;


class Competition extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $_storeManager;

    protected $_store = null;

    protected $_dateTime;


    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
        $this->_dateTime = $dateTime;
    }

    protected function _construct()
    {
        $this->_init('pl_competition', 'entity_id');
    }

    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['entity_id = ?' => (int)$object->getId()];

        $this->getConnection()->delete($this->getTable('pl_competition'), $condition);

        return parent::_beforeDelete($object);
    }

    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        // check unique url key
        if(trim($object->getUrlKey())!=""){
            if (!$this->isValidUrlKey($object)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The page URL key contains capital letters or disallowed symbols.')
                );
            }
            $url_key = $this->prepareText($object->getUrlKey());

        }else{
            $url_key = $this->prepareText($object->getTitle());
        }
        $object->setUrlKey(strtolower($url_key));

        return parent::_beforeSave($object);
    }

    public function getIsUniqueUrlKey(\Magento\Framework\Model\AbstractModel $object){


    }

    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $select = $this->getConnection()->select()->from(
            ['cp' => $this->getMainTable()]
        )->join(
            ['cps' => $this->getTable('pl_competition_store')],
            'cp.entity_id = cps.competition_id',
            []
        )->where(
            'cp.url_key = ?',
            $identifier
        )->where(
            'cps.store_id IN (?)',
            $store
        );

        if (!is_null($isActive)) {
            $select->where('cp.status = ?', $isActive);
        }

        return $select;
    }

    public function checkIdentifier($identifier, $storeId)
    {
        $stores = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, $storeId];
        $select = $this->_getLoadByIdentifierSelect($identifier, $stores, 1);
        $select->reset(\Magento\Framework\DB\Select::COLUMNS)->columns('cp.entity_id')->order('cps.store_id DESC')->limit(1);

        return $this->getConnection()->fetchOne($select);
    }

    public function prepareText($string){

        // remove all characters that arenâ€™t a-z, 0-9, dash, underscore or space
        $NOT_acceptable_characters_regex = '#[^-a-zA-Z0-9_]#';
        $string = preg_replace($NOT_acceptable_characters_regex, '-', $string);
        // remove all leading and trailing spaces
        $string = trim($string);
        // change all dashes, underscores and spaces to dashes
        $string = preg_replace('#[-_ ]+#', '-', $string);
        // return the modified string
        return $string;

    }


    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table = $this->getTable('pl_competition_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = ['competition_id = ?' => (int)$object->getId(), 'store_id IN (?)' => $delete];

            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];

            foreach ($insert as $storeId) {
                $data[] = ['competition_id' => (int)$object->getId(), 'store_id' => (int)$storeId];
            }

            $this->getConnection()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);
    }



    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        if (!is_numeric($value) && is_null($field)) {
            $field = 'url_key';
        }

        return parent::load($object, $value, $field);
    }

    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());

            $object->setData('store_id', $stores);
        }

        return parent::_afterLoad($object);
    }

    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, (int)$object->getStoreId()];
            $select->join(
                ['pl_competition_store' => $this->getTable('pl_competition_store')],
                $this->getMainTable() . '.entity_id = pl_competition_store.competition_id',
                []
            )->where(
                'is_active = ?',
                1
            )->where(
                'pl_competition_store.store_id IN (?)',
                $storeIds
            )->order(
                'pl_competition_store.store_id DESC'
            )->limit(
                1
            );
        }

        return $select;
    }


    public function lookupStoreIds($competitionId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('pl_competition_store'),
            'store_id'
        )->where(
            'competition_id = ?',
            (int)$competitionId
        );

        return $connection->fetchCol($select);
    }

    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    public function getStore()
    {
        return $this->_storeManager->getStore($this->_store);
    }

    protected function isValidUrlKey(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('url_key'));
    }

    /*public function getTotalWinners($competion_id,$store){
        $select = $this->getConnection()->select()->from(
            ['cp' => $this->getMainTable()]
        )->join(
            ['cps' => $this->getTable('pl_competition_store')],
            'cp.entity_id = cps.competition_id',
            []
        )
            ->join(
                ['ccp'=>$this->getTable('pl_competition_competitor')],
                'cp.entity_id = ccp.competition_id',
                []
            )
            ->where('cp.entity_id = ?',$competion_id)
            ->where('cps.store_id IN (?)', $store )
            ->where('ccp.winner = ?',1);


        return $select;

    }*/
}
