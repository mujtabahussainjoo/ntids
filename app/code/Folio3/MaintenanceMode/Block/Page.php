<?php

namespace Folio3\MaintenanceMode\Block;

use Magento\Framework\View\Element\Template;

class Page extends Template {

    protected $_helper;
    protected $_pageBlock;

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Folio3\MaintenanceMode\Helper\Data $helper, \Magento\Theme\Model\Favicon\Favicon $faviconInterface, array $data = []
    ) {
        $this->_helper = $helper;
        $this->_pageBlock = null;
        $this->_faviconInterface = $faviconInterface;

        parent::__construct($context, $data);
    }

    /**
     * Create a Maintenance Mode block and add it in the layout
     *
     * @return \Magento\Cms\Block\Block
     */
    protected function _loadBlock() {
        if (!is_null($this->_pageBlock))
            return $this->_pageBlock;
        $staticBlockId = $this->_helper->getConfig('MaintenanceMode/Configuration/pageStaticBlock');
        $pageContentBlock = $this->getLayout()->createBlock('Magento\Cms\Block\Block')->setBlockId($staticBlockId);

        $this->append($pageContentBlock);
        return $pageContentBlock;
    }

    /**
     * Get the static page name in layout
     *
     * @return string
     */
    public function getStaticPageIdentifier() {
        $pageBlock = $this->_loadBlock();
        return $pageBlock->getNameInLayout();
    }

    /**
     * Get the static page name in layout
     *
     * @return string
     */
    public function getSubsciptionLinks() {
        $links = array();
        if ($this->_helper->getConfig('MaintenanceMode/social_media/linkedin')) {
            $links['linkedin'] = $this->_helper->getConfig('MaintenanceMode/social_media/linkedin');
        }
        if ($this->_helper->getConfig('MaintenanceMode/social_media/pinterest')) {
            $links['pinterest'] = $this->_helper->getConfig('MaintenanceMode/social_media/pinterest');
        }
        if ($this->_helper->getConfig('MaintenanceMode/social_media/twitter')) {
            $links['twitter'] = $this->_helper->getConfig('MaintenanceMode/social_media/twitter');
        }
        if ($this->_helper->getConfig('MaintenanceMode/social_media/facebook')) {
            $links['facebook'] = $this->_helper->getConfig('MaintenanceMode/social_media/facebook');
        }
        if ($this->_helper->getConfig('MaintenanceMode/social_media/picasa')) {
            $links['picasa'] = $this->_helper->getConfig('MaintenanceMode/social_media/picasa');
        }
        if ($this->_helper->getConfig('MaintenanceMode/social_media/rss')) {
            $links['rss'] = $this->_helper->getConfig('MaintenanceMode/social_media/rss');
        }
        if ($this->_helper->getConfig('MaintenanceMode/social_media/vimeo')) {
            $links['vimeo'] = $this->_helper->getConfig('MaintenanceMode/social_media/vimeo');
        }
        return $links;
    }

    /**
     * Get the helper object
     *
     * @return \Folio3\MaintenanceMode\Helper\Data
     */
    public function getHelper() {
        return $this->_helper;
    }

    /**
     * Get the asset (js/css) URL.
     *
     * @param @asset string
     * @return string
     */
    public function getAsset($asset) {
        return $this->_assetRepo->createAsset('Folio3_MaintenanceMode::' . $asset, array('theme' => 'Magento/blank'))->getUrl();
    }

    /**
     * Get the asset (js/css) URL.
     *
     * @param @asset string
     * @return string
     */
    public function getBackgroundImage($asset) {
        $image_config = $this->_scopeConfig->getValue(
            'MaintenanceMode/Configuration/custom_file_upload', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (empty($image_config)) {
            $image = $this->_assetRepo->createAsset('Folio3_MaintenanceMode::' . $asset, array('theme' => 'Magento/blank'))->getUrl();
        }
        else {
            $image = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'maintenance_mode/' . $image_config;
        }
        return $image;
    }

    /**
     * Get current store name.
     *
     * @return string
     */
    public function getHeaderTitle() {
        $header_type = $this->_scopeConfig->getValue(
            'MaintenanceMode/header/headerType', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        switch ($header_type) {
            case 1:
                return $this->getStoreName();
            case 2:
                $logo_alt_text = $header_type = $this->_scopeConfig->getValue(
                    'design/header/logo_alt', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
                return '<img src="' . $this->getStoreLogo() . '" alt="' . $logo_alt_text . '" style="max-height: 125px; max-width: 200px;">';
                break;
            default:
                return NULL;
        }
    }

    /**
     * Get current store name.
     *
     * @return string
     */
    public function getStoreName() {
        $store_name_type = $this->_scopeConfig->getValue(
            'MaintenanceMode/header/useConfigStoreName', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        switch ($store_name_type) {
            case 'custom':
                $storeName = $this->_scopeConfig->getValue(
                    'MaintenanceMode/header/storeName', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
                break;
            default:
                $storeName = $this->_scopeConfig->getValue(
                    'general/store_information/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
                break;
        }
        return $storeName;
    }

    /**
     * Get current store name.
     *
     * @return string
     */
    public function getStoreLogo() {
        $store_logo_type = $this->_scopeConfig->getValue(
            'MaintenanceMode/header/useConfigStoreLogo', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        switch ($store_logo_type) {
            case 'custom':
                $LogoFileName = $this->_scopeConfig->getValue(
                    'MaintenanceMode/header/storeLogo', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
                $storeLogo = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'maintenance_mode/' . $LogoFileName;
                break;
            default:
                $LogoFileName = $this->_scopeConfig->getValue(
                    'design/header/logo_src', \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
                $storeLogo = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'logo/' . $LogoFileName;
                break;
        }
        return $storeLogo;
    }

    /**
     * Get title of the selected static block
     *
     * @return string
     */
    public function getStaticBlockTitle() {
        $staticBlockIdentifier = $this->getStaticPageIdentifier();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $block = $objectManager->get('Magento\Cms\Model\Block')->load($this->getChildBlock($staticBlockIdentifier)->getBlockId());

        return $block->getTitle();
    }

    /**
     * Get the Favicon URL
     *
     * @return string
     */
    public function getFavicon() {
        $repo = $this->_assetRepo->createAsset($this->_faviconInterface->getDefaultFavicon(), array('area' => 'adminhtml', 'theme' => 'Magento/backend'));
        return $repo->getUrl();
    }

}
