<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Block\Adminhtml\Banner\Edit\Tab\Grid;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended as GridExtended;
use Aheadworks\Rbslider\Model\Source\Status;
use Aheadworks\Rbslider\Model\Source\CustomerGroups;
use Aheadworks\Rbslider\Model\ResourceModel\Banner\Collection as BannerCollection;
use Aheadworks\Rbslider\Model\ResourceModel\Slide\Collection as SlideCollection;
use Aheadworks\Rbslider\Block\Adminhtml\Banner\Edit\Tab\Grid\Column\Renderer;
use Aheadworks\Rbslider\Api\Data\BannerInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;
use Aheadworks\Rbslider\Api\BannerRepositoryInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\DataObject\Factory as DataObjectFactory;

/**
 * Class Slide
 * @package Aheadworks\Rbslider\Block\Adminhtml\Banner\Edit\Tab\Grid
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Slide extends GridExtended
{
    /**
     * @var CustomerGroups
     */
    private $customerGroups;

    /**
     * @var Status
     */
    private $status;

    /**
     * @var BannerCollection
     */
    private $bannerCollection;

    /**
     * @var SlideCollection
     */
    private $slideCollection;

    /**
     * @var BannerRepositoryInterface
     */
    private $bannerRepository;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @param Context $context
     * @param Data $backendHelper
     * @param CustomerGroups $customerGroups
     * @param Status $status
     * @param BannerCollection $bannerCollection
     * @param SlideCollection $slideCollection
     * @param BannerRepositoryInterface $bannerRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataPersistorInterface $dataPersistor
     * @param DataObjectFactory $dataObjectFactory
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        CustomerGroups $customerGroups,
        Status $status,
        BannerCollection $bannerCollection,
        SlideCollection $slideCollection,
        BannerRepositoryInterface $bannerRepository,
        DataObjectProcessor $dataObjectProcessor,
        DataPersistorInterface $dataPersistor,
        DataObjectFactory $dataObjectFactory,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->customerGroups = $customerGroups;
        $this->status = $status;
        $this->bannerCollection = $bannerCollection;
        $this->slideCollection = $slideCollection;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->bannerRepository = $bannerRepository;
        $this->dataPersistor = $dataPersistor;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Initialize grid
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('banner_slides');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    /**
     * Retrieve position for selected slides in grid
     *
     * @return array
     */
    public function getSelectedSlidePosition()
    {
        $banner = $this->getBanner();
        $slidePosition = '{}';
        if (isset($banner['slide_position'])) {
            $slidePosition = $banner['slide_position'];
        }
        return $slidePosition;
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('aw_rbslider_admin/*/grid', ['_current' => true]);
    }

    /**
     * {@inheritdoc}
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for slide ids flag
        if ($column->getId() == 'slide_ids') {
            $slidesIds = $this->getSelectedSlides();
            if (empty($slidesIds)) {
                $slidesIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.id', ['in' => $slidesIds]);
            } elseif (!empty($slidesIds)) {
                $this->getCollection()->addFieldToFilter('main_table.id', ['nin' => $slidesIds]);
            }
        } elseif ($column->getId() == 'position') {
            // Fix, if create new banner and apply filter by position
            if ($this->getRequest()->getParam('id')) {
                parent::_addColumnFilterToCollection($column);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $bannerId = $this->getRequest()->getParam('id');
        $collection = $this->slideCollection;
        if ($bannerId) {
            $collection->joinPositions($bannerId);
            $this->setDefaultFilter(['slide_ids' => 1]);
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'slide_ids',
            [
                'type' => 'checkbox',
                'name' => 'slide_ids',
                'values' => $this->getSelectedSlides(),
                'index' => 'id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            ]
        );
        $this->addColumn(
            'id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'type' => 'number'
            ]
        );
        $this->addColumn(
            'thumbnail',
            [
                'header' => __('Thumbnail'),
                'width' => '200',
                'renderer' => Renderer\Thumbnail::class,
                'filter' => false,
                'sortable' => false
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'renderer' => Renderer\SlideName::class
            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type'  => 'options',
                'options' => $this->status->getOptionArray(),
            ]
        );
        $this->addColumn(
            'banner_ids',
            [
                'header' => __('Assigned to Banners'),
                'index' => 'banner_ids',
                'type'  => 'options',
                'options' => $this->bannerCollection->getOptionArray(),
                'filter_condition_callback' => [$this, 'filterBanners'],
                'renderer' => Renderer\Banners::class,
                'sortable' => false
            ]
        );
        $this->addColumn(
            'customer_group_ids',
            [
                'header' => __('Customer Groups'),
                'index' => 'customer_group_ids',
                'type'  => 'options',
                'options' => $this->customerGroups->getOptionArray(),
                'filter_condition_callback' => [$this, 'filterGroups'],
                'sortable' => false
            ]
        );
        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'store_ids',
                [
                    'header'     => __('Store view'),
                    'index'      => 'store_ids',
                    'type'       => 'store',
                    'store_view' => true,
                    'store_all'  => true,
                    'sortable'   => false,
                    'filter_condition_callback' => [$this, 'filterStores'],
                ]
            );
        }
        $this->addColumn(
            'display_from',
            [
                'header' => __('Display From'),
                'index' => 'display_from',
                'type' => 'datetime',
            ]
        );
        $this->addColumn(
            'display_to',
            [
                'header' => __('Display To'),
                'index' => 'display_to',
                'type' => 'datetime',
            ]
        );
        $this->addColumn(
            'position',
            [
                'header' => __('Position'),
                'type' => 'number',
                'index' => 'position',
                'editable' => true,
                'edit_only' => true,
            ]
        );
        return parent::_prepareColumns();
    }

    /**
     * Retrieve selected banner data
     *
     * @return array
     */
    private function getBanner()
    {
        $formData = [];
        if (!empty($this->dataPersistor->get('aw_rbslider_banner'))) {
            $formData = $this->dataObjectFactory->create(
                $this->dataPersistor->get('aw_rbslider_banner')
            );
        } elseif ($id = $this->getRequest()->getParam('id')) {
            $formData = $this->bannerRepository->get($id);
        }
        if ($formData) {
            $formData = $this->dataObjectProcessor->buildOutputDataArray(
                $formData,
                BannerInterface::class
            );
        }
        return $formData;
    }

    /**
     * Retrieve selected slides in grid
     *
     * @return array
     */
    private function getSelectedSlides()
    {
        $slideIds = $this->getRequest()->getPost('selected_slides');
        if (!$slideIds) {
            $slideIds = [];
            $banner = $this->getBanner();
            if (isset($banner['slide_ids'])) {
                $slideIds = $banner['slide_ids'];
            }
        }
        return $slideIds;
    }

    /**
     * Set filter for banners field
     *
     * @param SlideCollection $collection
     * @param Column\Extended $column
     * @return $this
     */
    protected function filterBanners($collection, $column)
    {
        $collection->addFieldToFilter('banner_id', ['eq' => $column->getFilter()->getValue()]);
        return $this;
    }

    /**
     * Set filter for customer group field
     *
     * @param SlideCollection $collection
     * @param Column\Extended $column
     * @return $this
     */
    protected function filterGroups($collection, $column)
    {
        $collection->addFieldToFilter('customer_group_id', ['eq' => $column->getFilter()->getValue()]);
        return $this;
    }

    /**
     * Set filter for store field
     *
     * @param SlideCollection $collection
     * @param Column\Extended $column
     * @return $this
     */
    protected function filterStores($collection, $column)
    {
        $collection->addFieldToFilter('store_id', ['eq' => $column->getFilter()->getValue()]);
        return $this;
    }
}
