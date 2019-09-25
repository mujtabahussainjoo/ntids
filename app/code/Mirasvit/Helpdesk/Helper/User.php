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

class User extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @var \Mirasvit\Helpdesk\Model\UserFactory
     */
    protected $userFactory;

    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $auth;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @param \Magento\Framework\App\Helper\Context     $context
     * @param \Magento\Backend\Model\Auth               $auth
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Mirasvit\Helpdesk\Model\UserFactory      $userFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\Auth $auth,
        \Magento\Framework\App\ResourceConnection $resource,
        \Mirasvit\Helpdesk\Model\UserFactory $userFactory
    ) {
        $this->context = $context;
        $this->auth = $auth;
        $this->resource = $resource;
        $this->userFactory = $userFactory;
        parent::__construct($context);
    }

    /**
     * @return bool|\Mirasvit\Helpdesk\Model\User
     */
    public function getHelpdeskUser()
    {
        if (!$user = $this->auth->getUser()) {
            return false;
        }
        $helpdeskUser = $this->userFactory->create();
        $resource = $helpdeskUser->getResource();
        $resource->load($helpdeskUser, $user->getId());
        $helpdeskUser->setId($user->getId());

        return $helpdeskUser;
    }
}
