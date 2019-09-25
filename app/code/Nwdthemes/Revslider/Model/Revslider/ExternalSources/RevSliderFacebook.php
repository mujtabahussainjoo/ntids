<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2015 ThemePunch
 */

namespace Nwdthemes\Revslider\Model\Revslider\ExternalSources;

/**
 * Facebook
 *
 * with help of the API this class delivers album images from Facebook
 *
 * @package    socialstreams
 * @subpackage socialstreams/facebook
 * @author     ThemePunch <info@themepunch.com>
 */

class RevSliderFacebook {

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

	public function __construct(
        \Nwdthemes\Revslider\Helper\Framework $framework,
        $transient_sec = 1200
    ) {
        $this->_framework = $framework;

	    $this->transient_sec = 	$transient_sec;
	}

	/**
	 * Get User ID from its URL
	 *
	 * @since    1.0.0
	 * @param    string    $user_url URL of the Page
	 */
	public function get_user_from_url($user_url){
		$theid = str_replace("https", "", $user_url);
		$theid = str_replace("http", "", $theid);
		$theid = str_replace("://", "", $theid);
		$theid = str_replace("www.", "", $theid);
		$theid = str_replace("facebook", "", $theid);
		$theid = str_replace(".com", "", $theid);
		$theid = str_replace("/", "", $theid);
		$theid = explode("?", $theid);
		return trim($theid[0]);
	}

	/**
	 * Get Photosets List from User
	 *
	 * @since    1.0.0
	 * @param    string    $user_id 	Facebook User id (not name)
	 * @param    int       $item_count 	number of photos to pull
	 */
	public function get_photo_sets($user_id,$item_count=10,$app_id,$app_secret){
		//photoset params
		$oauth = $this->_framework->wp_remote_fopen("https://graph.facebook.com/oauth/access_token?type=client_cred&client_id=".$app_id."&client_secret=".$app_secret);
        $oauth = json_decode($oauth);
        $url = "https://graph.facebook.com/$user_id/albums?access_token=".$oauth->access_token;
		$photo_sets_list = json_decode($this->_framework->wp_remote_fopen($url));

		if(isset($photo_sets_list->data))
			return $photo_sets_list->data;
		else return '';
	}

	/**
	 * Get Photoset Photos
	 *
     * @since    5.1.1
	 * @param    string    $photo_set_id 	Photoset ID
	 * @param    int       $item_count 	number of photos to pull
	 */
	public function get_photo_set_photos($photo_set_id,$item_count=10,$app_id,$app_secret){
		$oauth = $this->_framework->wp_remote_fopen("https://graph.facebook.com/oauth/access_token?type=client_cred&client_id=".$app_id."&client_secret=".$app_secret);
        $oauth = json_decode($oauth);
        $url = "https://graph.facebook.com/$photo_set_id/photos?fields=photos&access_token=".$oauth->access_token."&fields=id,from,message,picture,link,name,icon,privacy,type,status_type,object_id,application,created_time,updated_time,is_hidden,is_expired,comments.limit(1).summary(true),likes.limit(1).summary(true)";

		$transient_name = 'revslider_' . md5($url);

		if ($this->transient_sec > 0 && false !== ($data = $this->_framework->get_transient( $transient_name)))
			return ($data);

		$photo_set_photos = json_decode($this->_framework->wp_remote_fopen($url));

        if(isset($photo_set_photos->data)){
            $this->_framework->set_transient( $transient_name, $photo_set_photos->data, $this->transient_sec );
            return $photo_set_photos->data;
		}
		else return '';
	}

	/**
	 * Get Photosets List from User as Options for Selectbox
	 *
	 * @since    1.0.0
	 * @param    string    $user_url 	Facebook User id (not name)
	 * @param    int       $item_count 	number of photos to pull
	 */
	public function get_photo_set_photos_options($user_url,$current_album,$app_id,$app_secret,$item_count=99){
		$user_id = $this->get_user_from_url($user_url);
		$photo_sets = $this->get_photo_sets($user_id,999,$app_id,$app_secret);
		if(empty($current_album)) $current_album = "";
		$return = array();
		if(is_array($photo_sets)){
			foreach($photo_sets as $photo_set){
				$return[] = '<option title="'.$photo_set->name.'" '.$this->_framework->selected( $photo_set->id , $current_album , false ).' value="'.$photo_set->id.'">'.$photo_set->name.'</option>"';
			}
		}
		return $return;
	}


	/**
	 * Get Feed
	 *
	 * @since    1.0.0
	 * @param    string    $user 	User ID
	 * @param    int       $item_count 	number of itmes to pull
	 */
	public function get_photo_feed($user,$app_id,$app_secret,$item_count=10){
		$oauth = $this->_framework->wp_remote_fopen("https://graph.facebook.com/oauth/access_token?type=client_cred&client_id=".$app_id."&client_secret=".$app_secret);
        $oauth = json_decode($oauth);
        $url = "https://graph.facebook.com/$user/feed?access_token=".$oauth->access_token."&fields=id,from,message,picture,link,name,icon,privacy,type,status_type,object_id,application,created_time,updated_time,is_hidden,is_expired,comments.limit(1).summary(true),likes.limit(1).summary(true)";

		$transient_name = 'revslider_' . md5($url);
		if ($this->transient_sec > 0 && false !== ($data = $this->_framework->get_transient( $transient_name)))
			return ($data);

		$feed = json_decode($this->_framework->wp_remote_fopen($url));

		if(isset($feed->data)){
			$this->_framework->set_transient( $transient_name, $feed->data, $this->transient_sec );
			return $feed->data;
		}
		else return '';
	}

	/**
	 * Decode URL from feed
	 *
	 * @since    1.0.0
	 * @param    string    $url 	facebook Output Data
	 */
	private function decode_facebook_url($url) {
		$url = str_replace('u00253A',':',$url);
		$url = str_replace('\u00255C\u00252F','/',$url);
		$url = str_replace('u00252F','/',$url);
		$url = str_replace('u00253F','?',$url);
		$url = str_replace('u00253D','=',$url);
		$url = str_replace('u002526','&',$url);
		return $url;
	}
}