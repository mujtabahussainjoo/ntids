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



namespace Mirasvit\Helpdesk\Model\Rule\Condition;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Ticket extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Field\CollectionFactory
     */
    protected $fieldCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Status\CollectionFactory
     */
    protected $statusCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Department\CollectionFactory
     */
    protected $departmentCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Priority\CollectionFactory
     */
    protected $priorityCollectionFactory;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Tag
     */
    protected $helpdeskTag;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Field
     */
    protected $helpdeskField;

    /**
     * @var \Mirasvit\Helpdesk\Helper\Html
     */
    protected $helpdeskHtml;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Field\CollectionFactory      $fieldCollectionFactory
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Status\CollectionFactory     $statusCollectionFactory
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Department\CollectionFactory $departmentCollectionFactory
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Priority\CollectionFactory   $priorityCollectionFactory
     * @param \Mirasvit\Helpdesk\Helper\Tag                                       $helpdeskTag
     * @param \Mirasvit\Helpdesk\Helper\Field                                     $helpdeskField
     * @param \Mirasvit\Helpdesk\Helper\Html                                      $helpdeskHtml
     * @param \Magento\Rule\Model\Condition\Context                               $context
     * @param \Magento\Framework\Registry                                         $registry
     * @param array                                                               $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\ResourceModel\Field\CollectionFactory $fieldCollectionFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Status\CollectionFactory $statusCollectionFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Department\CollectionFactory $departmentCollectionFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Priority\CollectionFactory $priorityCollectionFactory,
        \Mirasvit\Helpdesk\Helper\Tag $helpdeskTag,
        \Mirasvit\Helpdesk\Helper\Field $helpdeskField,
        \Mirasvit\Helpdesk\Helper\Html $helpdeskHtml,
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->fieldCollectionFactory = $fieldCollectionFactory;
        $this->statusCollectionFactory = $statusCollectionFactory;
        $this->departmentCollectionFactory = $departmentCollectionFactory;
        $this->priorityCollectionFactory = $priorityCollectionFactory;
        $this->helpdeskTag = $helpdeskTag;
        $this->helpdeskField = $helpdeskField;
        $this->helpdeskHtml = $helpdeskHtml;
        $this->context = $context;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $attributes = [
            'subject'                   => __('Subject'),
            'last_message'              => __('Last message body'),
            'created_at'                => __('Created At'),
            'updated_at'                => __('Updated At'),
            'store_id'                  => __('Store'),
            'old_priority_id'           => __('Priority (before change)'),
            'priority_id'               => __('Priority'),
            'old_status_id'             => __('Status (before change)'),
            'status_id'                 => __('Status'),
            'old_department_id'         => __('Department (before change)'),
            'department_id'             => __('Department'),
            'old_user_id'               => __('Owner (before change)'),
            'user_id'                   => __('Owner'),
            'old_folder'                => __('Folder (before change)'),
            'folder'                    => __('Folder'),
            'last_reply_by'             => __('Last Reply By'),
            'last_reply_type'           => __('Last Reply Type'),
            'hours_since_created_at'    => __('Hours since Created'),
            'hours_since_updated_at'    => __('Hours since Updated'),
            'hours_since_last_reply_at' => __('Hours since Last reply'),
            'tags'                      => __('Tags'),
            'channel'                   => __('Ticket Source (Channel)'),
            'customer_email'            => __('Customer Email'),
            'customer_name'             => __('Customer Name'),
        ];

        $fields = $this->fieldCollectionFactory->create()
                    ->setOrder('sort_order', \Mirasvit\Helpdesk\Model\Config::DEFAULT_SORT_ORDER);

        foreach ($fields as $field) {
            $attributes['old_'.$field->getCode()] = __('%1 (before change)', $field->getName());
            $attributes[$field->getCode()] = $field->getName();
        }

        // asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * @return $this
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    /**
     * @return string
     */
    public function getInputType()
    {
        $attrCode = $this->getAttribute();
        if (strpos($attrCode, '_id') || $attrCode == 'last_reply_by' || $attrCode == 'channel' ||
            $attrCode == 'last_reply_type' || $attrCode == 'folder' || $attrCode == 'old_folder') {
            return 'select';
        }

        if ($field = $this->getCustomFieldByAttributeCode($attrCode)) {
            if ($field->getType() == 'select') {
                return 'select';
            }
        }

        return 'string';
    }

    /**
     * @return string
     */
    public function getValueElementType()
    {
        switch ($this->getInputType()) {
            case 'string':
                return 'text';
        }

        return $this->getInputType();
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return int
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function validate(\Magento\Framework\Model\AbstractModel $object)
    {
        /* @var \Mirasvit\Helpdesk\Model\Ticket $object */
        $attrCode = $this->getAttribute();
        if (strpos($attrCode, 'old_') === 0) {
            $attrCode = str_replace('old_', '', $attrCode);
            $value = $object->getOrigData($attrCode);
        } elseif ($attrCode == 'last_message') {
            $value = $object->getLastMessagePlainText();
        } elseif ($attrCode == 'last_reply_by') {
            $lastMessage = $object->getLastMessage();
            if ($lastMessage->getUserId()) {
                $value = 'user';
            } elseif ($lastMessage->getCustomerId() != $object->getCustomerId()) {
                $value = 'third_party';
            } else {
                $value = 'customer';
            }
        } elseif ($attrCode == 'last_reply_type') {
            if ($lastMessage = $object->getLastMessage()) {
                $value = $lastMessage->getType();
            }
        } elseif (strpos($attrCode, 'hours_since_') === 0) {
            $attrCode = str_replace('hours_since_', '', $attrCode);
            $timestamp = $object->getData($attrCode);

            $diff = abs(strtotime((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT)) -
                strtotime($timestamp));
            $value = round($diff / 60 / 60);
        } elseif ($attrCode == 'tags') {
            $value = $this->helpdeskTag->getTagsAsString($object);
        } else {
            $value = $object->getData($attrCode);
        }
        if (strpos($attrCode, '_id')) {
            $value = (int) $value; //We need it to empty value to zero and then to compare
        }

        return $this->validateAttribute($value);
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *  @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _prepareValueOptions()
    {
        // Check that both keys exist. Maybe somehow only one was set not in this routine, but externally.
        $selectReady = $this->getData('value_select_options');
        $hashedReady = $this->getData('value_option');
        if ($selectReady && $hashedReady) {
            return $this;
        }
        // Get array of select options. It will be used as source for hashed options
        $selectOptions = null;
        $addNotEmpty = true;
        $field = $this->getCustomFieldByAttributeCode($this->getAttribute());

        if ($field && $field->getType() == 'select') {
            $selectOptions = $field->getValues();
        } else {
            switch ($this->getAttribute()) {
                case 'status_id':
                case 'old_status_id':
                    $selectOptions = $this->statusCollectionFactory->create()->getOptionArray();
                    break;
                case 'department_id':
                case 'old_department_id':
                    $selectOptions = $this->departmentCollectionFactory->create()->getOptionArray();
                    break;
                case 'priority_id':
                case 'old_priority_id':
                    $selectOptions = $this->priorityCollectionFactory->create()->getOptionArray();
                    break;
                case 'user_id':
                case 'old_user_id':
                    $selectOptions = $this->helpdeskHtml->getAdminUserOptionArray();
                    break;
                case 'folder':
                case 'old_folder':
                    $selectOptions = $this->helpdeskHtml->getFolderOptionArray();
                    break;
                case 'store_id':
                    $selectOptions = $this->helpdeskHtml->getCoreStoreOptionArray();
                    break;
                case 'last_reply_by':
                    $selectOptions = [
                            'customer' => __('Customer'),
                            'user' => __('Staff'),
                            'third_party' => __('Third party'),
                        ];
                    $addNotEmpty = false;
                    break;
                case 'last_reply_type':
                    $selectOptions = $this->helpdeskHtml->getMessageTypesOptionArray();
                    $addNotEmpty = false;
                    break;
                case 'channel':
                    $selectOptions = $this->helpdeskHtml->getChannelOptionArray();
                    $addNotEmpty = false;
                    break;
                default:
                    return $this;
            }
        }
        if ($addNotEmpty) {
            $selectOptions = [0 => '(not set)'] + $selectOptions;
            // array_unshift($selectOptions, '(not set)');
        }

        $optionsA = [];
        foreach ($selectOptions as $key => $value) {
            $optionsA[] = ['value' => $key, 'label' => $value];
        }
        $selectOptions = $optionsA;

        // Set new values only if we really got them
        if ($selectOptions !== null) {
            // Overwrite only not already existing values
            if (!$selectReady) {
                $this->setData('value_select_options', $selectOptions);
            }
            if (!$hashedReady) {
                $hashedOptions = [];
                foreach ($selectOptions as $o) {
                    if (is_array($o['value'])) {
                        continue; // We cannot use array as index
                    }
                    $hashedOptions[$o['value']] = $o['label'];
                }
                $this->setData('value_option', $hashedOptions);
            }
        }

        return $this;
    }

    /**
     * Retrieve value by option.
     *
     * @param object $option
     *
     * @return string
     */
    public function getValueOption($option = null)
    {
        $this->_prepareValueOptions();

        return $this->getData('value_option'.($option !== null ? '/'.$option : ''));
    }

    /**
     * Retrieve select option values.
     *
     * @return array
     */
    public function getValueSelectOptions()
    {
        $this->_prepareValueOptions();

        return $this->getData('value_select_options');
    }

    /**
     * @return string
     */
    public function getJsFormObject()
    {
        return 'rule_conditions_fieldset';
    }

    /**
     * @param string $attrCode
     * @return \Mirasvit\Helpdesk\Model\Field
     */
    protected function getCustomFieldByAttributeCode($attrCode)
    {
        if (strpos($attrCode, 'f_') === 0 || strpos($attrCode, 'old_f_') === 0) {
            $attrCode = str_replace('old_f_', 'f_', $attrCode);

            if ($field = $this->helpdeskField->getFieldByCode($attrCode)) {
                return $field;
            }
        }
    }
}
