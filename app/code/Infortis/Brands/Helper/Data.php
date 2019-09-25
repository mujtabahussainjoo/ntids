<?php

namespace Infortis\Brands\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $_configScopeConfigInterface;

    public function __construct(Context $context)
    {    
        $this->_configScopeConfigInterface = $context->getScopeConfig();
        parent::__construct($context);
    }

	/**
	 * Get path of the directory with brand images
	 *
	 * @return string
	 */
	public function getBrandImagePath()
	{
		return 'wysiwyg/infortis/brands/';
	}

	/**
	 * Get module settings
	 *
	 * @return string
	 */
	public function getCfg($optionString)
	{
		return $this->_configScopeConfigInterface->getValue('brands/' . $optionString);
	}

	/**
	 * Get config flag: show brand image
	 *
	 * @return string
	 */
	public function isShowImage()
	{
		return $this->_configScopeConfigInterface->getValue('brands/general/show_image');
		//return $this->getCfg('general/show_image');
	}

	/**
	 * Get config flag: show brand name (simple text) if brand image doesn't exist
	 *
	 * @return string
	 */
	public function isShowImageFallbackToText()
	{
		return $this->_configScopeConfigInterface->getValue('brands/general/show_image_fallback_to_text');
		//return $this->getCfg('general/show_image_fallback_to_text');
	}

	/**
	 * Get config: logo is a link to search results
	 *
	 * @return string
	 */
	public function getCfgLinkToSearch()
	{
		return $this->_configScopeConfigInterface->getValue('brands/general/link_search_enabled');
	}
}
