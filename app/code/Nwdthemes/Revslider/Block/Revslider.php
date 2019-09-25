<?php

namespace Nwdthemes\Revslider\Block;

use \Nwdthemes\Revslider\Helper\Data;
use \Nwdthemes\Revslider\Helper\Framework;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderOperations;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderOutput;
use \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderCssParser;

class Revslider extends \Magento\Framework\View\Element\Template {

	protected $_framework;
	protected $_status;
	protected $_content;
    protected $_slider;
    protected $_customerGroupId;

	/**
	 * Constructor
	 */

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Nwdthemes\Revslider\Helper\Framework $framework,
		\Nwdthemes\Revslider\Helper\Plugin $pluginHelper,
		\Nwdthemes\Revslider\Model\Revslider\RevSliderFront $revsliderFront,
		array $data = []
	) {
		$this->_framework = $framework;

        parent::__construct($context, $data);

        $this->_customerGroupId = $customerSession->getCustomer()->getGroupId();

        $this->setTemplate('Nwdthemes_Revslider::revslider.phtml');

        $this->_status = $this->_scopeConfig->getValue('nwdthemes_revslider/revslider_configuration/status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($this->_status) {
            $pluginHelper->loadPlugins($framework);
        }
	}

	protected function _renderSlider() {
		if ( is_null($this->_slider) ) {
			ob_start();
			$this->_slider = RevSliderOutput::putSlider($this->getData('alias'));
			$this->_content = ob_get_contents();
			ob_clean();
			ob_end_clean();
		}
	}

    /**
     *  Include scritps and styles
     */

    protected function addHeadIncludes() {

        $this->_renderSlider();
        $this->_framework->do_action('wp_enqueue_scripts');

        $content = $this->_framework->do_action('wp_footer', 'action_no_output');

        foreach ($this->_framework->getRegisteredStyles() as $_style) {
            $content .= '<link  rel="stylesheet" type="text/css"  media="all" href="' . $_style . '" />';
        }

        $content .= $this->_framework->getLocalizeScriptsHtml();

        return $content;
    }

	public function getCacheKeyInfo() {
		$this->_renderSlider();
		$key = parent::getCacheKeyInfo();
		$key[] = $this->getData('alias');
		$key[] = $this->_slider->getParam("disable_on_mobile", "off");
        $key[] = isset($_SERVER['HTTPS']);
        $key[] = $this->_customerGroupId;
		return $key;
	}

	public function renderSlider() {
		if ($this->_status) {

			$this->_renderSlider();

            if(!empty($this->_slider)) {

                // Customer group permissions
                if ($this->_slider->getParam('use_access_permissions', 'off') == 'off' ||
                    in_array($this->_customerGroupId, $this->_slider->getParam('allow_groups', array()))
                ) {

                    $custom_css = RevSliderCssParser::compress_css(RevSliderOperations::getStaticCss());
                    if ($custom_css) {
                        $custom_css = '<style type="text/css">' . $custom_css . '</style>';
                    }

                    $this->_content = $this->addHeadIncludes() . $custom_css . $this->_content;

                    $show_alternate = $this->_slider->getParam("show_alternative_type","off");
                    if($show_alternate == 'mobile' || $show_alternate == 'mobile-ie8'){
                        if(strstr($_SERVER['HTTP_USER_AGENT'],'Android') || strstr($_SERVER['HTTP_USER_AGENT'],'webOS') || strstr($_SERVER['HTTP_USER_AGENT'],'iPhone') ||strstr($_SERVER['HTTP_USER_AGENT'],'iPod') || strstr($_SERVER['HTTP_USER_AGENT'],'iPad') || strstr($_SERVER['HTTP_USER_AGENT'],'Windows Phone') || $this->_framework->wp_is_mobile()){
                            $show_alternate_image = $this->_slider->getParam("show_alternate_image","");
                            $this->_content = '<img class="tp-slider-alternative-image" src="'.$show_alternate_image.'" data-no-retina>';
                        }
                    }

                } else {
                    $this->_content = '';
                }
			}
		}
		return $this->_content;
	}

}
