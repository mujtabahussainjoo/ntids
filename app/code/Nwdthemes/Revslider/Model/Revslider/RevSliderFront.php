<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2015 ThemePunch
 */

namespace Nwdthemes\Revslider\Model\Revslider;

use \Nwdthemes\Revslider\Helper\Data;
use \Nwdthemes\Revslider\Helper\Framework;
use \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderBase;
use \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderCssParser;
use \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderFunctions;
use \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderFunctionsWP;

class RevSliderFront extends \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderBaseFront {

	protected $_framework;
    protected $_query;
    protected $_curl;
    protected $_filesystem;
    protected $_images;
    protected $_resource;
    protected $_backendUrl;
    protected $_googleFonts;

	protected static $framework;
	protected static $query;
	protected static $curl;
	protected static $filesystem;
	protected static $images;
	protected static $resource;
    protected static $googleFonts;
	protected static $registerHelper;

	/**
	 *	Constructor
	 */

	public function __construct(
        \Nwdthemes\Revslider\Helper\Framework $framework,
        \Nwdthemes\Revslider\Helper\Query $query,
        \Nwdthemes\Revslider\Helper\Curl $curl,
        \Nwdthemes\Revslider\Helper\Filesystem $filesystem,
        \Nwdthemes\Revslider\Helper\Images $images,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Backend\Model\UrlInterface $backendUrl,
		\Nwdthemes\Revslider\Model\Revslider\RevSliderOperations $revSliderOperations,
		\Nwdthemes\Revslider\Model\Revslider\RevSliderOutput $revSliderOutput,
        \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderFunctions $revSliderFunctions,
        \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderFunctionsWP $revSliderFunctionsWP,
        \Nwdthemes\Revslider\Model\Revslider\GoogleFonts $googleFonts,
		\Nwdthemes\Revslider\Helper\Register $registerHelper
    ) {
        $this->_framework = $framework;
        $this->_query = $query;
        $this->_curl = $curl;
        $this->_filesystem = $filesystem;
        $this->_images = $images;
        $this->_resource = $resource;
        $this->_backendUrl = $backendUrl;
        $this->_googleFonts = $googleFonts;

		self::$framework = $this->_framework;
		self::$query = $this->_query;
		self::$curl = $this->_curl;
		self::$filesystem = $this->_filesystem;
        self::$images = $this->_images;
		self::$resource = $this->_resource;
        self::$googleFonts = $this->_googleFonts;
		self::$registerHelper = $registerHelper;

		parent::__construct($this, $this->_filesystem, $this->_framework, $this->_query, $this->_images, $this->_backendUrl);

		//set table names
		RevSliderGlobals::$table_sliders = self::$table_prefix.RevSliderGlobals::TABLE_SLIDERS_NAME;
		RevSliderGlobals::$table_slides = self::$table_prefix.RevSliderGlobals::TABLE_SLIDES_NAME;
		RevSliderGlobals::$table_static_slides = self::$table_prefix.RevSliderGlobals::TABLE_STATIC_SLIDES_NAME;
		RevSliderGlobals::$table_settings = self::$table_prefix.RevSliderGlobals::TABLE_SETTINGS_NAME;
		RevSliderGlobals::$table_css = self::$table_prefix.RevSliderGlobals::TABLE_CSS_NAME;
		RevSliderGlobals::$table_layer_anims = self::$table_prefix.RevSliderGlobals::TABLE_LAYER_ANIMS_NAME;
		RevSliderGlobals::$table_navigation = self::$table_prefix.RevSliderGlobals::TABLE_NAVIGATION_NAME;

		$this->_framework->add_filter('punchfonts_modify_url', array('\Nwdthemes\Revslider\Model\Revslider\RevSliderFront', 'modify_punch_url'));

		$this->_framework->add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
	}


	/**
	 *
	 * a must function. you can not use it, but the function must stay there!
	 */
	public static function onAddScripts(){

		$custom_css = RevSliderOperations::getStaticCss();
		$custom_css = RevSliderCssParser::compress_css($custom_css);

		if(trim($custom_css) == '') $custom_css = '#rs-demo-id {}';

		self::$framework->wp_add_inline_style( 'rs-plugin-settings', '<style type="text/css">' . $custom_css . '</style>' );

		self::$framework->add_action("wp_footer", array('\Nwdthemes\Revslider\Model\Revslider\RevSliderFront', "load_icon_fonts") );
		self::$framework->add_action('wp_footer', array('\Nwdthemes\Revslider\Model\Revslider\RevSliderFront', 'putAdminBarMenus'), 99);
	}

