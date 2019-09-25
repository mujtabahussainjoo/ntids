<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ibnab\OwlSlider\Controller\Adminhtml\Sliders;

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
    protected $sliderFactory;

    /** @var JsonFactory  */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param SlidersFactory $sliderFactory
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        \Ibnab\OwlSlider\Model\SlidersFactory $sliderFactory,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->sliderFactory = $sliderFactory;
        $this->jsonFactory = $jsonFactory;
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
        $model = $this->sliderFactory->create();
        
        foreach (array_keys($postItems) as $sliderId) {
                $slider = $model->load($sliderId);
                $currentItem = $postItems[$sliderId];
                foreach($currentItem as $key => $item)
                {
                  $slider->setData($key,$item);
                }
                
            
            try {
                $slider->save();
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
