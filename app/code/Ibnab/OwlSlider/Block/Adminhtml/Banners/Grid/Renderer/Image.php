<?php

namespace Ibnab\OwlSlider\Block\Adminhtml\Banners\Grid\Renderer;

/**
 * Image renderer.
 * @category Magestore
 * @package  Magestore_Bannerslider
 * @module   Bannerslider
 * @author   Magestore Developer
 */
class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    /**
     * @var \Ibnab\OwlSlider\Model\BannersFactory
     */
    protected $bannerFactory;

    protected $_storeManager;
    /**
     *
     * @param \Magento\Backend\Block\Context              $context
     * @param \Magestore\Bannerslider\Model\BannerFactory $bannerFactory
     * @param array                                       $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Ibnab\OwlSlider\Model\BannersFactory $bannerFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
        $this->bannerFactory = $bannerFactory;
    }

    /**
     * Render action.
     *
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $model = $this->bannerFactory->create()->load($row->getId());
        $src = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . $model->getImage();

        return '<image width="180" src ="'.$src.'" alt="'.$model->getImageAlt().'" >';
    }
}
