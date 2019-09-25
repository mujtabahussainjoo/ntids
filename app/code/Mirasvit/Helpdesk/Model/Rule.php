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
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\Rule\Collection|\Mirasvit\Helpdesk\Model\Rule[] getCollection()
 * @method \Mirasvit\Helpdesk\Model\Rule load(int $id)
 * @method bool getIsMassDelete()
 * @method \Mirasvit\Helpdesk\Model\Rule setIsMassDelete(bool $flag)
 * @method bool getIsMassStatus()
 * @method \Mirasvit\Helpdesk\Model\Rule setIsMassStatus(bool $flag)
 * @method \Mirasvit\Helpdesk\Model\ResourceModel\Rule getResource()
 * @method bool getIsStopProcessing()
 * @method \Mirasvit\Helpdesk\Model\Rule setIsStopProcessing(bool $flag)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $param)
 * @method string getUpdatedAt()
 * @method $this setUpdatedAt(string $param)
 */
class Rule extends \Magento\Rule\Model\AbstractModel
{
    const TYPE_PRODUCT = 'product';
    const TYPE_CART = 'cart';
    const TYPE_TICKET = 'ticket';

    const CACHE_TAG = 'helpdesk_rule';

    /**
     * @var string
     */
    protected $_cacheTag = 'helpdesk_rule';

    /**
     * @var string
     */
    protected $_eventPrefix = 'helpdesk_rule';

    /**
     *
     * @var string
     */
    protected $_eventObject = 'rule';

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
     * @var \Mirasvit\Helpdesk\Model\Rule\Condition\CombineFactory
     */
    protected $ruleConditionCombineFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\Rule\Action\CollectionFactory
     */
    protected $ruleActionCollectionFactory;

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
     * @param \Mirasvit\Helpdesk\Model\Rule\Condition\CombineFactory       $ruleConditionCombineFactory
     * @param \Mirasvit\Helpdesk\Model\Rule\Action\CollectionFactory       $ruleActionCollectionFactory
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Framework\Data\FormFactory                          $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface         $localeDate
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\Rule\Condition\CombineFactory $ruleConditionCombineFactory,
        \Mirasvit\Helpdesk\Model\Rule\Action\CollectionFactory $ruleActionCollectionFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->ruleConditionCombineFactory = $ruleConditionCombineFactory;
        $this->ruleActionCollectionFactory = $ruleActionCollectionFactory;
        $this->context = $context;
        $this->registry = $registry;
        $this->formFactory = $formFactory;
        $this->localeDate = $localeDate;
        $this->resource = $resource;
        $this->resourceCollection = $resourceCollection;
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Helpdesk\Model\ResourceModel\Rule');
        $this->setIdFieldName('rule_id');
    }

    /**
     * @param string|false $emptyOption
     * @return array
     */
    public function toOptionArray($emptyOption = false)
    {
        return $this->getCollection()->toOptionArray($emptyOption);
    }

    /** Rule Methods **/
    public function getConditionsInstance()
    {
        return $this->ruleConditionCombineFactory->create();
    }

    /**
     * @return object
     */
    public function getActionsInstance()
    {
        return $this->ruleActionCollectionFactory->create();
    }

    /**
     * @return object
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductIds()
    {
        return $this->_getResource()->getRuleProductIds($this->getId());
    }

    /**
     * @param string $format We need it to be compatible with Magento\Framework\DataObject::toString.
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toString($format = '')
    {
        $this->load($this->getId());
        $string = $this->getConditions()->asStringRecursive();

        $string = nl2br(preg_replace('/ /', '&nbsp;', $string));

        return $string;
    }
    /**
     *
     */
    public function getNotificationEmailTemplate()
    {
        if (!$this->getData('notification_email_template')) {
            return 'helpdesk_rule_notification_email_template';
        }

        return $this->getData('notification_email_template');
    }
}
