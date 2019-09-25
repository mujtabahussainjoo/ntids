<?php

namespace Nwdthemes\Revslider\Block\Adminhtml;

use \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderBase;

class GlobalSettings extends \Magento\Backend\Block\Template {

	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Nwdthemes\Revslider\Helper\Framework $framework,
        \Nwdthemes\Revslider\Model\Revslider\RevSliderOperations $operations
    ) {
		parent::__construct($context);

        $arrValues = $operations->getGeneralSettingsValues();

		$this->assign([
		    'framework'             => $framework,
            'includes_globally'    => RevSliderBase::getVar($arrValues, 'includes_globally', 'on'),
		    'pages_for_includes'   => RevSliderBase::getVar($arrValues, 'pages_for_includes', ''),
		    'show_dev_export'      => RevSliderBase::getVar($arrValues, 'show_dev_export', 'off'),
		    'change_font_loading'  => RevSliderBase::getVar($arrValues, 'change_font_loading', ''),
		    'pack_page_creation'   => RevSliderBase::getVar($arrValues, 'pack_page_creation', 'on'),
		    'single_page_creation' => RevSliderBase::getVar($arrValues, 'single_page_creation', 'off'),
		    'width'                => RevSliderBase::getVar($arrValues, 'width', 1240),
		    'width_notebook'       => RevSliderBase::getVar($arrValues, 'width_notebook', 1024),
		    'width_tablet'         => RevSliderBase::getVar($arrValues, 'width_tablet', 778),
		    'width_mobile'         => RevSliderBase::getVar($arrValues, 'width_mobile', 480),
		    'load_google_fonts'    => RevSliderBase::getVar($arrValues, 'load_google_fonts', 'off')
		]);
	}
}
