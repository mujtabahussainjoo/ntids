<?php

namespace Nwdthemes\Revslider\Block\Adminhtml;

class AddonAjax extends \Magento\Backend\Block\Template {

	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Nwdthemes\Revslider\Model\Revslider\RevSliderFront $revSliderFront,
        \Nwdthemes\Revslider\Model\Revslider\RevSliderAdmin $revSliderAdmin,
        \Nwdthemes\Revslider\Helper\Framework $frameworkHelper
	) {
		parent::__construct($context);
        $action = 'wp_ajax_' . $context->getRequest()->getParam('action');
        echo $frameworkHelper->do_action($action);
	}

}
