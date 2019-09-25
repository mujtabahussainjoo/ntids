<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ibnab\OwlSlider\Controller\Adminhtml\Sliders;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Ibnab\OwlSlider\Model\Sliders
     */
    protected $sliderFactory;
    /**
     * @var \Ibnab\OwlSlider\Model\BannerSlider
     */
    protected $bannerFactory;
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $dataHelper;
    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     */
    public function __construct(Action\Context $context,\Ibnab\OwlSlider\Model\SlidersFactory $sliderFactory,\Ibnab\OwlSlider\Model\BannerSliderFactory $bannerFactory,\Ibnab\OwlSlider\Helper\Data $dataHelper)
    {
        //$this->dataProcessor = $dataProcessor;
        $this->sliderFactory = $sliderFactory;
        $this->bannerFactory = $bannerFactory;
        $this->dataHelper = $dataHelper;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ibnab_OwlSlider::owlslider_sliders_save');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            //$data = $this->dataProcessor->filter($data);
            $model = $this->sliderFactory->create();

            $id = $this->getRequest()->getParam('slider_id');
            if ($id) {
                $model->load($id);
            }
            $model->setData($data);
   
            try {
                $links = $this->getRequest()->getParam('links');
                $bannerFactory = null;
                if(isset($links['banners'])){
                  $bannersId = $links['banners'];
	          $bannersId=explode("&",$bannersId);
                  $allidsgrid = array();
		  if($bannersId != "")
		  {
		    foreach($bannersId as $bannerId)
		    {
		     $parIds=explode("=",$bannerId);
		     $allidsgrid[]= (int) $parIds[0];
		    }
                     $model->setData("banners_id",$allidsgrid);
                     
		    }
                   }
                if(isset($links['products'])){
                  $productsId = $links['products'];
	          $productsId=explode("&",$productsId);
                  $allidsgrid = array();
		  if($productsId != "")
		  {
		    foreach($productsId as $productId)
		    {
		     $parIds=explode("=",$productId);
		     $allidsgrid[]= (int) $parIds[0];
		    }
                     $model->setData("products_id",$allidsgrid);
                     
		    }
                   }
                $model->save();
                //$selectedBannersId = $this->dataHelper->initializeGridValue();

                $this->messageManager->addSuccess(__('You saved this slider.'));
                $this->_session->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['slider_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, $e->getMessage());
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['slider_id' => $this->getRequest()->getParam('slider_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
