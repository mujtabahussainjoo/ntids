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



namespace Mirasvit\Helpdesk\Controller\Adminhtml\Status;

use Mirasvit\Helpdesk\Model\Config as Config;

class Delete extends \Mirasvit\Helpdesk\Controller\Adminhtml\Status
{
    /**
     * @throws \Exception
     *
     * @return void
     */
    public function execute()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $status = $this->statusFactory->create()->load($id);
                $code = $status->getCode();
                if ($code == Config::STATUS_OPEN
                    || $code == Config::STATUS_CLOSED) {
                    throw new \Exception("You can't remove \"".$status->getName()."\" status. It's required by system");
                }
                $status->getResource()->delete($status);

                $this->messageManager->addSuccessMessage(
                    __('Status was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_redirect('*/*/edit', ['id' => $this->getRequest()
                    ->getParam('id'), ]);
            }
        }
        $this->_redirect('*/*/');
    }
}
