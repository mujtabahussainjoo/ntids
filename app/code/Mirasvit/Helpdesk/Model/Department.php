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

use Magento\Framework\DataObject\IdentityInterface;

/**
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\Department\Collection|
 * \Mirasvit\Helpdesk\Model\Department[] getCollection()
 * @method \Mirasvit\Helpdesk\Model\Department load(int $id)
 * @method bool getIsMassDelete()
 * @method \Mirasvit\Helpdesk\Model\Department setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method \Mirasvit\Helpdesk\Model\Department setIsMassStatus(bool $flag)
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\Department getResource()
 * @method int[] getUserIds()
 * @method \Mirasvit\Helpdesk\Model\Department setUserIds(array $ids)
 * @method bool getIsMembersNotificationEnabled()
 * @method \Mirasvit\Helpdesk\Model\Department setIsMembersNotificationEnabled(bool $flag)
 * @method \Mirasvit\Helpdesk\Model\Department setSenderEmail(string $email)
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Department extends \Magento\Framework\Model\AbstractModel implements
    IdentityInterface,
    \Magento\Framework\Data\OptionSourceInterface
{
    const CACHE_TAG = 'helpdesk_department';
    /**
     * @var string
     */
    protected $_cacheTag = 'helpdesk_department';

    /**
     * @var string
     */
    protected $_eventPrefix = 'helpdesk_department';

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * @var \Magento\User\Model\ResourceModel\User\CollectionFactory
     */
    protected $userCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Storeview
     */
    protected $helpdeskStoreview;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Config\Model\Config\Source\Email\Identity
     */
    protected $emailIdentity;

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
     * @param \Magento\User\Model\ResourceModel\User\CollectionFactory $userCollectionFactory
     * @param \Mirasvit\Helpdesk\Helper\Storeview                      $helpdeskStoreview
     * @param \Magento\Framework\App\Config\ScopeConfigInterface       $scopeConfig
     * @param \Magento\Config\Model\Config\Source\Email\Identity       $emailIdentity
     * @param \Magento\Framework\Model\Context                         $context
     * @param \Magento\Framework\Registry                              $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource  $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb            $resourceCollection
     * @param array                                                    $data
     */
    public function __construct(
        \Magento\User\Model\ResourceModel\User\CollectionFactory $userCollectionFactory,
        \Mirasvit\Helpdesk\Helper\Storeview $helpdeskStoreview,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Config\Model\Config\Source\Email\Identity $emailIdentity,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->userCollectionFactory = $userCollectionFactory;
        $this->helpdeskStoreview = $helpdeskStoreview;
        $this->scopeConfig = $scopeConfig;
        $this->emailIdentity = $emailIdentity;
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
        $this->_init('Mirasvit\Helpdesk\Model\ResourceModel\Department');
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
     * @return string
     */
    public function getName()
    {
        return $this->helpdeskStoreview->getStoreViewValue($this, 'name');
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setName($value)
    {
        $this->helpdeskStoreview->setStoreViewValue($this, 'name', $value);

        return $this;
    }

    /**
     * Overrides standard getSenderEmail() method to return proper department email,
     * if customer migrating from older versions.
     *
     * @return string
     */
    public function getSenderEmail()
    {
        $senderEmail = parent::getSenderEmail();
        $emails = $this->emailIdentity->toOptionArray();
        foreach ($emails as $email) {
            $emailAddress = $this->scopeConfig->getValue("trans_email/ident_{$email['value']}/email");
            if ($email['value'] == $senderEmail) {
                $senderEmail = $emailAddress;
            }
        }

        return $senderEmail;
    }

    /**
     * @return string
     */
    public function getNotificationEmail()
    {
        return $this->helpdeskStoreview->getStoreViewValue($this, 'notification_email');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setNotificationEmail($value)
    {
        $this->helpdeskStoreview->setStoreViewValue($this, 'notification_email', $value);

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function addData(array $data)
    {
        if (isset($data['name']) && strpos($data['name'], 'a:') !== 0) {
            $this->setName($data['name']);
            unset($data['name']);
        }

        if (isset($data['notification_email']) && strpos($data['notification_email'], 'a:') !== 0) {
            $this->setNotificationEmail($data['notification_email']);
            unset($data['notification_email']);
        }

        return parent::addData($data);
    }
    /************************/

    /**
     * @return \Magento\Eav\Model\Entity\Collection\Abstract|\Magento\User\Model\User[]
     */
    public function getUsers()
    {
        if (!$this->getUserIds()) {
            $this->getResource()->loadUserIds($this);
        }

        return $this->userCollectionFactory->create()
            ->addFieldToFilter('main_table.user_id', $this->getUserIds())
            ->addFieldToFilter('main_table.is_active', true);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Prepare collection for dropdowns.
     *
     * @param int|\Magento\Store\Model\Store $store
     *
     * @return \Mirasvit\Helpdesk\Model\ResourceModel\Department\Collection|\Mirasvit\Helpdesk\Model\Department[]
     */
    public function getPreparedCollection($store)
    {
        if (is_object($store)) {
            $store = $store->getStoreId();
        }

        return $this->getCollection()
            ->addStoreFilter($store)
            ->addFieldToFilter('is_active', true)
            ->setOrder('sort_order', \Mirasvit\Helpdesk\Model\Config::DEFAULT_SORT_ORDER);
    }
}
