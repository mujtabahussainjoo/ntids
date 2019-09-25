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

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Ticket extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\Context
     */
    protected $context;

    /**
     * @var object
     */
    protected $resourcePrefix;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string                                            $resourcePrefix
     * @param array                                             $data
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $resourcePrefix = null,
        array $data = []
    ) {
        $this->context = $context;
        $this->resourcePrefix = $resourcePrefix;
        parent::__construct($context, $resourcePrefix);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('mst_helpdesk_ticket', 'ticket_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return \Magento\Framework\Model\AbstractModel|\Mirasvit\Helpdesk\Model\Ticket
     */
    public function loadTagIds(\Magento\Framework\Model\AbstractModel $object)
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable('mst_helpdesk_ticket_tag'))
            ->where('tt_ticket_id = ?', $object->getId());
        $array = [];
        if ($data = $this->getConnection()->fetchAll($select)) {
            foreach ($data as $row) {
                $array[] = $row['tt_tag_id'];
            }
        }
        $object->setData('tag_ids', $array);

        return $object;
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Ticket $object
     *
     * @return void
     */
    protected function saveTagIds($object)
    {
        /* @var  \Mirasvit\Helpdesk\Model\Ticket $object */
        $condition = $this->getConnection()->quoteInto('tt_ticket_id = ?', $object->getId());
        $this->getConnection()->delete($this->getTable('mst_helpdesk_ticket_tag'), $condition);
        foreach ((array) $object->getData('tag_ids') as $id) {
            $objArray = [
                'tt_ticket_id' => $object->getId(),
                'tt_tag_id' => $id,
            ];
            $this->getConnection()->insert(
                $this->getTable('mst_helpdesk_ticket_tag'),
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
        /** @var  \Mirasvit\Helpdesk\Model\Ticket $object */
        if (!$object->getIsMassDelete()) {
            $this->loadTagIds($object);
        }
        if (is_string($object->getChannelData())) {
            $object->setChannelData(@unserialize($object->getChannelData()));
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
        $object->isNew = 0;
        /** @var  \Mirasvit\Helpdesk\Model\Ticket $object */
        if (!$object->getId() && !$object->getInTest()) {
            $time = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
            $object->setCreatedAt($time);
            $object->setLastReplyAt($time);
            $object->isNew = 1;
        }
        if (is_array($object->getChannelData())) {
            $object->setChannelData(serialize($object->getChannelData()));
        }
        if (!$object->getInTest()) {
            $object->setUpdatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
        }

        $tags = [];
        foreach ($object->getTags() as $tag) {
            $tags[] = $tag->getName();
        }
        $object->addToSearchIndex(implode(' ', $tags));

        return parent::_beforeSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Mirasvit\Helpdesk\Model\Ticket $object */
        if (!$object->getIsMassStatus()) {
            $this->saveTagIds($object);
        }

        return parent::_afterSave($object);
    }

    /************************/
}
