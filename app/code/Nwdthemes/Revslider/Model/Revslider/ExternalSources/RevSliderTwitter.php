<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2015 ThemePunch
 */

namespace Nwdthemes\Revslider\Model\Revslider\ExternalSources;

use \Nwdthemes\Revslider\Model\Revslider\ExternalSources\RevSliderTwitterApi;

/**
 * Twitter
 *
 * with help of the API this class delivers all kind of tweeted images from twitter
 *
 * @package    socialstreams
 * @subpackage socialstreams/twitter
 * @author     ThemePunch <info@themepunch.com>
 */

class RevSliderTwitter {

    protected $_framework;

    /**
    * Consumer Key
    *
    * @since    1.0.0
    * @access   private
    * @var      string    $consumer_key    Consumer Key
    */
    private $consumer_key;

    /**
    * Consumer Secret
    *
    * @since    1.0.0
    * @access   private
    * @var      string    $consumer_secret    Consumer Secret
    */
    private $consumer_secret;

    /**
    * Access Token
    *
    * @since    1.0.0
    * @access   private
    * @var      string    $access_token    Access Token
    */
    private $access_token;

    /**
    * Access Token Secret
    *
    * @since    1.0.0
    * @access   private
    * @var      string    $access_token_secret    Access Token Secret
    */
    private $access_token_secret;

    /**
    * Twitter Account
    *
    * @since    1.0.0
    * @access   private
    * @var      string    $twitter_account    Account User Name
    */
    private $twitter_account;

    /**
    * Transient seconds
    *
    * @since    1.0.0
    * @access   private
    * @var      number    $transient Transient time in seconds
    */
    private $transient_sec;

    /**
    * Stream Array
    *
    * @since    1.0.0
    * @access   private
    * @var      array    $stream    Stream Data Array
    */
    private $stream;

    /**
    * Initialize the class and set its properties.
    *
    * @since    1.0.0
    * @param      string    $consumer_key Twitter App Registration Consomer Key
    * @param      string    $consumer_secret Twitter App Registration Consomer Secret
    * @param      string    $access_token Twitter App Registration Access Token
    * @param      string    $access_token_secret Twitter App Registration Access Token Secret
    */
    public function __construct(
        \Nwdthemes\Revslider\Helper\Framework $framework,
        $consumer_key,
        $consumer_secret,
        $access_token,
        $access_token_secret,
        $transient_sec = 1200
    ) {
        $this->_framework = $framework;

        $this->consumer_key         =   $consumer_key;
        $this->consumer_secret      =   $consumer_secret;
        $this->access_token         =   $access_token;
        $this->access_token_secret  =   $access_token_secret;
        $this->transient_sec		= 	$transient_sec;
    }

    /**
    * Get Tweets
    *
    * @since    1.0.0
    * @param    string    $twitter_account   Twitter account without trailing @ char
    */
    public function get_public_photos($twitter_account,$include_rts,$exclude_replies,$count,$imageonly){

      //require_once( 'class-wp-twitter-api.php' );
      // Set your personal data retrieved at https://dev.twitter.com/apps
      $credentials = array(
        'consumer_key'    =>  $this->consumer_key,
        'consumer_secret' =>    $this->consumer_secret
      );
      // Let's instantiate our class with our credentials
      $twitter_api = new RevSliderTwitterApi($this->_framework, $credentials, $this->transient_sec);

      $include_rts = $include_rts=="on" ? "true" : "false";
      $exclude_replies = $include_rts=="on" ? "false" : "true";

      $query = '&tweet_mode=extended&count=500&include_entities=true&include_rts='.$include_rts.'&exclude_replies='.$exclude_replies.'&screen_name='.$twitter_account;

      $tweets = $twitter_api->query( $query );

      if(!empty($tweets)){
          return $tweets;
      }
      else return '';
    }


    /**
    * Find Key in array and return value (multidim array possible)
    *
    * @since    1.0.0
    * @param    string    $key   Needle
    * @param    array     $form  Haystack
    */
    public function array_find_element_by_key($key, $form) {
        if (is_array($form) &&  array_key_exists($key, $form)) {
          $ret = $form[$key];
          return $ret;
        }
        if(is_array($form))
            foreach ($form as $k => $v) {
              if (is_array($v)) {
                $ret = $this->array_find_element_by_key($key, $form[$k]);
                if ($ret) {
                  return $ret;
                }
              }
            }
        return FALSE;
    }
}