<?php
namespace Ibnab\OwlSlider\Model;

class Sliders extends \Magento\Framework\Model\AbstractModel
{

     const SLIDER_ID  = 'slider_id';
    /**#@+
     * Slider's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    
    protected $paramsOwl = ['items','itemsDesktop','itemsTablet','itemsMobile','singleItem','margin','loop','center',
                            'mouseDrag','touchDrag','pullDrag','freeDrag','stagePadding','merge','mergeFit','autoWidth','startPosition','URLhashListener',
                            'nav','navRewind','navText','slideBy','dots','dotsEach','dotData','lazyLoad','autoplay','autoplayTimeout','autoplayHoverPause','smartSpeed',
                            'autoplaySpeed','navSpeed','dotsSpeed','dragEndSpeed','responsive','responsiveRefreshRate','video','videoHeight','videoWidth','animateOut','animateIn','keys'];
    protected $allBool = ['loop','center','mouseDrag','touchDrag','pullDrag','freeDrag','merge','mergeFit','autoWidth','URLhashListener',
                          'nav','navRewind','dots','dotsEach','dotData','lazyLoad','autoplay','autoplayHoverPause','autoplaySpeed','navSpeed','dotsSpeed','dragEndSpeed','video','animateOut','animateIn'];
    protected $responsive = ['itemsDesktop','itemsTablet','itemsMobile','singleItem',"responsive"];   
    /**
    /**
     * Slider types
     */
    const SLIDER_TYPE_DEFAULT = "";
    const SLIDER_TYPE_NEW = 'new';
    const SLIDER_TYPE_BESTSELLERS = 'bestsellers';
    const SLIDER_TYPE_MOSTVIEWED = 'mostviewed';
    const SLIDER_TYPE_ONSALE = 'onsale';
    const SLIDER_TYPE_FEATURED = 'featured';
    const SLIDER_TYPE_AUTORELATED = 'autorelated';
    const SLIDER_TYPE_BANNER = 'banners';
    const SLIDER_TYPE_WBOUGTH = 'whobought';
    const SLIDER_HOT_DEAL = 'hdeal';
    /**
     * Slider locations/positions displayed on frontend
     */
    const SLIDER_LOCATION_DEFAULT = "";
    const HOMEPAGE_CONTENT_TOP = 'homepage-content-top';
    const HOMEPAGE_CONTENT_BOTTOM = 'homepage-content-bottom';
    const CONTENT_TOP = 'content-top';
    const CONTENT_BOTTOM = 'content-bottom';
    const SIDEBAR_ADDITIONAL_TOP = 'sidebar-additional-top';
    const SIDEBAR_ADDITIONAL_BOTTOM = 'sidebar-additional-bottom';
    const CATEGORY_CONTENT_TOP = 'category-content-top';
    const CATEGORY_CONTENT_BOTTOM = 'category-content-bottom';
    const CATEGORY_SIDEBAR_ADDITIONAL_TOP = 'category-sidebar-additional-top';
    const CATEGORY_SIDEBAR_ADDITIONAL_BOTTOM = 'category-sidebar-additional-bottom';
    const PRODUCT_CONTENT_TOP = 'product-content-top';
    const PRODUCT_CONTENT_BOTTOM = 'product-content-bottom';
    const PRODUCT_SIDEBAR_ADDITIONAL_TOP = 'product-sidebar-additional-top';
    const PRODUCT_SIDEBAR_ADDITIONAL_BOTTOM = 'product-sidebar-additional-bottom';
    const CART_CONTENT_TOP = 'cart-content-top';
    const CART_CONTENT_BOTTOM = 'cart-content-bottom';
    const CHECKOUT_CONTENT_TOP = 'checkout-content-top';
    const CHECKOUT_CONTENT_BOTTOM = 'checkout-content-bottom';
    const CUSTOMER_CONTENT_TOP = 'customer-content-top';
    const CUSTOMER_CONTENT_BOTTOM = 'customer-content-bottom';
    const CUSTOMER_SIDEBAR_ADDITIONAL_TOP = 'customer-sidebar-additional-top';
    const CUSTOMER_SIDEBAR_ADDITIONAL_BOTTOM = 'customer-sidebar-additional-bottom';
 
    /**
     * Slider types
     *
     * @var array
     */
    protected static $sliderType = [
        self::SLIDER_TYPE_DEFAULT => '--- Select Slider Type --',
        self::SLIDER_TYPE_BANNER  => 'Banners',
        self::SLIDER_TYPE_NEW => 'New Products',
        self::SLIDER_TYPE_BESTSELLERS => 'Bestselers Products',
        self::SLIDER_TYPE_MOSTVIEWED => 'Most Viewed Products',
        self::SLIDER_TYPE_ONSALE => 'On Sale Products',
        self::SLIDER_TYPE_FEATURED => 'Featured Products',
        self::SLIDER_TYPE_AUTORELATED => 'Auto Related Products',
        self::SLIDER_HOT_DEAL => 'Hot Deal'
    ];
    protected $_moduleManager;
    
