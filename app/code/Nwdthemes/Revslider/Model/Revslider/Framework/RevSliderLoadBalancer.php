<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2017 ThemePunch
 */

namespace Nwdthemes\Revslider\Model\Revslider\Framework;

use \Nwdthemes\Revslider\Model\Revslider\RevSliderGlobals;

class RevSliderLoadBalancer
{

    public $servers = array();
    protected $_frameworkHelper;

    /**
     * set the server list on construct
     **/
    public function __construct(
        \Nwdthemes\Revslider\Helper\Framework $frameworkHelper
    ) {
        $this->_frameworkHelper = $frameworkHelper;
        $this->servers = $this->_frameworkHelper->get_option('revslider_servers', array());
        $this->servers = (empty($this->servers)) ? array('themepunch.tools') : $this->servers;
    }

    /**
     * get the url depending on the purpose, here with key, you can switch do a different server
     **/
    public function get_url($purpose, $key = 0) {
        $url = 'https://';

        $use_url = (!isset($this->servers[$key])) ? reset($this->servers) : $this->servers[$key];

        switch ($purpose) {
            case 'updates':
                $url .= 'updates.';
                break;
            case 'templates':
                $url .= 'templates.';
                break;
            case 'library':
                $url .= 'library.';
                break;
            default:
                return false;
        }

        $url .= $use_url;

        return $url;
    }

    /**
     * refresh the server list to be used, will be done once in a month
     **/
    public function refresh_server_list($force = false) {

        $last_check = $this->_frameworkHelper->get_option('revslider_server_refresh', false);

        if ($force === true || $last_check === false || time() - $last_check > 60 * 60 * 24 * 14) {
            $url = 'https://updates.themepunch.tools';
            $request = $this->_frameworkHelper->wp_remote_post($url . '/get_server_list.php', array(
                'body' => array(
                    'item' => urlencode(RevSliderGlobals::PLUGIN_SLUG),
                    'version' => urlencode(RevSliderGlobals::SLIDER_REVISION)
                ),
                'timeout' => 45
            ));
            if (!$this->_frameworkHelper->is_wp_error($request)) {
                if ($response = $this->_frameworkHelper->maybe_unserialize($request['body'])) {
                    $list = json_decode($response, true);
                    $this->_frameworkHelper->update_option('revslider_servers', $list);
                }
            }

            $this->_frameworkHelper->update_option('revslider_server_refresh', time());
        }
    }

    /**
     * move the server list, to take the next server as the one currently seems unavailable
     **/
    public function move_server_list() {

        $servers = $this->servers;

        $a = array_shift($servers);
        $servers[] = $a;

        $this->servers = $servers;
        $this->_frameworkHelper->update_option('revslider_servers', $servers);
    }

}
