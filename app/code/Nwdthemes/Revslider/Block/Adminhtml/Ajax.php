<?php

namespace Nwdthemes\Revslider\Block\Adminhtml;

class Ajax extends \Magento\Backend\Block\Template {

	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Nwdthemes\Revslider\Model\Revslider\RevSliderFront $revSliderFront,
        \Nwdthemes\Revslider\Model\Revslider\RevSliderAdmin $revSliderAdmin,
        \Nwdthemes\Revslider\Helper\Framework $frameworkHelper,
        \Nwdthemes\Revslider\Helper\Plugin $pluginHelper
    ) {
        $request = $context->getRequest();

        // temporary force no admin to load plugins as for frontend
        if ($request->getParam('client_action') == 'preview_slider' && $request->getParam('only_markup') == 'true') {
            $frameworkHelper->forceNoAdmin(true);
        }
        $pluginHelper->loadPlugins($frameworkHelper);
        $frameworkHelper->forceNoAdmin(false);

		parent::__construct($context);

        $action = $request->getParam('action');
        if ($action && $action !== 'revslider_ajax_action') {
            echo $frameworkHelper->do_action('wp_ajax_' . $action);
        } else {
            $revSliderAdmin->onAjaxAction();
        }
	}

}