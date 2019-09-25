<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2015 ThemePunch
 */

namespace Nwdthemes\Revslider\Model\Revslider\Framework;

class RevSliderBaseFront extends RevSliderBase {		

	const ACTION_ENQUEUE_SCRIPTS = "wp_enqueue_scripts";
	
	/**
	 * 
	 * main constructor		 
	 */

	public function __construct(
        $t,
        \Nwdthemes\Revslider\Helper\Filesystem $filesystemHelper,
        \Nwdthemes\Revslider\Helper\Framework $framework,
        \Nwdthemes\Revslider\Helper\Query $query,
        \Nwdthemes\Revslider\Helper\Images $images,
        \Magento\Backend\Model\UrlInterface $backendUrl
    ) {
		parent::__construct($t, $filesystemHelper, $framework, $query, $images, $backendUrl);
		
		$framework->add_action('wp_enqueue_scripts', array('\Nwdthemes\Revslider\Model\Revslider\RevSliderFront', 'onAddScripts'));
	}	
	
}
