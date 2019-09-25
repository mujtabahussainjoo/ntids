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

class Permission extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
        $this->_init('mst_helpdesk_permission', 'permission_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return \Magento\Framework\Model\AbstractModel|\Mirasvit\Helpdesk\Model\Permission
     */
    public function loadDepartmentIds(\Magento\Framework\Model\AbstractModel $object)
    {
        /* @var  \Mirasvit\Helpdesk\Model\Permission $object */
        $select = $this->getConnection()->select()
            ->from($this->getTable('mst_helpdesk_permission_department'))
            ->where('permission_id = ?', $object->getId());
        $array = [];
        if ($data = $this->getConnection()->fetchAll($select)) {
            foreach ($data as $row) {
                $array[] = (int) $row['department_id'];
            }
        }
        $object->setData('department_ids', $array);

        return $object;
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Permission $object
     *
     * @return void
     */
    protected function saveDepartmentIds($object)
    {
        /* @var  \Mirasvit\Helpdesk\Model\Permission $object */
        $condition = $this->getConnection()->quoteInto('permission_id = ?', $object->getId());
        $this->getConnection()->delete($this->getTable('mst_helpdesk_permission_department'), $condition);
        foreach ((array) $object->getData('department_ids') as $id) {
            $objArray = [
                'permission_id' => $object->getId(),
                'department_id' => $id ? $id : new \Zend_Db_Expr('NULL'),
            ];
            $this->getConnection()->insert(
                $this->getTable('mst_helpdesk_permission_department'),
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
        /** @var  \Mirasvit\Helpdesk\Model\Permission $object */
        if (!$object->getIsMassDelete()) {
            $this->loadDepartmentIds($object);
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
        /** @var  \Mirasvit\Helpdesk\Model\Permission $object */
        if (!$object->getId()) {
            $object->setCreatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
        }
        if (!$object->getRoleId()) {
            $object->setRoleId(new \Zend_Db_Expr('NULL'));
        }
        $object->setUpdatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));

        return parent::_beforeSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Mirasvit\Helpdesk\Model\Permission $object */
        if (!$object->getIsMassStatus()) {
            $this->saveDepartmentIds($object);
        }

        return parent::_afterSave($object);
    }

    /************************/
}
