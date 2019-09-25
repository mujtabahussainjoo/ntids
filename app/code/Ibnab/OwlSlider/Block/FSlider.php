<?php

namespace Ibnab\OwlSlider\Block;

use Ibnab\OwlSlider\Model\Sliders;

class FSlider extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface {

    /**
     * Config path to enable extension
     */
    const XML_PATH_PRODUCT_SLIDER_STATUS = "productslider/general/enable_productslider";

    /**
     * Main template container
     */
    protected $_template = 'Ibnab_OwlSlider::fslider.phtml';

    /**
     * Product slider collection factory
     *
     * @var \Ibnab\OwlSlider\Model\ResourceModel\Sliders\CollectionFactory
     */
    protected $_sliderCollectionFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    protected $_layoutConfig;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ibnab\OwlSlider\Model\ResourceModel\Sliders\CollectionFactory $sliderCollectionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Ibnab\OwlSlider\Model\ResourceModel\Sliders\CollectionFactory $sliderCollectionFactory,
        array $data = []
    ){
        $this->_sliderCollectionFactory = $sliderCollectionFactory;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_layoutConfig = $context->getLayout();
        parent::__construct($context,$data);
    }

    /**
     * Initialize slider if there is a widget slider active
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        if($this->getData('widget_slider_id')){
            $this->setSliderLocation(null);
        }
    }

    /**
     * Render block HTML
     * if extension is enabled then render HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        //if($this->_scopeConfig->getValue(self::XML_PATH_PRODUCT_SLIDER_STATUS,\Magento\Store\Model\ScopeInterface::SCOPE_STORES)){
            return parent::_toHtml();
        //}
        //return false;
    }


    public function setSliderLocation($location, $hide = false){
        $todayDateTime = $this->_localeDate->date()->format('Y-m-d H:i:s');
        $widgetSliderId = $this->getData('widget_slider_id');
        $cartHandles = ['0'=>'checkout_cart_index'];
        $checkoutHandles = ['0'=>'checkout_index_index','1'=>'checkout_onepage_failure', "2"=>'checkout_onepage_success'];
        $currentHandles = $this->_layoutConfig->getUpdate()->getHandles();

         /*
        // Get data without start/end time
        $sliderCollection = $this->_sliderCollectionFactory->create()
            ->addFieldToFilter('is_active',Sliders::STATUS_ENABLED)
            ->addFieldToFilter('startTime',['null' => true])
            ->addFieldToFilter('endTime',['null' => true]);

        // Check to exclude from cart page
        if(array_intersect($cartHandles,$currentHandles)){
            $sliderCollection->addFieldToFilter('excludeFromCart',0);
        }

        // Check to exclude from checkout
        if(array_intersect($checkoutHandles,$currentHandles)){
            $sliderCollection->addFieldToFilter('excludeFromCheckout',0);
        }

        // If widget_slider_id is not null
        if($widgetSliderId){
            $sliderCollection->addFieldToFilter('slider_id',$widgetSliderId);
        } else {
            $sliderCollection->addFieldToFilter('location',$location);
        }

        */
        // Get data with start/end time
        $sliderCollectionTimer = $this->_sliderCollectionFactory->create()
            ->addFieldToFilter('is_active',Sliders::STATUS_ENABLED)
            ->addFieldToFilter('startTime', [
                                    'or' => [
                                        0 => ['date' => true, 'lteq' => $todayDateTime],
                                        1 => ['is' => new \Zend_Db_Expr('null')],
                                    ]
                                ])
            ->addFieldToFilter('endTime',
                                [
                                    'or' => [
                                        0 => ['date' => true, 'from' => $todayDateTime],
                                        1 => ['is' => new \Zend_Db_Expr('null')],
                                    ]
                                ]);

        // Check to exclude from cart page
        if(array_intersect($cartHandles,$currentHandles)){
            $sliderCollectionTimer->addFieldToFilter('excludeFromCart',0);
        }

        // Check to exclude from checkout
        if(array_intersect($checkoutHandles,$currentHandles)){
            $sliderCollectionTimer->addFieldToFilter('excludeFromCheckout',0);
        }

        if($widgetSliderId){
            $sliderCollectionTimer->addFieldToFilter('slider_id',$widgetSliderId);
        } else {
            $sliderCollectionTimer->addFieldToFilter('location',$location);
        }
        if($widgetSliderId == 2 ){
       //$sliderCollectionTimer->getSelect()->assemble();
       //$sliderCollectionTimer->getSelect()->__toString();
       //echo $sliderCollectionTimer->getSelect(); die();
        }
        //$this->setSlider($sliderCollection);
        $this->setSlider($sliderCollectionTimer);
    }

    /**
     *  Add child sliders block
     *
     * @param \Ibnab\OwlSlider\Model\ResourceModel\Sliders\Collection $sliderCollection
     *
     * @return $this
     */
    public function setSlider($sliderCollection)
    {

        foreach($sliderCollection as $slider):
            $this->append($this->getLayout()
                                ->createBlock('\Ibnab\OwlSlider\Block\FSlider\Lister')
                                ->setSlider($slider));
        endforeach;

        return $this;
    }

}