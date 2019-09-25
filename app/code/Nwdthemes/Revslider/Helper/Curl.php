<?php

namespace Nwdthemes\Revslider\Helper;

use \Magento\Framework\UrlInterface;

class Curl extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $_userAgent;

	/**
	 *	Constructor
	 */

	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
	    $version = $productMetadata->getVersion();
        $url = $storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB);
	    $this->_userAgent = "Magento/$version; $url";

        parent::__construct($context);
	}

    /**
     *  Check if Curl available
     *
     *  @return boolean
     */

    public function test() {
        $test = function_exists('curl_version');
		return $test;
    }

    /**
     *  Get user agent
     *
     *  @return string
     */

    public function getUserAgent() {
        return $this->_userAgent;
    }

    /**
     *  Do request
     *
     *	@param	string	$url
     *	@param	array	$args
     *	@return	array
     */

    public function request($url, $args = array()) {

        $defaults = array(
            'headers' => array(),
            'cookies' => array(),
            'httpversion' => CURL_HTTP_VERSION_NONE,
            'timeout' => 30,
            'method' => 'POST',
            'body' => array()
        );
        $args = array_merge($defaults, $args);

        $headers = array();
        foreach ($args['headers'] as $key => $value) {
            $headers[] = "$key: $value";
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        if ($args['method'] == 'POST' && $args['body'] && is_array($args['body'])) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args['body']));
        }
        curl_setopt($ch, CURLOPT_USERAGENT, $this->_userAgent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_COOKIE,  implode('; ', $args['cookies']));
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $args['timeout']);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, $args['httpversion'] == '1.0' ? CURL_HTTP_VERSION_1_0 : ($args['httpversion'] == '1.1' ? CURL_HTTP_VERSION_1_1 : CURL_HTTP_VERSION_NONE));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_ENCODING, '');

        $output = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_errno($ch);
        $message = curl_error($ch);

        curl_close ($ch);

        $result = array(
            'response'	=> array('code' => $code, 'message' => $message),
            'body'		=> $output
        );
        return $result;
    }

    /**
     *	Get content from remote url
     *
     *	@param	string	$url
     *	@param	array	$args
     *	@return	array
     */

    public function requestGet($url, $args = array()) {
        $args['method'] = 'GET';
        return $this->request($url, $args);
    }

    /**
     *	Open content from remote url
     *
     *	@param	string	$url
     *	@return	string
     */

    public function remoteFileOpen($url) {
        $args = array(
            'method'             =>         'GET',
            'timeout'            =>         5,
            'redirection'        =>         5,
            'httpversion'        =>         '1.0',
            'blocking'           =>         true,
            'body'               =>         null
        );
        $response = $this->request($url, $args);
        return $response['response']['code'] == 200 ? $response['body'] : '';
    }

    /**
     *  Get response code from response
     *
     *	@param	array	$response
     *	@return	string
     */

    public function getCode($response) {
        return $response['response']['code'];
    }

    /**
     *  Get body from response
     *
     *	@param	array	$response
     *	@return	string
     */

    public function getBody($response) {
        return $response['body'];
    }

}