<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ibnab\OwlSlider\Controller\Adminhtml\Banners;
use Magento\Backend\App\Action;
class Delete extends \Magento\Backend\App\Action
{
    protected $bannerFactory;
    /**
     * @param Action\Context $context
     * @param \Ibnab\OwlSlider\Model\BannersFactory $bannerFactory
     */
    public function __construct(Action\Context $context,\Ibnab\OwlSlider\Model\BannersFactory $bannerFactory)
    {
        //$this->dataProcessor = $dataProcessor;
        $this->bannerFactory = $bannerFactory;
        parent::__construct($context);
    }
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ibnab_OwlSlider::owlslider_banners_delete');
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('banner_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = "";
            try {
                // init model and delete
                $model = $this->bannerFactory->create();;
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('The banner has been deleted.'));

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['banner_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a banner to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