    protected function _construct()
    {
        $this->_init('Ibnab\OwlSlider\Model\ResourceModel\Sliders');
    }
    /**
     * Initialize resource model
     *
     * @return void
     */
    public function __construct(\Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = [])
    {
        $this->_moduleManager = $moduleManager;
        $this->_construct();
        parent::__construct($context,$registry,$resource,$resourceCollection,$data);
        
    }
    /**
     * Prepare statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }
    /**
     * Prepare statuses.
     *
     * @return array
     */
    public function getAllParamsOwl()
    {
        $allparams = '';
        $sliderData = null;
        $sliderData = $this->getData();
        foreach($this->paramsOwl as $param){     
            if(!in_array($param,$this->responsive)){
            if(isset($sliderData[$param]))
            {
            if(!is_numeric($sliderData[$param])):
            $allparams.="$param".": ".$sliderData[$param].",";
            else :
            if(in_array($param,$this->allBool)):
                if($sliderData[$param]==1):
                   $allparams.="$param".": true,"; 
                else:
                   $allparams.="$param".": false,";
                endif;
            else:
            $allparams.="$param".": ".$sliderData[$param].","; 
            endif;
            endif;
            }
            }
            
        }
        $valueParam = null;
        if(isset($sliderData['singleItem']) && isset($sliderData['responsive'])):
            if($sliderData['responsive']==1):
            if($sliderData['singleItem']==1):
                $valueParam= "responsive:{
                    0:{
                       items:1,
                      },
                    600:{
                      items:1,
                    },
                    800:{
                      items:1,
                    },
                    1000:{
                      items:1,
                    }
                }";
            else:
                $itemDesktop = isset($sliderData['itemsDesktop']) ?  $sliderData['itemsDesktop'] : 5;
                $itemsDesktopSmall = isset($sliderData['itemsDesktopSmall']) ?  $sliderData['itemsDesktopSmall'] : 4;
                $itemTablet = isset($sliderData['itemsTablet']) ?  $sliderData['itemsTablet'] : 2;
                $itemMobile = isset($sliderData['itemsMobile']) ?  $sliderData['itemsMobile'] : 1;
                
