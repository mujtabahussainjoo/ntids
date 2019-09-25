<?php

namespace Ibnab\OwlSlider\Controller\Ajax;

class Listproduct extends \Magento\Framework\App\Action\Action {

    protected $sliderFactory;
    protected $layoutFactory;
    public function __construct(
    \Magento\Framework\App\Action\Context $context, \Ibnab\OwlSlider\Model\SlidersFactory $sliderFactory,
    \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        $this->sliderFactory = $sliderFactory;
        $this->layoutFactory = $layoutFactory;
        parent::__construct($context);
    }

    public function execute() {
        /*         * *create and save* */
        $id = $this->getRequest()->getParam('slider_id');
        $model = $this->sliderFactory->create();
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                echo "Content has no data";
            } else {
                echo $this->layoutFactory->create()
                                ->createBlock('\Ibnab\OwlSlider\Block\FSlider\ListerProduct')
                                ->setSlider($model)->toHTML();
            }
        } else {
            echo "Content has no data";
        }
    }

}
