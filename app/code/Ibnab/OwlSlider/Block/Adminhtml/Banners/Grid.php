<?php

namespace Ibnab\OwlSlider\Block\Adminhtml\Banners;

use Ibnab\OwlSlider\Model\Source\Status;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended {

	/**
	 * banner factory
	 * @var \Ibnab\MageUnslider\Model\BannersFactory
	 */
	protected $_bannerFactory;
	/**
	 * bannerslider factory
	 * @var \Ibnab\OwlSlider\Model\BannerSliderFactor
	 */
	protected $_bannersliderFactory;
	/**
	 * Registry object
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry;

	/**
	 * @param \Magento\Backend\Block\Template\Context     $context            
	 * @param \Magento\Backend\Helper\Data                $backendHelper      
	 * @param \Ibnab\OwlSlider\Model\BannersFactory   bannerFactory   
         * @param \Ibnab\OwlSlider\Model\BannerSliderFactory $bannersliderFactory   
	 * @param \Magento\Framework\Registry                 $coreRegistry       
	 * @param array                                       $data               
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Backend\Helper\Data $backendHelper,
		\Ibnab\OwlSlider\Model\BannersFactory $bannerFactory,
                \Ibnab\OwlSlider\Model\BannerSliderFactory $bannersliderFactory,
		\Magento\Framework\Registry $coreRegistry,
		array $data = []
	) {
		$this->_bannerFactory = $bannerFactory;
                $this->_bannersliderFactory = $bannersliderFactory;
		$this->_coreRegistry = $coreRegistry;
		parent::__construct($context, $backendHelper, $data);
	}

       /**
       * @return void
       */
	protected function _construct() {
		parent::_construct();
		$this->setId('BannersSliderGrid');
		$this->setDefaultSort('banner_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
                if ($this->getRequest()->getParam('slider_id')) {
                   $this->setDefaultFilter(['in_banners' => 1]);
                }
	}

      /**
      * @return $this
      */
	protected function _prepareCollection() {
		$collection = $this->_bannerFactory->create()->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	/**
	 * @return $this
	 */
	protected function _prepareColumns() {
         $this->addColumn(
            'in_banners',
            [
                'type' => 'checkbox',
                'name' => 'in_banners',
                'align' => 'center',
                'header_css_class' => 'col-select',
                'column_css_class' => 'col-select',
                'index' => 'banner_id',
                'values' => $this->_getSelectedBanners(),
            ]
         );
		$this->addColumn(
			'banner_id',
			[
				'header' => __('Banner ID'),
				'type' => 'number',
				'index' => 'banner_id',
				'header_css_class' => 'col-id',
				'column_css_class' => 'col-id',
			]
		);
		$this->addColumn(
			'name',
			[
				'header' => __('Name'),
				'index' => 'name',
				'class' => 'xxx',
				'width' => '50px',
			]
		);
		$this->addColumn(
			'image',
			[
				'header' => __('Thumbnail'),
				'index' => 'image',
				'class' => 'xxx',
                                'align' => 'center',
                                'renderer' => 'Ibnab\OwlSlider\Block\Adminhtml\Banners\Grid\Renderer\Image',
			]
		);
                $this->addColumn(
                  'order_banners',
                   [
                     'header' => __('Order'),
                     'name' => 'order_banners',
                     'index' => 'order_banners',
                     'class' => 'xxx',
                     'width' => '50px',
                     'editable' => true,
                   ]
                );
		$this->addColumn(
			'edit',
			[
				'header' => __('Edit'),
				'type' => 'action',
				'getter' => 'getId',
				'actions' => [
					[
						'caption' => __('Edit'),
						'url' => [
							'base' => '*/*/edit',
						],
						'field' => 'banner_id',
					],
				],
				'filter' => false,
				'sortable' => false,
				'index' => 'stores',
				'header_css_class' => 'col-action',
				'column_css_class' => 'col-action',
			]
		);

		return parent::_prepareColumns();
	}
    
    /**
     * add Column Filter To Collection
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in slider flag
        if ($column->getId() == 'in_banners') {
            $banner_ids = $this->_getSelectedBanners();
            if (empty($banner_ids)) {
                $banner_ids = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('banner_id', array('in' => $banner_ids));
            } else {
                if ($banner_ids) {
                    $this->getCollection()->addFieldToFilter('banner_id', array('nin' => $banner_ids));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }
        protected function _getSelectedBanners()
        {
            $banners = $this->getBannersSlider();
            if (!is_array($banners)) {
                $banners = array_keys($this->getSelectedSliderBanners());
            }
            return $banners;
        }
        public function getSelectedSliderBanners()
        {
           $bannersliderFactory = $this->_bannersliderFactory->create();
           $banners = [];
           $slider_id = $this->getRequest()->getParam('slider_id');
           if (!isset($slider_id)) {
            return [];
           }
           $bannersliderSelectedFactory = $bannersliderFactory->getSelectedSliderBanners($slider_id);
           foreach ($bannersliderSelectedFactory as $banner) {   
               if(!isset($banners[$banner->getId()]))           
               $banners[$banner->getId()] = ['order_banners' => $banner->getOrder()];
            }
            return $banners;
        }
	/**
	 * @return string
	 */
	public function getGridUrl() {
	return $this->getUrl('ibnabowlslider/*/bannersgrid', array('_current' => true));
	}
	public function getRowUrl($row) {
		return '';
	}
	
    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return true;
    }
}
