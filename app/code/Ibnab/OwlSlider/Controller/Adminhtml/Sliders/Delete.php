<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ibnab\OwlSlider\Controller\Adminhtml\Sliders;
use Magento\Backend\App\Action;
class Delete extends \Magento\Backend\App\Action
{
    /**
     * @param Action\Context $context
     * @param \Ibnab\OwlSlider\Model\SlidersFactory $sliderFactory
     */
    public function __construct(Action\Context $context,\Ibnab\OwlSlider\Model\SlidersFactory $sliderFactory)
    {
        //$this->dataProcessor = $dataProcessor;
        $this->sliderFactory = $sliderFactory;
        parent::__construct($context);
    }
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ibnab_OwlSlider::owlslider_sliders_delete');
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('slider_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = "";
            try {
                // init model and delete
                $model = $model = $this->sliderFactory->create();;
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('The slider has been deleted.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['slider_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a slider to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