                $valueParam= "responsive:{
                    0:{
                       items:".$itemMobile.",
                      },
                    600:{
                      items:".$itemTablet.",
                    },
                    800:{
                      items:".$itemsDesktopSmall.",
                    },
                    1000:{
                      items:".$itemDesktop.",
                    }
                },";                
            endif;
            $allparams.=$valueParam; 
            else:
            $allparams.="responsive".": false,";
            endif;
        endif;
        return $allparams;
    }
    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::SLIDER_ID);
    }
    public function getBannersId()
    {
       return $this->hasData('banners_id') ? $this->getData('banners_id') : null;
    }
    /**
     * Slider types
     * @return array
     */
    public function getSliderType()
    {
      $sliderType = [
        self::SLIDER_TYPE_DEFAULT => '--- Select Slider Type --',
        self::SLIDER_TYPE_BANNER  => 'Banners',
        self::SLIDER_TYPE_NEW => 'New Products',
        self::SLIDER_TYPE_BESTSELLERS => 'Bestselers Products',
        self::SLIDER_TYPE_MOSTVIEWED => 'Most Viewed Products',
        self::SLIDER_TYPE_ONSALE => 'On Sale Products',
        self::SLIDER_TYPE_FEATURED => 'Featured Products',
        self::SLIDER_TYPE_AUTORELATED => 'Auto Related Products',
        self::SLIDER_HOT_DEAL => 'Hot Deal'
     ];
        $sliderTypeResult = $sliderType;
        if ($this->_moduleManager->isEnabled('Ibnab_WBought')) {
             $sliderTypeResult [self::SLIDER_TYPE_WBOUGTH] = 'Who Bought Also Bought';
        }
        return $sliderTypeResult;
    }
    /**
     * Not formatted slider locations
     * @return array
     */
    public static function getSliderGridLocations()
    {
        return [
            self::SLIDER_LOCATION_DEFAULT => '--- No Location ---',
            self::HOMEPAGE_CONTENT_TOP => __('Homepage Content Top'),
            self::HOMEPAGE_CONTENT_BOTTOM => __('Homepage Content Bottom'),
            self::CONTENT_TOP => __('Content Top'),
            self::CONTENT_BOTTOM => __('Content Bottom'),
            self::SIDEBAR_ADDITIONAL_TOP => __('Sidebar Additional Top'),
            self::SIDEBAR_ADDITIONAL_BOTTOM => __('Sidebar Additional Bottom'),
            self::CATEGORY_CONTENT_TOP => __('Category Content Top'),
            self::CATEGORY_CONTENT_BOTTOM => __('Category Content Bottom'),
            self::CATEGORY_SIDEBAR_ADDITIONAL_TOP => __('Category Sidebar Additional Top'),
            self::CATEGORY_SIDEBAR_ADDITIONAL_BOTTOM => __('Category Sidebar Additional Bottom'),
            self::PRODUCT_CONTENT_TOP => __('Product Content Top'),
            self::PRODUCT_CONTENT_BOTTOM => __('Product Content Bottom'),
            self::PRODUCT_SIDEBAR_ADDITIONAL_TOP => __('Product Sidebar Additional Top'),
            self::PRODUCT_SIDEBAR_ADDITIONAL_BOTTOM => __('Product Sidebar Additional Bottom'),
            self::CART_CONTENT_TOP => __('Cart Content Top'),
            self::CART_CONTENT_BOTTOM => __('Cart Content Bottom'),
            self::CHECKOUT_CONTENT_TOP => __('Checkout Content Top'),
            self::CHECKOUT_CONTENT_BOTTOM => __('Checkout Content Bottom'),
            self::CUSTOMER_CONTENT_TOP => __('Customer Content Top'),
            self::CUSTOMER_CONTENT_BOTTOM => __('Customer Content Bottom'),
            self::CUSTOMER_SIDEBAR_ADDITIONAL_TOP => __('Customer Sidebar Additional Top'),
            self::CUSTOMER_SIDEBAR_ADDITIONAL_BOTTOM => __('Customer Sidebar Additional Bottom'),
        ];
    }
    /**
     * Not formatted slider locations
     * @return array
     */
    public static function getSliderImageView()
    {
        $imageView = [];
        $imageView[''] = __('Default');
        $doc = new \DOMDocument();
        $doc->load(__DIR__ . '/../etc/view.xml');
        $images = $doc->getElementsByTagName('image');
       for($i = 0; $i < count($images) ; $i++) {
             $imageView[$images[$i]->getAttribute('id')] = $images[$i]->getAttribute('id');
       }

       return array_unique($imageView);
    }
    /**
     * Formatted slider locations
     * @return array
     */
    public static function getSliderLocations()
    {
        return [
            ['label' => __('--- Select Slider Location --') , 'value' => self::SLIDER_LOCATION_DEFAULT ],
            ['label' => __('Home page'), 'value' => [
                    ['label' => __('Homepage Content Top'), 'value' => self::HOMEPAGE_CONTENT_TOP],
                    ['label' => __('Homepage Content Bottom'), 'value' => self::HOMEPAGE_CONTENT_BOTTOM],
                ]
            ],
            ['label' => __('Display on all pages'), 'value' =>  [
                    ['label' => __('Content Top'), 'value' => self::CONTENT_TOP],
                    ['label'  => __('Content Bottom'), 'value' => self::CONTENT_BOTTOM],
                    ['label'  => __('Sidebar Additional Top'), 'value' => self::SIDEBAR_ADDITIONAL_TOP],
                    ['label'  => __('Sidebar Additional Bottom'), 'value' => self::SIDEBAR_ADDITIONAL_BOTTOM],
                ]
            ],
            ['label' => __('Category page'), 'value' => [
                    ['label' => __('Category Content Top'), 'value' => self::CATEGORY_CONTENT_TOP],
                    ['label' => __('Category Content Bottom'), 'value' => self::CATEGORY_CONTENT_BOTTOM],
                    ['label' => __('Category Sidebar Additional Top'), 'value' => self::CATEGORY_SIDEBAR_ADDITIONAL_TOP],
                    ['label' => __('Category Sidebar Additional Bottom'), 'value' => self::CATEGORY_SIDEBAR_ADDITIONAL_BOTTOM],
                ]
            ],
            ['label' => __('Product page'), 'value' => [
                    ['label' => __('Product Content Top'), 'value' => self::PRODUCT_CONTENT_TOP],
                    ['label' => __('Product Content Bottom'), 'value' => self::PRODUCT_CONTENT_BOTTOM],
                    ['label' => __('Product Sidebar Additional Top'), 'value' => self::PRODUCT_SIDEBAR_ADDITIONAL_TOP],
                    ['label' => __('Product Sidebar Additional Bottom'), 'value' => self::PRODUCT_SIDEBAR_ADDITIONAL_BOTTOM],
                ]
            ],
            ['label' => __('Cart, checkout and customer page'), 'value' => [
                    ['label' => __('Cart Content Top'), 'value' => self::CART_CONTENT_TOP],
                    ['label' => __('Cart Content Bottom'), 'value' => self::CART_CONTENT_BOTTOM],
                    ['label' => __('Checkout Content Top'), 'value' => self::CHECKOUT_CONTENT_TOP],
                    ['label' => __('Checkout Content Bottom'), 'value' => self::CHECKOUT_CONTENT_BOTTOM],
                ]
            ],
            ['label' => __('Customer page'), 'value' => [
                    ['label' => __('Customer Content Top'), 'value' => self::CUSTOMER_CONTENT_TOP],
                    ['label' => __('Customer Content Bottom'), 'value' => self::CUSTOMER_CONTENT_BOTTOM],
                    ['label' => __('Customer Sidebar Additional Top'), 'value' => self::CUSTOMER_SIDEBAR_ADDITIONAL_TOP],
                    ['label' => __('Customer Sidebar Additional Bottom'), 'value' => self::CUSTOMER_SIDEBAR_ADDITIONAL_BOTTOM],
                ]
            ],
        ];
    }
}
