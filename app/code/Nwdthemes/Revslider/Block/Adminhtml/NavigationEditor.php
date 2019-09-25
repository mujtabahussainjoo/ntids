<?php

namespace Nwdthemes\Revslider\Block\Adminhtml;

use \Nwdthemes\Revslider\Model\Revslider\RevSliderNavigation;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderOperations;

class NavigationEditor extends \Magento\Backend\Block\Template {

	/**
	 * Constructor
	 */

	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Nwdthemes\Revslider\Helper\Framework $framework,
        \Nwdthemes\Revslider\Helper\Query $query,
        \Nwdthemes\Revslider\Helper\Curl $curl,
        \Nwdthemes\Revslider\Helper\Filesystem $filesystemHelper,
        \Nwdthemes\Revslider\Helper\Images $images,
        \Magento\Framework\App\ResourceConnection $resource,
        \Nwdthemes\Revslider\Model\Revslider\GoogleFonts $googleFonts,
        \Nwdthemes\Revslider\Helper\Register $registerHelper
    ) {
		parent::__construct($context);

		$nav = new RevSliderNavigation($framework, $query);

		$navigation = intval($this->getRequest()->getParam('navigation', 0));

		$navigs = $nav->get_all_navigations();

		$rsopr = new RevSliderOperations($framework, $query, $curl, $filesystemHelper, $images, $resource, $googleFonts, $registerHelper);


		$font_families = $rsopr->getArrFontFamilys();
		
		$this->assign([
			'framework' => $framework,
			'nav' => $nav,
			'navigs' => $navigs,
			'font_families' => $font_families
		]);
	}
}