	/**
	 * add admin menu points in ToolBar Top
	 * @since: 5.0.5
	 */
	public static function putAdminBarMenus () {
		if(!self::$framework->is_super_admin() || !self::$framework->is_admin_bar_showing()) return;

		?>
		<script>
			require(['jquery'], function(jQuery) {
				jQuery(document).ready(function () {

					if (jQuery('#wp-admin-bar-revslider-default').length > 0 && jQuery('.rev_slider_wrapper').length > 0) {
						var aliases = new Array();
						jQuery('.rev_slider_wrapper').each(function () {
							aliases.push(jQuery(this).data('alias'));
						});
						if (aliases.length > 0)
							jQuery('#wp-admin-bar-revslider-default li').each(function () {
								var li = jQuery(this),
									t = jQuery.trim(li.find('.ab-item .rs-label').data('alias')); //text()

								if (jQuery.inArray(t, aliases) != -1) {
								} else {
									li.remove();
								}
							});
					} else {
						jQuery('#wp-admin-bar-revslider').remove();
					}
				});
			});
		</script>
		<?php
	}

	/**
	 * add admin node
	 * @since: 5.0.5
	 */
	public static function _add_node($title, $parent = false, $href = '', $custom_meta = array(), $id = ''){
		global $wp_admin_bar;

		if(!self::$framework->is_super_admin() || !self::$framework->is_admin_bar_showing()) return;

		if($id == '') $id = strtolower(str_replace(' ', '-', $title));

		// links from the current host will open in the current window
		$meta = strpos( $href, site_url() ) !== false ? array() : array( 'target' => '_blank' ); // external links open in new tab/window
		$meta = array_merge( $meta, $custom_meta );

		$wp_admin_bar->add_node(array(
			'parent' => $parent,
			'id'     => $id,
			'title'  => $title,
			'href'   => $href,
			'meta'   => $meta,
		));
	}


	/**
	 *
	 * create db tables
	 */
	public static function createDBTables(){
		if(self::$framework->get_option('revslider_change_database', false) || self::$framework->get_option('rs_tables_created', false) === false){
			self::createTable(RevSliderGlobals::TABLE_SLIDERS_NAME);
			self::createTable(RevSliderGlobals::TABLE_SLIDES_NAME);
			self::createTable(RevSliderGlobals::TABLE_STATIC_SLIDES_NAME);
			self::createTable(RevSliderGlobals::TABLE_CSS_NAME);
			self::createTable(RevSliderGlobals::TABLE_LAYER_ANIMS_NAME);
			self::createTable(RevSliderGlobals::TABLE_NAVIGATION_NAME);
			self::$framework->update_option('rs_tables_created', true);
			self::$framework->update_option('revslider_change_database', false);
		}

		self::updateTables();
	}

	public static function load_icon_fonts(){
		global $fa_icon_var,$pe_7s_var;
		$output = '';
		if ($fa_icon_var) {
            $output .= "<link rel='stylesheet' property='stylesheet' id='rs-icon-set-fa-icon-css'  href='" . Framework::$RS_PLUGIN_URL . "public/assets/fonts/font-awesome/css/font-awesome.min.css' type='text/css' media='all' />";
        }
		if ($pe_7s_var) {
            $output .= "<link rel='stylesheet' property='stylesheet' id='rs-icon-set-pe-7s-css'  href='" . Framework::$RS_PLUGIN_URL . "public/assets/fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.min.css' type='text/css' media='all' />";
        }
        return $output;
	}

	public static function updateTables(){
		$cur_ver = self::$framework->get_option('revslider_table_version', '1.0.0');
        if(self::$framework->get_option('revslider_change_database', false)){
            $cur_ver = '1.0.0';
        }

        if(version_compare($cur_ver, '1.0.6', '<')){

            $tableName = RevSliderGlobals::TABLE_SLIDERS_NAME;
			$sql = "CREATE TABLE " .self::$table_prefix.$tableName ." (
						  id int(9) NOT NULL AUTO_INCREMENT,
                          title tinytext NOT NULL,
                          alias tinytext,
                          params LONGTEXT NOT NULL,
                          settings TEXT NULL,
                          type VARCHAR(191) NOT NULL DEFAULT '',
						  UNIQUE KEY id (id)
						);";
			self::$query->dbDelta($sql);

            $tableName = RevSliderGlobals::TABLE_SLIDES_NAME;
			$sql = "CREATE TABLE " .self::$table_prefix.$tableName ." (
						  id int(9) NOT NULL AUTO_INCREMENT,
						  slider_id int(9) NOT NULL,
                          slide_order int not NULL,
                          params LONGTEXT NOT NULL,
                          layers LONGTEXT NOT NULL,
                          settings TEXT NOT NULL DEFAULT '',
						  UNIQUE KEY id (id)
						);";
			self::$query->dbDelta($sql);

