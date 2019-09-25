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

use Mirasvit\Helpdesk\Model\Config;

class Html extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\User\Model\ResourceModel\User\CollectionFactory
     */
    protected $userCollectionFactory;

    /**
     * @var \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory
     */
    protected $roleCollectionFactory;

    /**
     * @var \Magento\Store\Model\ResourceModel\Store\CollectionFactory
     */
    protected $storeCollectionFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context                               $context
     * @param \Magento\User\Model\ResourceModel\User\CollectionFactory            $userCollectionFactory
     * @param \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory   $roleCollectionFactory
     * @param \Magento\Store\Model\ResourceModel\Store\CollectionFactory          $storeCollectionFactory
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Department\CollectionFactory $departmentCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\User\Model\ResourceModel\User\CollectionFactory $userCollectionFactory,
        \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory $roleCollectionFactory,
        \Magento\Store\Model\ResourceModel\Store\CollectionFactory $storeCollectionFactory,
        \Mirasvit\Helpdesk\Model\ResourceModel\Department\CollectionFactory $departmentCollectionFactory
    ) {
        $this->userCollectionFactory = $userCollectionFactory;
        $this->roleCollectionFactory = $roleCollectionFactory;
        $this->departmentCollectionFactory = $departmentCollectionFactory;
        $this->storeCollectionFactory = $storeCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @param bool $emptyOption
     *
     * @return array
     */
    public function toAdminUserOptionArray($emptyOption = false)
    {
        $arr = $this->userCollectionFactory->create()->addFieldToFilter('is_active', 1)->toArray();
        $result = [];
        foreach ($arr['items'] as $value) {
            $result[] = ['value' => $value['user_id'], 'label' => $value['firstname'].' '.$value['lastname']];
        }
        if ($emptyOption) {
            array_unshift($result, ['value' => 0, 'label' => __('-- Please Select --')]);
        }

        return $result;
    }

    /**
     * @param bool $emptyOption
     *
     * @return array
     */
    public function getAdminUserOptionArray($emptyOption = false)
    {
        $arr = $this->userCollectionFactory->create()->toArray();
        $result = [];
        foreach ($arr['items'] as $value) {
            $result[$value['user_id']] = $value['firstname'].' '.$value['lastname'];
        }
        if ($emptyOption) {
            $result[0] = __('-- Please Select --');
        }

        return $result;
    }

    /**
     * @param bool $emptyOption
     *
     * @return array
     */
    public function getFolderOptionArray($emptyOption = false)
    {
        $arr = [];
        if ($emptyOption) {
            $arr[0] = __('-- Please Select --');
        }
        $arr[Config::FOLDER_INBOX]   = __('Inbox')->getText();
        $arr[Config::FOLDER_ARCHIVE] = __('Archive')->getText();
        $arr[Config::FOLDER_SPAM]    = __('Spam')->getText();

        return $arr;
    }

    /**
     * @param bool $emptyOption
     *
     * @return array
     */
    public function toAdminRoleOptionArray($emptyOption = false)
    {
        $arr = $this->roleCollectionFactory->create()
            ->addFieldToFilter('role_type', 'G')
            ->toArray();
        $result = [];
        foreach ($arr['items'] as $value) {
            $result[] = ['value' => $value['role_id'], 'label' => $value['role_name']];
        }
        if ($emptyOption === true) {
            $emptyOption = '-- Please Select --';
        }
        if ($emptyOption) {
            array_unshift($result, ['value' => 0, 'label' => __($emptyOption)]);
        }

        return $result;
    }

    /**
     * @param bool $emptyOption
     *
     * @return array
     */
    public function getAdminRoleOptionArray($emptyOption = false)
    {
        $arr = $this->roleCollectionFactory->create()
            ->addFieldToFilter('role_type', 'G')
            ->toArray();
        $result = [];
        foreach ($arr['items'] as $value) {
            $result[$value['role_id']] = $value['role_name'];
        }
        if ($emptyOption === true) {
            $emptyOption = '-- Please Select --';
        }
        if ($emptyOption) {
            $result[0] = __($emptyOption);
        }

        return $result;
    }

    /**
     * @param bool $emptyOption
     *
     * @return array
     */
    public function getCoreStoreOptionArray($emptyOption = false)
    {
        $result = [];
        $arr = $this->storeCollectionFactory->create()
            ->setOrder('sort_order', \Mirasvit\Helpdesk\Model\Config::DEFAULT_SORT_ORDER)
            ->toArray();
        foreach ($arr['items'] as $value) {
            $result[$value['store_id']] = $value['name'];
        }
        if ($emptyOption) {
            $result[0] = __('-- Please Select --');
        }

        return $result;
    }

    /**
     * @param bool $emptyOption
     * @param bool $storeId
     *
     * @return array
     */
    public function getAdminOwnerOptionArray($emptyOption = false, $storeId = false)
    {
        $result = [];
        if ($emptyOption) {
            $result['0_0'] = __('-- Please Select --');
        }
        $collection = $this->departmentCollectionFactory->create()
            ->addFieldToFilter('is_active', true)
            ->setOrder('sort_order', \Mirasvit\Helpdesk\Model\Config::DEFAULT_SORT_ORDER);
        if ($storeId) {
            $collection->addStoreFilter($storeId);
        }
        foreach ($collection as $department) {
            /* @var \Mirasvit\Helpdesk\Model\Department $department */
            $result[$department->getId().'_0'] = $department->getName();
            foreach ($department->getUsers() as $user) {
                $result[$department->getId().'_'.$user->getId()] =
                    '- '.$user->getFirstname().' '.$user->getLastname();
            }
        }

        return $result;
    }

    /**
     * @param bool $emptyOption
     *
     * @return array
     */
    public function getChannelOptionArray($emptyOption = false)
    {
        $result = [];
        if ($emptyOption) {
            $result['0_0'] = __('-- Please Select --');
        }
        $result = [
            Config::CHANNEL_EMAIL            => __('Email'),
            Config::CHANNEL_BACKEND          => __('Backend'),
            Config::CHANNEL_FEEDBACK_TAB     => __('Contact Tab'),
            Config::CHANNEL_CONTACT_FORM     => __('Contact Form'),
            Config::CHANNEL_CUSTOMER_ACCOUNT => __('Customer Account'),
        ];

        return $result;
    }

    /**
     * @param bool $emptyOption
     *
     * @return array
     */
    public function getMessageTypesOptionArray($emptyOption = false)
    {
        $result = [];
        if ($emptyOption) {
            $result['0_0'] = __('-- Please Select --');
        }
        $result = [
            Config::MESSAGE_PUBLIC => __('Public Reply'),
            Config::MESSAGE_INTERNAL => __('Internal Note'),
            Config::MESSAGE_PUBLIC_THIRD => __('Message to Third Party'),
            Config::MESSAGE_INTERNAL_THIRD => __('Internal Note to Third Party'),
        ];

        return $result;
    }
}
