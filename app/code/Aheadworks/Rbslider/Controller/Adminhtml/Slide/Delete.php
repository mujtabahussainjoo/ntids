<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Controller\Adminhtml\Slide;

use Magento\Backend\App\Action\Context;
use Aheadworks\Rbslider\Api\SlideRepositoryInterface;

/**
 * Class Delete
 * @package Aheadworks\Rbslider\Controller\Adminhtml\Slide
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_Rbslider::slides';

    /**
     * @var SlideRepositoryInterface
     */
    private $slideRepository;

    /**
     * @param Context $context
     * @param SlideRepositoryInterface $slideRepository
     */
    public function __construct(
        Context $context,
        SlideRepositoryInterface $slideRepository
    ) {
        parent::__construct($context);
        $this->slideRepository = $slideRepository;
    }

    /**
     * Delete slide action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->slideRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('Slide was successfully deleted'));
                return $resultRedirect->setPath('*/*/index');
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
        }
        $this->messageManager->addErrorMessage(__('Slide could not be deleted'));
        return $resultRedirect->setPath('*/*/index');
    }
}
