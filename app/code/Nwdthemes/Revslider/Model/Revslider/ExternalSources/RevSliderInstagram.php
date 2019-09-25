<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2015 ThemePunch
 */

namespace Nwdthemes\Revslider\Model\Revslider\ExternalSources;

/**
 * Instagram
 *
 * with help of the API this class delivers all kind of Images from instagram
 *
 * @package    socialstreams
 * @subpackage socialstreams/instagram
 * @author     ThemePunch <info@themepunch.com>
 */

class RevSliderInstagram {

    protected $_framework;

	/**
	 * Stream Array
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $stream    Stream Data Array
	 */
	private $stream;

	/**
   * Transient seconds
   *
   * @since    1.0.0
   * @access   private
   * @var      number    $transient Transient time in seconds
   */
  private $transient_sec;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $api_key	Instagram API key.
	 */
	public function __construct(
        \Nwdthemes\Revslider\Helper\Framework $framework,
        $transient_sec=1200
    ) {
        $this->_framework = $framework;
		$this->transient_sec = $transient_sec;
	}

    /**
     * Get Instagram Pictures Public by User
     *
     * @since    1.0.0
     * @param    string    $user_id   Instagram User id (not name)
     */
    public function get_public_photos($search_user_id,$count)
    {
        if(!empty($search_user_id)){
            $url = 'https://www.instagram.com/'.$search_user_id.'/';

            $transient_name = 'revslider_' . md5($url);

            if ($this->transient_sec > 0 && false !== ($data = $this->_framework->get_transient( $transient_name)))
                return ($data);

            $rsp = json_decode(json_encode($this->getFallbackImages($search_user_id)));
            for($i=0;$i<$count;$i++) {
                if(isset($rsp->edge_owner_to_timeline_media->edges[$i])){
                    $return[] = $rsp->edge_owner_to_timeline_media->edges[$i];
                }
            }

            if(isset($return)){
                $this->_framework->set_transient( $transient_name, $return, $this->transient_sec );
                return $return;
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    /**
     * Get user ID if necessary
     * @since 5.4.6.3
     */
    public function get_user_id($search_user_id) {
       $url = 'https://www.instagram.com/'.$search_user_id.'/?__a=1';

        // check for transient
        $transient_name = 'revslider_' . md5($url);
        if ($this->transient_sec > 0 && false !== ($data = $this->_framework->get_transient( $transient_name)))
            return ($data);

        // contact API
        $rsp = json_decode($this->_framework->wp_remote_fopen($url));

        // set new transient
        if(isset($rsp->user->id))
            $this->_framework->set_transient( $transient_name, $rsp->user->id, 604800 );

        // return user id
        if(isset($rsp->user->id))
            return $rsp->user->id;
        else
            return false;
    }

    /**
     * Fallback method to get 12 latest photos
     * @param String $search_user_id (name of instagram user)
     */
    private function getFallbackImages($search_user_id) {
        //FALLBACK 12 ELEMENTS
        $page_res = $this->client_request('get', '/' . $search_user_id . '/');
        switch ($page_res['http_code']) {
        default:
            break;

        case 404:
            break;

        case 200:
            $page_data_matches = array();

            if (!preg_match('#window\._sharedData\s*=\s*(.*?)\s*;\s*</script>#', $page_res['body'], $page_data_matches)) {
            echo __('Instagram reports: Parse script error');

            } else {
            $page_data = json_decode($page_data_matches[1], true);

            if (!$page_data || empty($page_data['entry_data']['ProfilePage'][0]['graphql']['user'])) {
                echo __('Instagram reports: Content did not match expected');

            } else {
                $user_data = $page_data['entry_data']['ProfilePage'][0]['graphql']['user'];

                if ($user_data['is_private']) {
                echo __('Instagram reports: Content is private');

                }
            }
            }

            break;
        }
        $user_data = isset($page_data['entry_data']['ProfilePage'][0]['graphql']['user']) ? $page_data['entry_data']['ProfilePage'][0]['graphql']['user'] : false;
        return $user_data;
    }

    /**
     * Cliente request to get 12 instagram photos fallback
     * @param unknown $type
     * @param unknown $url
     * @param unknown $options
     * @return number[]|string[]|NULL|number[]|string[]|number[]|unknown[]|string[]|number[]|unknown[]|unknown[][]|string[][]|number[][]|NULL[][]
     */
    private function client_request($type, $url, $options = null) {

        $this->index('client', array(
            'base_url' => 'https://www.instagram.com/',
            'cookie_jar' => array(),
            'headers' => array(
                // 'Accept-Encoding' => supports_gz () ? 'gzip' : null,
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.87 Safari/537.36',
                'Origin' => 'https://www.instagram.com',
                'Referer' => 'https://www.instagram.com',
                'Connection' => 'close'
            )
        ));
        $client = $this->index('client');
        $type = strtoupper($type);
        $options = is_array($options) ? $options : array();

        $url = (!empty($client['base_url']) ? rtrim($client['base_url'], '/') : '') . $url;
        $url_info = parse_url($url);

        $scheme = !empty($url_info['scheme']) ? $url_info['scheme'] : '';
        $host = !empty($url_info['host']) ? $url_info['host'] : '';
        $port = !empty($url_info['port']) ? $url_info['port'] : '';
        $path = !empty($url_info['path']) ? $url_info['path'] : '';
        $query_str = !empty($url_info['query']) ? $url_info['query'] : '';

        if (!empty($options['query'])) {
        $query_str = http_build_query($options['query']);
        }

        $headers = !empty($client['headers']) ? $client['headers'] : array();

        if (!empty($options['headers'])) {
        $headers = $this->array_merge_assoc($headers, $options['headers']);
        }

        $headers['Host'] = $host;

        $client_cookies = $this->client_get_cookies_list($host);
        $cookies = $client_cookies;

        if (!empty($options['cookies'])) {
        $cookies = $this->array_merge_assoc($cookies, $options['cookies']);
        }

        if ($cookies) {
        $request_cookies_raw = array();

        foreach ($cookies as $cookie_name => $cookie_value) {
            $request_cookies_raw[] = $cookie_name . '=' . $cookie_value;
        }
        unset($cookie_name, $cookie_data);

        $headers['Cookie'] = implode('; ', $request_cookies_raw);
        }

        if ($type === 'POST' && !empty($options['data'])) {
        $data_str = http_build_query($options['data']);
        $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        $headers['Content-Length'] = strlen($data_str);

        } else {
        $data_str = '';
        }

        $headers_raw_list = array();

        foreach ($headers as $header_key => $header_value) {
        $headers_raw_list[] = $header_key . ': ' . $header_value;
        }
        unset($header_key, $header_value);

        $transport_error = null;
        $curl_support = function_exists('curl_init');
        $sockets_support = function_exists('fsockopen');

        if (!$curl_support && !$sockets_support) {
        log_error('Curl and sockets are not supported on this server');

        return array(
            'status' => 0,
            'transport_error' => 'php on web-server does not support curl and sockets'
        );
        }

        if ($curl_support) {


        $curl = curl_init();
        $curl_options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_URL => $scheme . '://' . $host . $path . (!empty($query_str) ? '?' . $query_str : ''),
            CURLOPT_HTTPHEADER => $headers_raw_list,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => 60,
        );
        if ($type === 'POST') {
            $curl_options[CURLOPT_POST] = true;
            $curl_options[CURLOPT_POSTFIELDS] = $data_str;
        }

        curl_setopt_array($curl, $curl_options);

        $response_str = curl_exec($curl);
        $curl_info = curl_getinfo($curl);
        $curl_error = curl_error($curl);

        curl_close($curl);


        if ($curl_info['http_code'] === 0) {
            log_error('An error occurred while loading data. curl_error: ' . $curl_error);

            $transport_error = array('status' => 0, 'transport_error' => 'curl');

            if (!$sockets_support) {
            return $transport_error;

            }

        }
        }

        if (!$curl_support || $transport_error) {
        log_error('Trying to load data using sockets');

        $headers_str = implode("\r\n", $headers_raw_list);

        $out = sprintf("%s %s HTTP/1.1\r\n%s\r\n\r\n%s", $type, $path . (!empty($query_str) ? '?' . $query_str : ''), $headers_str, $data_str);

        if ($scheme === 'https') {
            $scheme = 'ssl';
            $port = !empty($port) ? $port : 443;
        }

        $scheme = !empty($scheme) ? $scheme . '://' : '';
        $port = !empty($port) ? $port : 80;

        $sock = @fsockopen($scheme . $host, $port, $err_num, $err_str, 15);

        if (!$sock) {
            log_error('An error occurred while loading data error_number: ' . $err_num . ', error_number: ' . $err_str);

            return array(
                'status' => 0,
                'error_number' => $err_num,
                'error_message' => $err_str,
                'transport_error' => $transport_error ? 'curl and sockets' : 'sockets'
            );
        }

        fwrite($sock, $out);

        $response_str = '';

        while ($line = fgets($sock, 128)) {
            $response_str .= $line;
        }

        fclose($sock);
        }


        @list ($response_headers_str, $response_body_encoded, $alt_body_encoded) = explode("\r\n\r\n", $response_str);

        if ($alt_body_encoded) {
        $response_headers_str = $response_body_encoded;
        $response_body_encoded = $alt_body_encoded;
        }


        $response_body = $response_body_encoded;
        $response_headers_raw_list = explode("\r\n", $response_headers_str);
        $response_http = array_shift($response_headers_raw_list);

        preg_match('#^([^\s]+)\s(\d+)\s([^$]+)$#', $response_http, $response_http_matches);
        array_shift($response_http_matches);
        list ($response_http_protocol, $response_http_code, $response_http_message) = $response_http_matches;

        $response_headers = array();
        $response_cookies = array();
        foreach ($response_headers_raw_list as $header_row) {
        list ($header_key, $header_value) = explode(': ', $header_row, 2);

        if (strtolower($header_key) === 'set-cookie') {
            $cookie_params = explode('; ', $header_value);

            if (empty($cookie_params[0])) {
            continue;
            }

            list ($cookie_name, $cookie_value) = explode('=', $cookie_params[0]);
            $response_cookies[$cookie_name] = $cookie_value;

        } else {
            $response_headers[$header_key] = $header_value;
        }
        }
        unset($header_row, $header_key, $header_value, $cookie_name, $cookie_value);

        if ($response_cookies) {
        $response_cookies['ig_or'] = 'landscape-primary';
        $response_cookies['ig_pr'] = '1';
        $response_cookies['ig_vh'] = rand(500, 1000);
        $response_cookies['ig_vw'] = rand(1100, 2000);

        $client['cookie_jar'][$host] = $this->array_merge_assoc($client_cookies, $response_cookies);
        $this->index('client', $client);
        }
        return array(
            'status' => 1,
            'http_protocol' => $response_http_protocol,
            'http_code' => $response_http_code,
            'http_message' => $response_http_message,
            'headers' => $response_headers,
            'cookies' => $response_cookies,
            'body' => $response_body
        );
    }
    /**
     * Helper function for fallback photos function
     * @param unknown $domain
     * @return unknown
     */
    private function client_get_cookies_list($domain) {
        $client = $this->index('client');
        $cookie_jar = $client['cookie_jar'];

        return !empty($cookie_jar[$domain]) ? $cookie_jar[$domain] : array();
    }
    /**
     * Helper function for fallback photos function
     * @param unknown $key
     * @param unknown $value
     * @param string $f
     * @return NULL|string
     */
    private function index($key, $value = null, $f = false) {
        static $index = array();

        if ($value || $f) {
        $index[$key] = $value;
        }

        return !empty($index[$key]) ? $index[$key] : null;
    }
    /**
     * Helper function for fallback photos function
     * @return NULL
     */
    private function array_merge_assoc() {
        $mixed = null;
        $arrays = func_get_args();

        foreach ($arrays as $k => $arr) {
        if ($k === 0) {
            $mixed = $arr;
            continue;
        }

        $mixed = array_combine(
            array_merge(array_keys($mixed), array_keys($arr)),
            array_merge(array_values($mixed), array_values($arr))
            );
        }

        return $mixed;
    }

    /**
     * Get Instagram Pictures Public by Tag
     *
     * @since    1.0.0
     * @param    string    $user_id     Instagram User id (not name)
     */
    public function get_tag_photos($search_tag,$count){
        //call the API and decode the response
        $url = "https://www.instagram.com/explore/tags/".$search_tag."/?__a=1";

        $transient_name = 'revslider_' . md5($url);

        $rsp = json_decode($this->_framework->wp_remote_fopen($url));

        for($i=0;$i<$count;$i++) {
            $return[] = $rsp->tag->media->nodes[$i];
        }

        if(isset($rsp->tag->media->nodes)){
            $rsp->tag->media->nodes = $return;
            $this->_framework->set_transient( $transient_name, $rsp->tag->media->nodes, $this->transient_sec );
            return $rsp->tag->media->nodes;
        }
        else return '';
    }
}