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



namespace Mirasvit\Helpdesk\Helper;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Field extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Field\CollectionFactory
     */
    protected $fieldCollectionFactory;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Field\CollectionFactory $fieldCollectionFactory
     * @param \Magento\Framework\App\Helper\Context                          $context
     * @param \Magento\Store\Model\StoreManagerInterface                     $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface           $localeDate
     * @param \Magento\Framework\View\Asset\Repository                       $assetRepo
     * @param \Magento\Framework\ObjectManagerInterface                      $objectManager
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\ResourceModel\Field\CollectionFactory $fieldCollectionFactory,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->fieldCollectionFactory = $fieldCollectionFactory;
        $this->context = $context;
        $this->storeManager = $storeManager;
        $this->localeDate = $localeDate;
        $this->assetRepo = $assetRepo;
        $this->objectManager = $objectManager;
        parent::__construct($context);
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\Field[]|\Mirasvit\Helpdesk\Model\ResourceModel\Field\Collection
     */
    public function getEditableCustomerCollection()
    {
        return $this->fieldCollectionFactory->create()
            ->addFieldToFilter('is_active', true)
            ->addFieldToFilter('is_editable_customer', true)
            ->addStoreFilter($this->storeManager->getStore()->getId())
            ->setOrder('sort_order', \Mirasvit\Helpdesk\Model\Config::DEFAULT_SORT_ORDER);
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\Field[]|\Mirasvit\Helpdesk\Model\ResourceModel\Field\Collection
     */
    public function getVisibleCustomerCollection()
    {
        return $this->fieldCollectionFactory->create()
            ->addFieldToFilter('is_active', true)
            ->addFieldToFilter('is_visible_customer', true)
            ->addStoreFilter($this->storeManager->getStore()->getId())
            ->setOrder('sort_order', \Mirasvit\Helpdesk\Model\Config::DEFAULT_SORT_ORDER);
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\Field[]|\Mirasvit\Helpdesk\Model\ResourceModel\Field\Collection
     */
    public function getContactFormCollection()
    {
        return $this->fieldCollectionFactory->create()
            ->addFieldToFilter('is_active', true)
            ->addFieldToFilter('is_visible_contact_form', true)
            ->addStoreFilter($this->storeManager->getStore()->getId())
            ->setOrder('sort_order', \Mirasvit\Helpdesk\Model\Config::DEFAULT_SORT_ORDER);
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\Field[]|\Mirasvit\Helpdesk\Model\ResourceModel\Field\Collection
     */
    public function getStaffCollection()
    {
        return $this->fieldCollectionFactory->create()
            ->addFieldToFilter('is_active', true)
            ->setOrder('sort_order', \Mirasvit\Helpdesk\Model\Config::DEFAULT_SORT_ORDER);
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\Field[]|\Mirasvit\Helpdesk\Model\ResourceModel\Field\Collection
     */
    public function getActiveCollection()
    {
        return $this->fieldCollectionFactory->create()
            ->addFieldToFilter('is_active', true)
            ->addStoreFilter($this->storeManager->getStore()->getId())
            ->setOrder('sort_order', \Mirasvit\Helpdesk\Model\Config::DEFAULT_SORT_ORDER);
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Field       $field
     * @param bool                                 $staff
     * @param bool|\Mirasvit\Helpdesk\Model\Ticket $ticket
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getInputParams($field, $staff = true, $ticket = false)
    {
        $params = [
            'label'        => __($field->getName()),
            'name'         => $field->getCode(),
            'required'     => $staff ? $field->getIsRequiredStaff() : $field->getIsRequiredCustomer(),
            'value'        => $field->getType() == 'checkbox'
                ? 1
                : ($ticket ? $ticket->getData($field->getCode()) : ''),
            'checked'      => $ticket ? $ticket->getData($field->getCode()) : false,
            'values'       => $field->getValues(true),
            'note'         => $field->getDescription(),
            'date_format'  => 'yyyy-MM-dd',
            'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
        ];

        return $params;
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Field $field
     *
     * @return string
     */
    public function getInputHtml($field)
    {
        $params = $this->getInputParams($field, false);
        unset($params['label']);
        $className = '\Magento\Framework\Data\Form\Element\\' . ucfirst(strtolower($field->getType()));
        /** @var \Magento\Framework\Data\Form\Element\AbstractElement $element */
        $element = $this->objectManager->create($className);
        $element->setData($params);
        $element->setForm(new \Magento\Framework\DataObject());
        $element->setType($field->getType());
        $element->setId($field->getCode());
        $element->setNoSpan(true);
        if ($field->getIsRequiredCustomer()) {
            $element->addClass('required-entry');
        }

        $html = $element->toHtml();
        return $html;
    }

    /**
     * @param array                           $post
     * @param \Mirasvit\Helpdesk\Model\Ticket $ticket
     * @return void
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processPost($post, $ticket)
    {
        $collection = $this->getActiveCollection();
        foreach ($collection as $field) {
            if (isset($post[$field->getCode()])) {
                $value = $post[$field->getCode()];
                $ticket->setData($field->getCode(), $value);
            }
            if ($field->getType() == 'checkbox') {
                if (!isset($post[$field->getCode()])) {
                    $ticket->setData($field->getCode(), 0);
                }
            } elseif ($field->getType() == 'date') {
                $value = $ticket->getData($field->getCode());
                try {
                    $value = $this->localeDate->formatDate($value, \IntlDateFormatter::SHORT);
                } catch (\Exception $e) { //we have exception if input date is in incorrect format
                    $value = '';
                }
                $ticket->setData($field->getCode(), $value);
            }
        }
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Ticket $ticket
     * @param \Mirasvit\Helpdesk\Model\Field  $field
     *
     * @return bool|string
     */
    public function getValue($ticket, $field)
    {
        $value = $ticket->getData($field->getCode());
        if (!$value) {
            return false;
        }
        if ($field->getType() == 'checkbox') {
            $value = $value ? __('Yes') : __('No');
        } elseif ($field->getType() == 'date') {
            try {
                $value = $this->localeDate->formatDate($value, \IntlDateFormatter::MEDIUM);
            } catch (\Exception $e) { //we have exception if input date is in incorrect format
                $value = '';
            }
        } elseif ($field->getType() == 'select') {
            $values = $field->getValues();
            // if value was deleted but ticket still contains it, we return empty string
            $value = isset($values[$value]) ? $values[$value] : '';
        }

        return $value;
    }

    /**
     * @param string $code
     *
     * @return \Mirasvit\Helpdesk\Model\Field
     */
    public function getFieldByCode($code)
    {
        $field = $this->fieldCollectionFactory->create()
            ->addFieldToFilter('code', $code)
            ->getFirstItem();
        if ($field->getId()) {
            return $field;
        }
    }
}