            $tableName = RevSliderGlobals::TABLE_STATIC_SLIDES_NAME;
			$sql = "CREATE TABLE " .self::$table_prefix.$tableName ." (
						  id int(9) NOT NULL AUTO_INCREMENT,
                          slider_id int(9) NOT NULL,
                          params LONGTEXT NOT NULL,
                          layers LONGTEXT NOT NULL,
                          settings TEXT NOT NULL,
						  UNIQUE KEY id (id)
						);";
			self::$query->dbDelta($sql);

			$tableName = RevSliderGlobals::TABLE_CSS_NAME;
			$sql = "CREATE TABLE " .self::$table_prefix.$tableName ." (
						  id int(9) NOT NULL AUTO_INCREMENT,
						  handle TEXT NOT NULL,
                          settings LONGTEXT,
                          hover LONGTEXT,
                          advanced LONGTEXT,
                          params LONGTEXT NOT NULL,
						  UNIQUE KEY id (id)
						);";
			self::$query->dbDelta($sql);

            $tableName = RevSliderGlobals::TABLE_LAYER_ANIMS_NAME;
            $sql = "CREATE TABLE " .self::$table_prefix.$tableName ." (
                      id int(9) NOT NULL AUTO_INCREMENT,
                      settings text NULL,
					  UNIQUE KEY id (id)
					);";
			self::$query->dbDelta($sql);

            self::$framework->update_option('revslider_table_version', '1.0.6');
		}

	}


	/**
	 * create tables
	 */
	public static function createTable($tableName){

		$parseCssToDb = false;

		$checkForTablesOneTime = self::$framework->get_option('revslider_checktables', '0');

		if($checkForTablesOneTime == '0'){
			self::$framework->update_option('revslider_checktables', '1');
			if(RevSliderFunctionsWP::isDBTableExists(self::$table_prefix.RevSliderGlobals::TABLE_CSS_NAME)){ //$wpdb->tables( 'global' )
				//check if database is empty
				$result = $wpdb->get_row("SELECT COUNT( DISTINCT id ) AS NumberOfEntrys FROM ".self::$table_prefix.RevSliderGlobals::TABLE_CSS_NAME);
				if($result->NumberOfEntrys == 0) $parseCssToDb = true;
			}
		}

		if($parseCssToDb){
			$RevSliderOperations = new RevSliderOperations(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);
			$RevSliderOperations->importCaptionsCssContentArray();
			$RevSliderOperations->moveOldCaptionsCss();
		}

        if(!self::$framework->get_option('revslider_change_database', false)){
            //if table exists - don't create it.
            $tableRealName = self::$table_prefix.$tableName;
            if(RevSliderFunctionsWP::isDBTableExists($tableRealName))
                return(false);
        }

		switch($tableName){
			case RevSliderGlobals::TABLE_SLIDERS_NAME:
			$sql = "CREATE TABLE " .self::$table_prefix.$tableName ." (
						  id int(9) NOT NULL AUTO_INCREMENT,
						  title tinytext NOT NULL,
						  alias tinytext,
                          params LONGTEXT NOT NULL,
						  settings TEXT NULL,
						  type VARCHAR(191) NOT NULL DEFAULT '',
						  UNIQUE KEY id (id)
						);";
			break;
			case RevSliderGlobals::TABLE_SLIDES_NAME:
				$sql = "CREATE TABLE " .self::$table_prefix.$tableName ." (
							  id int(9) NOT NULL AUTO_INCREMENT,
							  slider_id int(9) NOT NULL,
							  slide_order int not NULL,
                              params LONGTEXT NOT NULL,
                              layers LONGTEXT NOT NULL,
							  settings TEXT NOT NULL DEFAULT '',
							  UNIQUE KEY id (id)
							);";
			break;
			case RevSliderGlobals::TABLE_STATIC_SLIDES_NAME:
				$sql = "CREATE TABLE " .self::$table_prefix.$tableName ." (
							  id int(9) NOT NULL AUTO_INCREMENT,
							  slider_id int(9) NOT NULL,
                              params LONGTEXT NOT NULL,
                              layers LONGTEXT NOT NULL,
							  settings TEXT NOT NULL,
							  UNIQUE KEY id (id)
							);";
			break;
			case RevSliderGlobals::TABLE_CSS_NAME:
				$sql = "CREATE TABLE " .self::$table_prefix.$tableName ." (
							  id int(9) NOT NULL AUTO_INCREMENT,
							  handle TEXT NOT NULL,
                              settings LONGTEXT,
                              hover LONGTEXT,
							  advanced LONGTEXT,
                              params LONGTEXT NOT NULL,
							  UNIQUE KEY id (id)
							);";
				$parseCssToDb = true;
			break;
			case RevSliderGlobals::TABLE_LAYER_ANIMS_NAME:
				$sql = "CREATE TABLE " .self::$table_prefix.$tableName ." (
							  id int(9) NOT NULL AUTO_INCREMENT,
							  handle TEXT NOT NULL,
							  params TEXT NOT NULL,
							  settings TEXT NULL,
							  UNIQUE KEY id (id)
							);";
			break;
			case RevSliderGlobals::TABLE_NAVIGATION_NAME:
				$sql = "CREATE TABLE " .self::$table_prefix.$tableName ." (
							  id int(9) NOT NULL AUTO_INCREMENT,
							  name VARCHAR(191) NOT NULL,
							  handle VARCHAR(191) NOT NULL,
                              css LONGTEXT NOT NULL,
                              markup LONGTEXT NOT NULL,
                              settings LONGTEXT NULL,
							  UNIQUE KEY id (id)
							);";
			break;
			default:
				RevSliderFunctions::throwError("table: $tableName not found");
			break;
		}

		self::$query->dbDelta($sql);

        if(!self::$framework->get_option('revslider_change_database', false)){
            if($parseCssToDb){
                $RevSliderOperations = new RevSliderOperations(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);
                $RevSliderOperations->importCaptionsCssContentArray();
                $RevSliderOperations->moveOldCaptionsCss();
            }
		}

	}



	public function enqueue_styles(){

	}


	/**
	 * Change FontURL to new URL (added for chinese support since google is blocked there)
	 * @since: 5.0
	 */
	public static function modify_punch_url($url){
		$operations = new RevSliderOperations(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);
		$arrValues = $operations->getGeneralSettingsValues();

		$set_diff_font = RevSliderFunctions::getVal($arrValues, "change_font_loading",'');
		if($set_diff_font !== ''){
			return $set_diff_font;
		}else{
			return $url;
		}
	}

    /**
     * Add Meta Generator Tag in FrontEnd
     * @since: 5.4.3
     */
    public static function add_setREVStartSize(){
        $script = '<script type="text/javascript">';
        $script .= 'function setREVStartSize(e){
			try{ e.c=jQuery(e.c);var i=jQuery(window).width(),t=9999,r=0,n=0,l=0,f=0,s=0,h=0;
				if(e.responsiveLevels&&(jQuery.each(e.responsiveLevels,function(e,f){f>i&&(t=r=f,l=e),i>f&&f>r&&(r=f,n=e)}),t>r&&(l=n)),f=e.gridheight[l]||e.gridheight[0]||e.gridheight,s=e.gridwidth[l]||e.gridwidth[0]||e.gridwidth,h=i/s,h=h>1?1:h,f=Math.round(h*f),"fullscreen"==e.sliderLayout){var u=(e.c.width(),jQuery(window).height());if(void 0!=e.fullScreenOffsetContainer){var c=e.fullScreenOffsetContainer.split(",");if (c) jQuery.each(c,function(e,i){u=jQuery(i).length>0?u-jQuery(i).outerHeight(!0):u}),e.fullScreenOffset.split("%").length>1&&void 0!=e.fullScreenOffset&&e.fullScreenOffset.length>0?u-=jQuery(window).height()*parseInt(e.fullScreenOffset,0)/100:void 0!=e.fullScreenOffset&&e.fullScreenOffset.length>0&&(u-=parseInt(e.fullScreenOffset,0))}f=u}else void 0!=e.minHeight&&f<e.minHeight&&(f=e.minHeight);e.c.closest(".rev_slider_wrapper").css({height:f})
			}catch(d){console.log("Failure at Presize of Slider:"+d)}
		};';
        $script .= '</script>'."\n";
        echo self::$framework->apply_filters('revslider_add_setREVStartSize', $script);
    }

    /**
	 *
	 * adds async loading
	 * @since: 5.0
	 */
	public static function add_defer_forscript($url)
	{
	    if ( strpos($url, 'themepunch.revolution.min.js' )===false  && strpos($url, 'themepunch.tools.min.js' )===false )
	        return $url;
	    else if (self::$framework->is_admin())
	        return $url;
	    else
	        return $url."' defer='defer";
	}

}