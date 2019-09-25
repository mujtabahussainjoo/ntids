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

class Field extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Catalog\Model\Product\Url
     */
    protected $productUrl;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\Context
     */
    protected $context;

    /**
     * @var object
     */
    protected $resourcePrefix;

    /**
     * @param \Magento\Catalog\Model\Product\Url                $productUrl
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string                                            $resourcePrefix
     * @param array                                             $data
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        \Magento\Catalog\Model\Product\Url $productUrl,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $resourcePrefix = null,
        array $data = []
    ) {
        $this->productUrl = $productUrl;
        $this->context = $context;
        $this->resourcePrefix = $resourcePrefix;
        parent::__construct($context, $resourcePrefix);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('mst_helpdesk_field', 'field_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return \Magento\Framework\Model\AbstractModel|\Mirasvit\Helpdesk\Model\Field
     */
    protected function loadStoreIds(\Magento\Framework\Model\AbstractModel $object)
    {
        /* @var  \Mirasvit\Helpdesk\Model\Field $object */
        $select = $this->getConnection()->select()
            ->from($this->getTable('mst_helpdesk_field_store'))
            ->where('fs_field_id = ?', $object->getId());
        if ($data = $this->getConnection()->fetchAll($select)) {
            $array = [];
            foreach ($data as $row) {
                $array[] = $row['fs_store_id'];
            }
            $object->setData('store_ids', $array);
        }

        return $object;
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Field $object
     *
     * @return void
     */
    protected function saveStoreIds($object)
    {
        /* @var  \Mirasvit\Helpdesk\Model\Field $object */
        $condition = $this->getConnection()->quoteInto('fs_field_id = ?', $object->getId());
        $this->getConnection()->delete($this->getTable('mst_helpdesk_field_store'), $condition);
        foreach ((array) $object->getData('store_ids') as $id) {
            $objArray = [
                'fs_field_id' => $object->getId(),
                'fs_store_id' => $id,
            ];
            $this->getConnection()->insert(
                $this->getTable('mst_helpdesk_field_store'),
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
        /** @var  \Mirasvit\Helpdesk\Model\Field $object */
        if (!$object->getIsMassDelete()) {
            $this->loadStoreIds($object);
        }

        return parent::_afterLoad($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return $this
     *
     * @throws \Exception
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Mirasvit\Helpdesk\Model\Field $object */
        if (!$object->getId()) {
            $object->setCreatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
            $object->setCode($this->normalize($object->getCode()));
            if (in_array($object->getCode(), [
                'name', 'code', 'external_id',
                'user_id', 'description', 'customer_email', 'customer_name', 'order_id', 'last_reply_at',
            ])) {
                throw new \Exception("Code {$object->getCode()} is not allowed. Please, try different code");
            }
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
        /** @var  \Mirasvit\Helpdesk\Model\Field $object */
        if (!$object->getIsMassDelete() && !$object->getIsMassChange()) {
            $this->saveStoreIds($object);
        }

        return parent::_afterSave($object);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function normalize($string)
    {
        $string = $this->productUrl->formatUrlKey($string);
        $string = str_replace('-', '_', $string);

        return 'f_'.$string;
    }
}
