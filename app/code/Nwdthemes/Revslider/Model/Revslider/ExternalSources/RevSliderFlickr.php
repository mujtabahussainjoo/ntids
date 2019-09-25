<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2015 ThemePunch
 */

namespace Nwdthemes\Revslider\Model\Revslider\ExternalSources;

/**
 * Flickr
 *
 * with help of the API this class delivers all kind of Images from flickr
 *
 * @package    socialstreams
 * @subpackage socialstreams/flickr
 * @author     ThemePunch <info@themepunch.com>
 */

class RevSliderFlickr {

    protected $_framework;

	/**
	 * API key
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $api_key    flickr API key
	 */
	private $api_key;

	/**
	 * API params
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $api_param_defaults    Basic params to call with API
	 */
	private $api_param_defaults;

	/**
	 * Stream Array
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $stream    Stream Data Array
	 */
	private $stream;

	/**
	 * Basic URL
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $url    Url to fetch user from
	 */
	private $flickr_url;

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
	 * @param      string    $api_key	flickr API key.
	 */
	public function __construct(
        \Nwdthemes\Revslider\Helper\Framework $framework,
        $api_key,
        $transient_sec=1200
    ) {
        $this->_framework = $framework;

		$this->api_key = $api_key;
		$this->api_param_defaults = array(
		  'api_key' => $this->api_key,
		  'format' => 'json',
		  'nojsoncallback' => 1,
		);

        $this->transient_sec = $transient_sec;
	}

	/**
	 * Calls Flicker API with set of params, returns json
	 *
	 * @since    1.0.0
	 * @param    array    $params 	Parameter build for API request
	 */
	private function call_flickr_api($params){
		//build url
		$encoded_params = array();
		foreach ($params as $k => $v){
		  $encoded_params[] = urlencode($k).'='.urlencode($v);
		}

		//call the API and decode the response
		$url = "https://api.flickr.com/services/rest/?".implode('&', $encoded_params);
		$transient_name = 'revslider_' . md5($url);

		if ($this->transient_sec > 0 && false !== ($data = $this->_framework->get_transient( $transient_name)))
			return ($data);

		$rsp = json_decode(file_get_contents($url));
		if(isset($rsp)){
			$this->_framework->set_transient( $transient_name, $rsp, $this->transient_sec );
			return $rsp;
		}
		else return '';
	}

	/**
	 * Get User ID from its URL
	 *
	 * @since    1.0.0
	 * @param    string    $user_url URL of the Gallery
	 */
	public function get_user_from_url($user_url){
		//gallery params
		$user_params = $this->api_param_defaults + array(
			'method'  => 'flickr.urls.lookupUser',
  			'url' => $user_url,
		);

		//set User Url
		$this->flickr_url = $user_url;

		//get gallery info
		$user_info = $this->call_flickr_api($user_params);
		if(isset($user_info->user->id))
			return $user_info->user->id;
		else return '';
	}

	/**
	 * Get Group ID from its URL
	 *
	 * @since    1.0.0
	 * @param    string    $group_url URL of the Gallery
	 */
	public function get_group_from_url($group_url){
		//gallery params
		$group_params = $this->api_param_defaults + array(
			'method'  => 'flickr.urls.lookupGroup',
  			'url' => $group_url,
		);

		//set User Url
		$this->flickr_url = $group_url;

		//get gallery info
		$group_info = $this->call_flickr_api($group_params);
		if(isset($group_info->group->id))
			return $group_info->group->id;
		else return '';
	}

	/**
	 * Get Public Photos
	 *
	 * @since    1.0.0
	 * @param    string    $user_id 	flicker User id (not name)
	 * @param    int       $item_count 	number of photos to pull
	 */
	public function get_public_photos($user_id,$item_count=10){
		//public photos params
		$public_photo_params = $this->api_param_defaults + array(
			'method'  => 'flickr.people.getPublicPhotos',
  			'user_id' => $user_id,
  			'extras'  => 'description, license, date_upload, date_taken, owner_name, icon_server, original_format, last_update, geo, tags, machine_tags, o_dims, views, media, path_alias, url_sq, url_t, url_s, url_q, url_m, url_n, url_z, url_c, url_l, url_o',
  			'per_page'=> $item_count,
  			'page' => 1
		);

		//get photo list
		$public_photos_list = $this->call_flickr_api($public_photo_params);
    //var_dump($public_photos);
        if(isset($public_photos_list->photos->photo))
			return $public_photos_list->photos->photo;
		else return '';
	}

