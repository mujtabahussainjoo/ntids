<?php

namespace Nwdthemes\Revslider\Block;

class Head extends \Magento\Framework\View\Element\Template {

	protected $_framework;

	/**
	 * Constructor
	 */

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Nwdthemes\Revslider\Helper\Framework $framework
	) {
		$this->_framework = $framework;
        parent::__construct($context);
	}

    /**
     * Get assets URL
     *
     * return string
     */

	public function getAssetUrl($handle) {
	    return $this->_framework->getAssetUrl($handle);
    }

}
