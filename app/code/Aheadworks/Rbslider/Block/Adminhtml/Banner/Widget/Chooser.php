<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Block\Adminhtml\Banner\Widget;

use Aheadworks\Rbslider\Model\BannerFactory;
use Aheadworks\Rbslider\Model\ResourceModel\Banner\CollectionFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Widget\Block\Adminhtml\Widget\Chooser as BlockWidgetChooser;
use Aheadworks\Rbslider\Model\Source\PageType;
use Aheadworks\Rbslider\Model\Source\Status;

/**
 * Class Chooser
 * @package Aheadworks\Rbslider\Block\Adminhtml\Banner\Widget
 */
class Chooser extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var BannerFactory
     */
    private $bannerFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Status
     */
    private $statusSource;

    /**
     * @param Context $context
     * @param BackendHelper $backendHelper
     * @param BannerFactory $bannerFactory
     * @param CollectionFactory $collectionFactory
     * @param Status $statusSource
     * @param array $data
     */
    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        BannerFactory $bannerFactory,
        CollectionFactory $collectionFactory,
        Status $statusSource,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->bannerFactory = $bannerFactory;
        $this->collectionFactory = $collectionFactory;
        $this->statusSource = $statusSource;
    }

    /**
     * Banner construction, prepare grid params
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setDefaultFilter(['chooser_status' => '1']);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param AbstractElement $element
     * @return AbstractElement
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $uniqId = $this->mathRandom->getUniqueHash($element->getId());
        $sourceUrl = $this->getUrl('aw_rbslider_admin/banner_widget/chooser', ['uniq_id' => $uniqId]);

        $chooser = $this->getLayout()
            ->createBlock(BlockWidgetChooser::class)
            ->setElement($element)
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);

        if ($element->getValue()) {
            $block = $this->bannerFactory->create()->load($element->getValue());
            if ($block->getId()) {
                $chooser->setLabel($this->escapeHtml($block->getTitle()));
            }
        }

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        $chooserJsObject = $this->getId();
        $js = '
            function (grid, event) {
                var trElement = Event.findElement(event, "tr");
                var blockId = trElement.down("td").innerHTML.replace(/^\s+|\s+$/g,"");
                var blockTitle = trElement.down("td").next().innerHTML; '
                . $chooserJsObject . '.setElementValue(blockId); '
                . $chooserJsObject . '.setElementLabel(blockTitle); '
                . $chooserJsObject . '.close();
            }';
        return $js;
    }

    /**
     * Prepare banner blocks collection
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareCollection()
    {
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('page_type', ['eq' => PageType::CUSTOM_WIDGET]);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for banners grid
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'chooser_id',
            [
                'header' => __('ID'),
                'align' => 'right',
                'index' => 'id',
                'width' => 50
            ]
        );
        $this->addColumn(
            'chooser_name',
            [
                'header' => __('Name'),
                'align' => 'left',
                'index' => 'name'
            ]
        );
        $this->addColumn(
            'chooser_status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $this->statusSource->getOptionArray()
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('aw_rbslider_admin/banner_widget/chooser', ['_current' => true]);
    }
}
