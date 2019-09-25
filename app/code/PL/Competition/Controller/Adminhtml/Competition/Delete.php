<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/20/2016
 * Time: 4:53 PM
 */

namespace PL\Competition\Controller\Adminhtml\Competition;

class Delete extends \PL\Competition\Controller\Adminhtml\Competition{

    public function execute()
    {
        // TODO: Implement execute() method.
        $id = (int) $this->getRequest()->getParam('entity_id');

        if ($id) {
            /** @var $newsModel \PL\News\Model\News */
            $model = $this->_competitionFactory->create();
            $model->load($id);

            // Check this news exists or not
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Competition no longer exists.'));
            } else {
                try {

                    $model->delete();
                    $this->messageManager->addSuccess(__('The Competition has been deleted.'));

                    // Redirect to grid page
                    $this->_redirect('*/*/');
                    return;
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $this->_redirect('*/*/edit', ['entity_id' => $model->getId()]);
                }
            }
        }
    }

}