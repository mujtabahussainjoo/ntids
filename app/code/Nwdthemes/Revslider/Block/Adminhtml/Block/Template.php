<?php

namespace Nwdthemes\Revslider\Block\Adminhtml\Block;

class Template extends \Magento\Backend\Block\Template {

    protected $_framework;

	/**
	 *	Constructor
	 */

	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Nwdthemes\Revslider\Helper\Data $dataHelper,
        \Nwdthemes\Revslider\Helper\Framework $framework,
        \Nwdthemes\Revslider\Helper\Query $query,
        \Nwdthemes\Revslider\Helper\Curl $curl,
        \Nwdthemes\Revslider\Helper\Filesystem $filesystemHelper,
        \Nwdthemes\Revslider\Helper\Images $images,
        \Magento\Framework\App\ResourceConnection $resource,
        \Nwdthemes\Revslider\Model\Revslider\GoogleFonts $googleFonts,
        \Nwdthemes\Revslider\Helper\Register $registerHelper
    ) {
        $this->_framework = $framework;

        parent::__construct($context);

        $this->assign([
			'framework' => $framework,
			'query' => $query,
			'curl' => $curl,
			'filesystemHelper' => $filesystemHelper,
			'images' => $images,
			'resource' => $resource,
			'googleFonts' => $googleFonts,
            'dataHelper' => $dataHelper,
            'registerHelper' => $registerHelper
		]);
	}
	
	/**
	 *	Get admin url
	 *
	 *	@param	string	url
	 *	@param	string	args
	 *	@return	string
	 */	

	public function getViewUrl($url, $args = '') {
		return $this->_framework->getBackendUrl($url, $args);
	}

}
