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
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Helpdesk\Model\ResourceModel;

class Schedule extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\Context
     */
    protected $context;

    /**
     * @var string
     */
    protected $resourcePrefix;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\App\PageCache\Cache            $pageCache
     * @param string                                            $resourcePrefix
     * @param array                                             $data
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\App\PageCache\Cache $pageCache,
        $resourcePrefix = null,
        array $data = []
    ) {
        $this->context = $context;
        $this->pageCache = $pageCache;
        $this->resourcePrefix = $resourcePrefix;
        parent::__construct($context, $resourcePrefix);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('mst_helpdesk_schedule', 'schedule_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return \Magento\Framework\Model\AbstractModel|\Mirasvit\Helpdesk\Model\Schedule
     */
    protected function loadStoreIds(\Magento\Framework\Model\AbstractModel $object)
    {
        /* @var  \Mirasvit\Helpdesk\Model\Schedule $object */
        $select = $this->getConnection()->select()
            ->from($this->getTable('mst_helpdesk_schedule_store'))
            ->where('whs_schedule_id = ?', $object->getId());
        if ($data = $this->getConnection()->fetchAll($select)) {
            $array = [];
            foreach ($data as $row) {
                $array[] = $row['whs_store_id'];
            }
            $object->setData('store_ids', $array);
        }

        return $object;
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Schedule $object
     * @return void
     */
    protected function saveStoreIds($object)
    {
        /* @var  \Mirasvit\Helpdesk\Model\Schedule $object */
        $condition = $this->getConnection()->quoteInto('whs_schedule_id = ?', $object->getId());
        $this->getConnection()->delete($this->getTable('mst_helpdesk_schedule_store'), $condition);
        foreach ((array) $object->getData('store_ids') as $id) {
            $objArray = [
                'whs_schedule_id' => $object->getId(),
                'whs_store_id' => $id,
            ];
            $this->getConnection()->insert(
                $this->getTable('mst_helpdesk_schedule_store'),
                $objArray
            );
        }
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Mirasvit\Helpdesk\Model\Schedule $object */
        if (!$object->getIsMassDelete()) {
            $this->loadStoreIds($object);
        }

        return parent::_afterLoad($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Mirasvit\Helpdesk\Model\Schedule $object */
        if (!$object->getId()) {
            $object->setCreatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
        }
        $object->setUpdatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));

        $activeFrom = $object->getData('active_from');
        if ($activeFrom && $object->dataHasChangedFor('active_from')) {
            $date = (new \DateTime($activeFrom, new \DateTimeZone($object->getTimezone())))
                ->setTimezone(new \DateTimeZone('UTC'))
                ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
            $object->setActiveFrom($date);
        }
        $activeTo = $object->getData('active_to');
        if ($activeTo && $object->dataHasChangedFor('active_to')) {
            $date = (new \DateTime($activeTo, new \DateTimeZone($object->getTimezone())))
                ->setTimezone(new \DateTimeZone('UTC'))
                ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
            $object->setActiveTo($date);
        }

        return parent::_beforeSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Mirasvit\Helpdesk\Model\Schedule $object */
        if (!$object->getIsMassStatus()) {
            $this->saveStoreIds($object);
        }

        $this->pageCache->getFrontend()->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, ['helpdesk_schedule_block']);

        return parent::_afterSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return $this
     */
    protected function _afterDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Mirasvit\Helpdesk\Model\Schedule $object */
        if (!$object->getIsMassStatus()) {
            $object->unsetData('store_ids');
            $this->saveStoreIds($object);
        }

        return parent::_afterSave($object);
    }

    /************************/
}
