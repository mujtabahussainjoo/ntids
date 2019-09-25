<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2015 ThemePunch
 */

namespace Nwdthemes\Revslider\Model\Revslider\Framework;

use \Nwdthemes\Revslider\Helper\Data;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderGlobals;

class RevSliderUpdate {

    protected $_framework;
    protected $_revSliderLoadBalancer;

	private $plugin_url			= 'https://codecanyon.net/item/slider-revolution-responsive-magento-extension/9332896';
    private $remote_url         = 'check_for_updates.php';
    private $remote_url_info    = 'revslider-magento/revslider-magento.php';
    private $remote_temp_active = 'temp_activate.php';
	private $plugin_slug		= RevSliderGlobals::PLUGIN_SLUG;
	private $plugin_path		= 'revslider/revslider.php';
	private $version;
	private $option;


	public function __construct(
        $version,
        \Nwdthemes\Revslider\Helper\Framework $framework
    ) {
        $this->_framework = $framework;
        $this->_revSliderLoadBalancer = new RevSliderLoadBalancer($this->_framework);

		$this->option = $this->plugin_slug . '_update_info';
		$this->_retrieve_version_info();
		$this->version = $version;
	}

	public function add_update_checks(){

		$this->_framework->add_filter('pre_set_site_transient_update_plugins', array(&$this, 'set_update_transient'));
		$this->_framework->add_filter('plugins_api', array(&$this, 'set_updates_api_results'), 10, 3);

	}

	public function set_update_transient($transient) {

		$this->_check_updates();

		if(isset($transient) && !isset($transient->response)) {
			$transient->response = array();
		}

		if(!empty($this->data->basic) && is_object($this->data->basic)) {
			if(version_compare($this->version, $this->data->basic->version, '<')) {

				$this->data->basic->new_version = $this->data->basic->version;
				$transient->response[$this->plugin_path] = $this->data->basic;
			}
		}

		return $transient;
	}


	public function set_updates_api_results($result, $action, $args) {

		$this->_check_updates();

		if(isset($args->slug) && $args->slug == $this->plugin_slug && $action == 'plugin_information') {
			if(is_object($this->data->full) && !empty($this->data->full)) {
				$result = $this->data->full;
			}
		}

		return $result;
	}


	protected function _check_updates() {
		//reset saved options
		//$this->_framework->update_option($this->option, false);

		$force_check = false;

		if(isset(Data::$_GET['checkforupdates']) && Data::$_GET['checkforupdates'] == 'true') $force_check = true;

		// Get data
		if(empty($this->data)) {
			$data = $this->_framework->get_option($this->option, false);
			$data = $data ? $data : new \stdClass;

			$this->data = is_object($data) ? $data : $this->_framework->maybe_unserialize($data);

		}

		$last_check = $this->_framework->get_option('revslider-update-check');
		if($last_check == false){ //first time called
			$last_check = time();
			$this->_framework->update_option('revslider-update-check', $last_check);
		}

		// Check for updates
		if(time() - $last_check > 172800 || $force_check == true){

			$data = $this->_retrieve_update_info();

			if(isset($data->basic)) {
				$this->_framework->update_option('revslider-update-check', time());

				$this->data->checked = time();
				$this->data->basic = $data->basic;
				$this->data->full = $data->full;

				$this->_framework->update_option('revslider-stable-version', $data->full->stable);
				$this->_framework->update_option('revslider-latest-version', $data->full->version);
			}

		}

		// Save results
		$this->_framework->update_option($this->option, $this->data);
	}


	public function _retrieve_update_info() {

		$data = new \stdClass;

		// Build request
		$code = $this->_framework->get_option('revslider-code', '');

		$validated = $this->_framework->get_option('revslider-valid', 'false');
		$stable_version = $this->_framework->get_option('revslider-stable-version', '4.2');

		$rattr = array(
			'code' => urlencode($code),
			'version' => urlencode(RevSliderGlobals::SLIDER_REVISION)
		);

		if($validated !== 'true' && version_compare(RevSliderGlobals::SLIDER_REVISION, $stable_version, '<')){ //We'll get the last stable only now!
			$rattr['get_stable'] = 'true';
		}

        $done = false;
        $count = 0;
        do {
            $url = $this->_revSliderLoadBalancer->get_url('updates');
            $request = $this->_framework->wp_remote_post($url . '/' . $this->remote_url_info, array(
                'body' => $rattr
            ));

            $response_code = $this->_framework->wp_remote_retrieve_response_code($request);
            if ($response_code == 200) {
                $done = true;
            } else {
                $this->_revSliderLoadBalancer->move_server_list();
            }

            $count++;
        } while ($done == false && $count < 5);

        if(!$this->_framework->is_wp_error($request)) {
			if($response = $this->_framework->maybe_unserialize($request['body'])) {
				if(is_object($response)) {
					$data = $response;

					$data->basic->url = $this->plugin_url;
					$data->full->url = $this->plugin_url;
					$data->full->external = 1;
				}
			}
		}

		return $data;
	}


