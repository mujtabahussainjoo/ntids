<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ibnab\OwlSlider\Controller\Adminhtml\Banners;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
/**
 * OwlSlider sliders grid inline edit controller
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InlineEdit extends \Magento\Backend\App\Action
{

    /** @var SlidersFactory  */
    protected $bannerFactory;

    /** @var JsonFactory  */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param SlidersFactory $sliderFactory
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        \Ibnab\OwlSlider\Model\BannersFactory $bannerFactory,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->bannerFactory = $bannerFactory;
        $this->jsonFactory = $jsonFactory;
    }
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ibnab_OwlSlider::owlslider_banners_save');
    }
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }
        $model = $this->bannerFactory->create();
        $banner = null;
        foreach (array_keys($postItems) as $bannerId) {
                $banner = $model->load($bannerId);
                $currentItem = $postItems[$bannerId];
                foreach($currentItem as $key => $item)
                {
                  $banner->setData($key,$item);
                }
                
            
            try {
                $banner->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $e->getMessage();
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $e->getMessage();
                $error = true;
            } catch (\Exception $e) {
                $messages[] = __('Something went wrong while saving the page.');
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