	/**
	 * Get Photosets List from User
	 *
	 * @since    1.0.0
	 * @param    string    $user_id 	flicker User id (not name)
	 * @param    int       $item_count 	number of photos to pull
	 */
	public function get_photo_sets($user_id,$item_count=10,$current_photoset){
		//photoset params
		$photo_set_params = $this->api_param_defaults + array(
			'method'  => 'flickr.photosets.getList',
  			'user_id' => $user_id,
  			'per_page'=> $item_count,
  			'page'    => 1
		);

		//get photoset list
		$photo_sets_list = $this->call_flickr_api($photo_set_params);

		$return = array();
		foreach($photo_sets_list->photosets->photoset as $photo_set){
			if(empty($photo_set->title->_content)) $photo_set->title->_content = "";
			if(empty($photo_set->photos))  $photo_set->photos = 0;
			$return[] = '<option title="'.$photo_set->description->_content.'" '.$this->_framework->selected( $photo_set->id , $current_photoset , false ).' value="'.$photo_set->id.'">'.$photo_set->title->_content.' ('.$photo_set->photos.' photos)</option>"';
		}
		return $return;
	}

	/**
	 * Get Photoset Photos
	 *
	 * @since    1.0.0
	 * @param    string    $photo_set_id 	Photoset ID
	 * @param    int       $item_count 	number of photos to pull
	 */
	public function get_photo_set_photos($photo_set_id,$item_count=10){
		//photoset photos params
		$this->stream = array();
		$photo_set_params = $this->api_param_defaults + array(
			'method'  		=> 'flickr.photosets.getPhotos',
  			'photoset_id' 	=> $photo_set_id,
  			'per_page'		=> $item_count,
  			'page'    		=> 1,
  			'extras'		=> 'license, date_upload, date_taken, owner_name, icon_server, original_format, last_update, geo, tags, machine_tags, o_dims, views, media, path_alias, url_sq, url_t, url_s, url_q, url_m, url_n, url_z, url_c, url_l, url_o'
		);

		//get photo list
		$photo_set_photos = $this->call_flickr_api($photo_set_params);
		if(isset($photo_set_photos->photoset->photo))
			return $photo_set_photos->photoset->photo;
		else return '';
	}

	/**
	 * Get Groop Pool Photos
	 *
	 * @since    1.0.0
	 * @param    string    $group_id 	Photoset ID
	 * @param    int       $item_count 	number of photos to pull
	 */
	public function get_group_photos($group_id,$item_count=10){
		//photoset photos params
		$group_pool_params = $this->api_param_defaults + array(
			'method'  		=> 'flickr.groups.pools.getPhotos',
  			'group_id' 	=> $group_id,
  			'per_page'		=> $item_count,
  			'page'    		=> 1,
  			'extras'		=> 'license, date_upload, date_taken, owner_name, icon_server, original_format, last_update, geo, tags, machine_tags, o_dims, views, media, path_alias, url_sq, url_t, url_s, url_q, url_m, url_n, url_z, url_c, url_l, url_o'
		);

		//get photo list
		$group_pool_photos = $this->call_flickr_api($group_pool_params);
		if(isset($group_pool_photos->photos->photo))
			return $group_pool_photos->photos->photo;
		else
			return '';
	}

	/**
	 * Get Gallery ID from its URL
	 *
	 * @since    1.0.0
	 * @param    string    $gallery_url URL of the Gallery
	 * @param    int       $item_count 	number of photos to pull
	 */
	public function get_gallery_from_url($gallery_url){
		//gallery params
		$gallery_params = $this->api_param_defaults + array(
			'method'  => 'flickr.urls.lookupGallery',
  			'url' => $gallery_url,
		);

		//get gallery info
		$gallery_info = $this->call_flickr_api($gallery_params);
		if(isset($gallery_info->gallery->id))
			return $gallery_info->gallery->id;
		else return '';
	}

	/**
	 * Get Gallery Photos
	 *
	 * @since    1.0.0
	 * @param    string    $gallery_id 	flicker Gallery id (not name)
	 * @param    int       $item_count 	number of photos to pull
	 */
	public function get_gallery_photos($gallery_id,$item_count=10){
		//gallery photos params
		$gallery_photo_params = $this->api_param_defaults + array(
			'method'  => 'flickr.galleries.getPhotos',
  			'gallery_id' => $gallery_id,
  			'extras'  => 'description, license, date_upload, date_taken, owner_name, icon_server, original_format, last_update, geo, tags, machine_tags, o_dims, views, media, path_alias, url_sq, url_t, url_s, url_q, url_m, url_n, url_z, url_c, url_l, url_o',
  			'per_page'=> $item_count,
  			'page' => 1
		);

		//get photo list
		$gallery_photos_list = $this->call_flickr_api($gallery_photo_params);
		if(isset($gallery_photos_list->photos->photo))
			return $gallery_photos_list->photos->photo;
		else return '';
	}

	/**
	 * Encode the flickr ID for URL (base58)
	 *
	 * @since    1.0.0
	 * @param    string    $num 	flickr photo id
	 */
	private function base_encode($num, $alphabet='123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ') {
		$base_count = strlen($alphabet);
		$encoded = '';
		while ($num >= $base_count) {
			$div = $num/$base_count;
			$mod = ($num-($base_count*intval($div)));
			$encoded = $alphabet[$mod] . $encoded;
			$num = intval($div);
		}
		if ($num) $encoded = $alphabet[$num] . $encoded;
		return $encoded;
	}
}