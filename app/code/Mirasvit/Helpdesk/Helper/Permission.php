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

use Mirasvit\Helpdesk\Model;

class Permission extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Mirasvit\Helpdesk\Model\ResourceModel\Permission\CollectionFactory
     */
    protected $permissionCollectionFactory;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $auth;

    /**
     * @param \Mirasvit\Helpdesk\Model\ResourceModel\Permission\CollectionFactory $permissionCollectionFactory
     * @param \Magento\Framework\App\Helper\Context                               $context
     * @param \Magento\Backend\Model\Auth                                         $auth
     */
    public function __construct(
        \Mirasvit\Helpdesk\Model\ResourceModel\Permission\CollectionFactory $permissionCollectionFactory,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\Auth $auth
    ) {
        $this->permissionCollectionFactory = $permissionCollectionFactory;
        $this->context = $context;
        $this->auth = $auth;
        parent::__construct($context);
    }

    /**
     * @param Model\ResourceModel\Ticket\Collection|\Mirasvit\Helpdesk\Model\Ticket[] $ticketCollection
     *
     * @return \Mirasvit\Helpdesk\Model\ResourceModel\Ticket\Collection|\Mirasvit\Helpdesk\Model\Ticket[]
     */
    public function setTicketRestrictions($ticketCollection)
    {
        if (!$permission = $this->getPermission()) {
            $ticketCollection->addFieldToFilter('main_table.department_id', -1);

            return $ticketCollection;
        }
        $departmentIds = $permission->getDepartmentIds();

        if (in_array(0, $departmentIds)) {
            return $ticketCollection;
        }
        $ticketCollection->addFieldToFilter('main_table.department_id', $departmentIds);

        return $ticketCollection;
    }

    /**
     * @param Model\ResourceModel\Department\Collection|\Mirasvit\Helpdesk\Model\Department[] $departmentCollection
     *
     * @return \Mirasvit\Helpdesk\Model\ResourceModel\Department\Collection|\Mirasvit\Helpdesk\Model\Department[]
     */
    public function setDepartmentRestrictions($departmentCollection)
    {
        if (!$permission = $this->getPermission()) {
            $departmentCollection->addFieldToFilter('department_id', -1);

            return $departmentCollection;
        }

        $departmentIds = $permission->getDepartmentIds();
        if (in_array(0, $departmentIds)) {
            return $departmentCollection;
        }
        $departmentCollection->addFieldToFilter('department_id', $departmentIds);

        return $departmentCollection;
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\Permission
     */
    public function getPermission()
    {
        /** @var \Magento\User\Model\User $user */
        $user = $this->auth->getUser();
        $permissions = $this->permissionCollectionFactory->create()
            ->addFieldToFilter(
                'role_id',
                [
                    [
                        'attribute' => 'role_id',
                        'null' => 'this_value_doesnt_matter',
                    ],
                    [
                        'attribute' => 'role_id',
                        'in' => $user->getRoles(),
                    ],
                ]
            );

        if ($permissions->count()) {
            $permission = $permissions->getFirstItem();
            $permission->loadDepartmentIds();

            return $permission;
        }
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Ticket $ticket
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function checkReadTicketRestrictions($ticket)
    {
        $allow = false;
        if ($permission = $this->getPermission()) {
            $departmentIds = $permission->getDepartmentIds();
            if (in_array(0, $departmentIds)) {
                $allow = true;
            } else {
                if (in_array($ticket->getDepartmentId(), $departmentIds)) {
                    $allow = true;
                }
            }
        }
        if (!$allow) {
            echo __('You don\'t have permissions to read this ticket. Please, contact your administrator.');
            die;
        }
    }

    /**
     * @return bool
     */
    public function isTicketRemoveAllowed()
    {
        if ($permission = $this->getPermission()) {
            return $permission->getIsTicketRemoveAllowed();
        }

        return false;
    }
}
