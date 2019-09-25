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



namespace Mirasvit\Helpdesk\Controller\Adminhtml\Ticket;

use Magento\Framework\Controller\ResultFactory;

class Customerfind extends \Mirasvit\Helpdesk\Controller\Adminhtml\Ticket
{
    /**
     * Do search of customers.
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $query = $this->getRequest()->getParam('query', '');
        $result = $this->helpdeskCustomer->findCustomer($query);
        // Example of result
        //        $result = [
        //          ['id' => 1, 'email' => 'sonya1@example.com', 'name' => 'Sonya1', 'label' => 'sonya1@example.com'],
        //          ['id' => 2, 'email' => 'sonya2@example.com', 'name' => 'Sonya2', 'label' => 'sonya2@example.com'],
        //          ['id' => 3, 'email' => 'sonya3@example.com', 'name' => 'Sonya3', 'label' => 'sonya3@example.com'],
        //        ];
        $resultPage->setData($result);
        return $resultPage;
    }
}