	public function _retrieve_version_info($force_check = false) {

		$last_check = $this->_framework->get_option('revslider-update-check-short');
		if($last_check == false){ //first time called
			$last_check = time();
			$this->_framework->update_option('revslider-update-check-short', $last_check);
		}


		// Check for updates
		if(time() - $last_check > 172800 || $force_check == true){

			$this->_framework->update_option('revslider-update-check-short', time());

            $purchase = ($this->_framework->get_option('revslider-valid', 'false') == 'true') ? $this->_framework->get_option('revslider-code', '') : '';

            $done = false;
            $count = 0;
            do {
                $url = $this->_revSliderLoadBalancer->get_url('updates');
                $response = $this->_framework->wp_remote_post($url . '/' . $this->remote_url, array(
                    'body' => array(
                        'item' => urlencode(RevSliderGlobals::PLUGIN_SLUG),
                        'version' => urlencode(RevSliderGlobals::SLIDER_REVISION),
                        'code' => urlencode($purchase)
                    ),
                    'timeout' => 45
                ));

                $response_code = $this->_framework->wp_remote_retrieve_response_code($response);
                $version_info = $this->_framework->wp_remote_retrieve_body($response);

                if ($response_code == 200) {
                    $done = true;
                } else {
                    $this->_revSliderLoadBalancer->move_server_list();
                }

                $count++;
            } while ($done == false && $count < 5);

            if ( $response_code != 200 || $this->_framework->is_wp_error( $version_info ) ) {
				$this->_framework->update_option('revslider-connection', false);
				return false;
			}else{
				$this->_framework->update_option('revslider-connection', true);
			}

			$version_info = json_decode($version_info);
			if(isset($version_info->version)){
				$this->_framework->update_option('revslider-latest-version', $version_info->version);
			}

			if(isset($version_info->stable)){
				$this->_framework->update_option('revslider-stable-version', $version_info->stable);
			}

			if(isset($version_info->notices)){
				$this->_framework->update_option('revslider-notices', $version_info->notices);
			}

            if(isset($version_info->dashboard)){
                $this->_framework->update_option('revslider-dashboard', $version_info->dashboard);
            }

            if(isset($version_info->addons)){
                $this->_framework->update_option('revslider-addons', $version_info->addons);
            }

            if(isset($version_info->deactivated) && $version_info->deactivated === true){
                if($this->_framework->get_option('revslider-valid', 'false') == 'true'){
                    //remove validation, add notice
                    $this->_framework->update_option('revslider-valid', 'false');
                    $this->_framework->update_option('revslider-deact-notice', true);
                }
            }

		}

		if($force_check == true){ //force that the update will be directly searched
			$this->_framework->update_option('revslider-update-check', '');
		}

	}


    public function add_temp_active_check($force = false){

        $last_check = $this->_framework->get_option('revslider-activate-temp-short');
        if($last_check == false){ //first time called
            $last_check = time();
            $this->_framework->update_option('revslider-activate-temp-short', $last_check);
        }

        // Check for updates
        if (time() - $last_check > 3600 || $force == true) {
            $done = false;
            $count = 0;
            do {
                $url = $this->_revSliderLoadBalancer->get_url('updates');
                $response = $this->_framework->wp_remote_post($url . '/' . $this->remote_temp_active, array(
                    'body' => array(
                        'item' => urlencode(RevSliderGlobals::PLUGIN_SLUG),
                        'version' => urlencode(RevSliderGlobals::SLIDER_REVISION),
                        'code' => urlencode($this->_framework->get_option('revslider-code', ''))
                    ),
                    'timeout' => 45
                ));

                $response_code = $this->_framework->wp_remote_retrieve_response_code($response);
                $version_info = $this->_framework->wp_remote_retrieve_body($response);

                if ($response_code == 200) {
                    $done = true;
                } else {
                    $this->_revSliderLoadBalancer->move_server_list();
                }

                $count++;
            } while ($done == false && $count < 5);

            if ( $response_code != 200 || $this->_framework->is_wp_error( $version_info ) ) {
                //wait, cant connect
            }else{
                if($version_info == 'valid'){
                    $this->_framework->update_option('revslider-valid', 'true');
                    $this->_framework->update_option('revslider-temp-active', 'false');
                }elseif($version_info == 'temp_valid'){
                    //do nothing,
                }elseif($version_info == 'invalid'){
                    //invalid, deregister plugin!
                    $this->_framework->update_option('revslider-valid', 'false');
                    $this->_framework->update_option('revslider-temp-active', 'false');
                    $this->_framework->update_option('revslider-temp-active-notice', 'true');
                }
            }

            $last_check = time();
             $this->_framework->update_option('revslider-activate-temp-short', $last_check);
        }
    }

}
