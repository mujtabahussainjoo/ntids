<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/20/2016
 * Time: 12:27 PM
 */
namespace PL\Competition\Controller\Adminhtml\Competition;


class Edit extends \PL\Competition\Controller\Adminhtml\Competition
{


    public function execute()
    {
        $id = $this->getRequest()->getParam('entity_id');

        $model = $this->_competitionFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Competition no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        // Restore previously entered form data from session
        $data = $this->_session->getSalenoticeData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('_current_competition', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('PL_Competition::competition');
        $resultPage->getConfig()->getTitle()->prepend(__('Competition'));

        return $resultPage;
    }


}