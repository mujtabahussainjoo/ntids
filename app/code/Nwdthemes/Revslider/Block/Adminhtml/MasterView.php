<?php

namespace Nwdthemes\Revslider\Block\Adminhtml;

use \Nwdthemes\Revslider\Helper\Data;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderGlobals;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderOperations;
use \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderFunctions;
use \Nwdthemes\Revslider\Model\Revslider\Framework\TPColorpicker;

class MasterView extends \Magento\Backend\Block\Template {

	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Nwdthemes\Revslider\Helper\Framework $framework,
		\Nwdthemes\Revslider\Helper\Plugin $pluginHelper,
        \Nwdthemes\Revslider\Helper\Query $query,
        \Nwdthemes\Revslider\Helper\Register $registerHelper,
        \Nwdthemes\Revslider\Helper\Curl $curl,
        \Nwdthemes\Revslider\Helper\Filesystem $filesystemHelper,
        \Nwdthemes\Revslider\Helper\Images $images,
        \Magento\Framework\App\ResourceConnection $resource,
        \Nwdthemes\Revslider\Model\Revslider\GoogleFonts $googleFonts,
        \Nwdthemes\Revslider\Model\Revslider\RevSliderAdmin $revSliderAdmin,
        \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderLoadBalancer $revSliderLoadBalancer
    ) {
		parent::__construct($context);

        $revSliderLoadBalancer->refresh_server_list($context->getRequest()->getParam('rs_refresh_server'));

		$pluginHelper->loadPlugins($framework);

		$revSliderAdmin->onAddScripts();

		$framework->do_action('admin_enqueue_scripts', 'toplevel_page_revslider');

		$revSliderVersion = RevSliderGlobals::SLIDER_REVISION;

		$wrapperClass = "";
		if(RevSliderGlobals::$isNewVersion == false)
			$wrapperClass = " oldwp";

        $wrapperClass = $framework->apply_filters( 'rev_overview_wrapper_class_filter', $wrapperClass );

        $nonce = $framework->wp_create_nonce("revslider_actions");

		$rsop = new RevSliderOperations($framework, $query, $curl, $filesystemHelper, $images, $resource, $googleFonts, $registerHelper);
		$glval = $rsop->getGeneralSettingsValues();

		$waitstyle = '';
		if(isset(Data::$_REQUEST['update_shop'])){
			$waitstyle = 'display:block';
		}

        $operations = new RevSliderOperations($framework, $query, $curl, $filesystemHelper, $images, $resource, $googleFonts, $registerHelper);
        $glob_vals = $operations->getGeneralSettingsValues();
        $pack_page_creation = RevSliderFunctions::getVal($glob_vals, "pack_page_creation", "on");
        $single_page_creation = RevSliderFunctions::getVal($glob_vals, "single_page_creation", "off");
        $tp_color_picker_presets = TPColorpicker::get_color_presets();

		$inlineStyles = $registerHelper->getFromRegister('inline_styles');
        $localizeScriptsHtml = $framework->getLocalizeScriptsHtml();

		$this->assign([
			'framework'             => $framework,
            'revSliderVersion'      => $revSliderVersion,
            'rsop'                  => $rsop,
			'wrapperClass'          => $wrapperClass,
			'nonce'                 => $nonce,
			'glval'                 => $glval,
			'waitstyle'             => $waitstyle,
			'inlineStyles'          => $inlineStyles,
			'localizeScriptsHtml'   => $localizeScriptsHtml,
            'operations'            => $operations,
            'glob_vals'             => $glob_vals,
            'pack_page_creation'    => $pack_page_creation,
            'single_page_creation'  => $single_page_creation,
            'tp_color_picker_presets' => $tp_color_picker_presets
        ]);
	}

}