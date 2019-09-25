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



namespace Mirasvit\Helpdesk\Model;

/**
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\Draft\Collection|\Mirasvit\Helpdesk\Model\Draft[] getCollection()
 * @method \Mirasvit\Helpdesk\Model\Draft load(int $id)
 * @method bool getIsMassDelete()
 * @method \Mirasvit\Helpdesk\Model\Draft setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method \Mirasvit\Helpdesk\Model\Draft setIsMassStatus(bool $flag)
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\Draft getResource()
 * @method int getTicketId()
 * @method \Mirasvit\Helpdesk\Model\Draft setTicketId(int $param)
 * @method \Mirasvit\Helpdesk\Model\Draft setUsersOnline(int $param)
 * @method int getUpdatedBy()
 * @method \Mirasvit\Helpdesk\Model\Draft setUpdatedBy(int $param)
 * @method string getBody()
 * @method \Mirasvit\Helpdesk\Model\Draft setBody(string $param)
 * @method string getUpdatedAt()
 * @method \Mirasvit\Helpdesk\Model\Draft setUpdatedAt(string $param)
 */
class Draft extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'helpdesk_draft';

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $userFactory;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Model\ResourceModel\AbstractResource
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Data\Collection\AbstractDb
     */
    protected $resourceCollection;

    /**
     * @param \Magento\User\Model\UserFactory                         $userFactory
     * @param \Magento\Framework\Model\Context                        $context
     * @param \Magento\Framework\Registry                             $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb           $resourceCollection
     * @param array                                                   $data
     */
    public function __construct(
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->userFactory = $userFactory;
        $this->context = $context;
        $this->registry = $registry;
        $this->resource = $resource;
        $this->resourceCollection = $resourceCollection;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Helpdesk\Model\ResourceModel\Draft');
    }

    /**
     * @param string|false $emptyOption
     * @return array
     */
    public function toOptionArray($emptyOption = false)
    {
        return $this->getCollection()->toOptionArray($emptyOption);
    }

    /**
     *
     */
    public function getUsersOnline()
    {
        $value = $this->getData('users_online');
        if (is_array($value)) {
            return $value;
        }
        $value = unserialize($value);
        $this->setData('users_online', $value);

        return $value;
    }

    /**
     * @var \Magento\User\Model\User
     */
    protected $user = null;

    /**
     * @return bool|null
     */
    public function getUser()
    {
        if (!$this->getUpdatedBy()) {
            return false;
        }
        if ($this->user === null) {
            $this->user = $this->userFactory->create()->load($this->getUpdatedBy());
        }

        return $this->user;
    }

    /************************/
}
