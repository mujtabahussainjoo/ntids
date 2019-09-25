<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2015 ThemePunch
 */

namespace Nwdthemes\Revslider\Model\Revslider;

use \Nwdthemes\Revslider\Helper\Data;
use \Nwdthemes\Revslider\Helper\Framework;
use \Nwdthemes\Revslider\Helper\Query;
use \Nwdthemes\Revslider\Model\Revslider\ExternalSources\RevSliderFacebook;
use \Nwdthemes\Revslider\Model\Revslider\ExternalSources\RevSliderFlickr;
use \Nwdthemes\Revslider\Model\Revslider\ExternalSources\RevSliderYoutube;
use \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderAddonAdmin;
use \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderBase;
use \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderCssParser;
use \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderDB;
use \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderFunctions;
use \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderFunctionsWP;
use \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderLoadBalancer;
use \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderUpdate;
use \Nwdthemes\Revslider\Model\Revslider\Framework\TPColorpicker;

class RevSliderAdmin extends \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderBaseAdmin {

    protected $_framework;
    protected $_query;
    protected $_curl;
    protected $_filesystem;
    protected $_images;
    protected $_backendUrl;
    protected $_resource;
    protected $_googleFonts;
    protected $_registerHelper;

    protected static $framework;
    protected static $query;
    protected static $curl;
	protected static $filesystem;
    protected static $images;
    protected static $resource;
    protected static $googleFonts;
    protected static $pluginHelper;
    protected static $registerHelper;
    protected static $revSliderLoadBalancer;

	const VIEW_SLIDER = "slider";
	const VIEW_SLIDER_TEMPLATE = "slider_template"; //obsolete
	const VIEW_SLIDERS = "sliders";
	const VIEW_SLIDES = "slides";
	const VIEW_SLIDE = "slide";
	/** @noinspection PhpUndefinedNamespaceInspection */
	/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */

	/**
	 * the constructor
	 */
	public function __construct(
        \Nwdthemes\Revslider\Helper\Data $dataPlugin,
        \Nwdthemes\Revslider\Helper\Framework $framework,
        \Nwdthemes\Revslider\Helper\Query $query,
        \Nwdthemes\Revslider\Helper\Curl $curl,
        \Nwdthemes\Revslider\Helper\Filesystem $filesystem,
        \Nwdthemes\Revslider\Helper\Images $images,
        \Nwdthemes\Revslider\Helper\Plugin $pluginHelper,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\App\ResourceConnection $resource,
		\Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderCssParser $revSliderCssParser,
        \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderFunctions $revSliderFunctions,
        \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderFunctionsWP $revSliderFunctionsWP,
        \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderPluginUpdate $revSliderPluginUpdate,
        \Nwdthemes\Revslider\Model\Revslider\GoogleFonts $googleFonts,
        \Nwdthemes\Revslider\Model\Revslider\RevSliderNavigation $revSliderNavigation,
		\Nwdthemes\Revslider\Helper\Register $registerHelper,
        \Nwdthemes\Revslider\Model\Revslider\Framework\TPColorpicker $TPColorpicker
	){
        $this->_framework = $framework;
        $this->_query = $query;
        $this->_curl = $curl;
        $this->_filesystem = $filesystem;
        $this->_images = $images;
        $this->_backendUrl = $backendUrl;
        $this->_resource = $resource;
        $this->_googleFonts = $googleFonts;
        $this->_registerHelper = $registerHelper;

        self::$framework = $this->_framework;
        self::$query = $this->_query;
        self::$curl = $this->_curl;
		self::$filesystem = $this->_filesystem;
        self::$images = $this->_images;
        self::$resource = $this->_resource;
        self::$googleFonts = $this->_googleFonts;
        self::$registerHelper = $this->_registerHelper;
        self::$pluginHelper = $pluginHelper;
        self::$revSliderLoadBalancer = new RevSliderLoadBalancer($this->_framework);

		parent::__construct(
            $this,
            $this->_framework,
            $this->_query,
            $this->_curl,
            $this->_filesystem,
            $this->_images,
            $this->_resource,
            $this->_backendUrl,
			$this->_googleFonts,
            $this->_registerHelper
        );

		//set table names
		RevSliderGlobals::$table_sliders = RevSliderGlobals::TABLE_SLIDERS_NAME;
		RevSliderGlobals::$table_slides = RevSliderGlobals::TABLE_SLIDES_NAME;
		RevSliderGlobals::$table_static_slides = RevSliderGlobals::TABLE_STATIC_SLIDES_NAME;
		RevSliderGlobals::$table_settings = RevSliderGlobals::TABLE_SETTINGS_NAME;
		RevSliderGlobals::$table_css = RevSliderGlobals::TABLE_CSS_NAME;
		RevSliderGlobals::$table_layer_anims = RevSliderGlobals::TABLE_LAYER_ANIMS_NAME;
		RevSliderGlobals::$table_navigation = RevSliderGlobals::TABLE_NAVIGATION_NAME;

		RevSliderGlobals::$filepath_backup = Framework::$RS_PLUGIN_PATH.'backup/';
		RevSliderGlobals::$filepath_captions = Framework::$RS_PLUGIN_PATH.'public/assets/css/captions.css';
		RevSliderGlobals::$urlCaptionsCSS = Framework::$RS_PLUGIN_URL.'public/assets/css/captions.php';
		RevSliderGlobals::$filepath_dynamic_captions = Framework::$RS_PLUGIN_PATH.'public/assets/css/dynamic-captions.css';
		RevSliderGlobals::$filepath_captions_original = Framework::$RS_PLUGIN_PATH.'public/assets/css/captions-original.css';

		$wp_upload_dir = $this->_framework->wp_upload_dir();
		$wp_upload_dir = $wp_upload_dir['basedir'].'/';
		RevSliderGlobals::$uploadsUrlExportZip = $wp_upload_dir.'export.zip';

		$this->init();
	}


	/**
	 * init all actions
	 */
	private function init(){

		$revSliderAsTheme = false;

		$template = new RevSliderTemplate($this->_framework, $this->_query, $this->_curl, $this->_filesystem, $this->_images, $this->_resource, $this->_googleFonts, $this->_registerHelper);
		$operations = new RevSliderOperations($this->_framework, $this->_query, $this->_curl, $this->_filesystem, $this->_images, $this->_resource, $this->_googleFonts, $this->_registerHelper);
		$obj_library = new RevSliderObjectLibrary($this->_filesystem, $this->_framework, $this->_curl, $this->_images);
		$general_settings = $operations->getGeneralSettingsValues();

		$role = RevSliderBase::getVar($general_settings, 'role', 'admin');
		$force_activation_box = RevSliderBase::getVar($general_settings, 'force_activation_box', 'off');

		if($force_activation_box == 'on'){ //force the notifications and more
			$revSliderAsTheme = false;
		}

		self::setMenuRole($role);

		self::addMenuPage('Slider Revolution', "adminPages");

		self::addSubMenuPage(__('Navigation Editor'), 'display_plugin_submenu_page_navigation', 'revslider_navigation');


		$this->addSliderMetaBox();

		//ajax response to save slider options.
		self::addActionAjax("ajax_action", "onAjaxAction");

        $upgrade = new RevSliderUpdate( RevSliderGlobals::SLIDER_REVISION, $this->_framework );

        $temp_active = $this->_framework->get_option('revslider-temp-active', 'false');

        if($temp_active == 'true'){ //check once an hour
            $temp_force = (isset(Data::$_GET['checktempactivate'])) ? true : false;
            $upgrade->add_temp_active_check($temp_force);
        }

		//add common scripts there
		$validated = $this->_framework->get_option('revslider-valid', 'false');
		$notice = $this->_framework->get_option('revslider-valid-notice', 'true');
		$latestv = RevSliderGlobals::SLIDER_REVISION;
		$stablev = $this->_framework->get_option('revslider-stable-version', '0');

		if(!$revSliderAsTheme || version_compare($latestv, $stablev, '<')){
			if($validated === 'false' && $notice === 'true'){
				$this->_framework->add_action('admin_notices', array($this, 'addActivateNotification'));
			}

			if(isset(Data::$_GET['checkforupdates']) && Data::$_GET['checkforupdates'] == 'true')
				$upgrade->_retrieve_version_info(true);

			if($validated === 'true' || version_compare($latestv, $stablev, '<')) {
				$upgrade->add_update_checks();
			}
		}


		if(isset(Data::$_REQUEST['update_shop'])){
			$template->_get_template_list(true);
		}else{
			$template->_get_template_list();
		}

		if(isset($_REQUEST['update_object_library'])){
			$obj_library->_get_list(true);
		}else{
			$obj_library->_get_list();
		}

		$upgrade->_retrieve_version_info();
		$this->_framework->add_action('admin_notices', array($this, 'add_notices'));

		$this->_framework->add_action('admin_enqueue_scripts', array('\Nwdthemes\Revslider\Model\Revslider\RevSliderAdmin', 'enqueue_styles'));

		$this->_framework->add_action('admin_enqueue_scripts', array('\Nwdthemes\Revslider\Model\Revslider\RevSliderAdmin', 'enqueue_all_admin_scripts'));

		$this->_framework->add_action('wp_ajax_revslider_ajax_call_front', array('RevSliderAdmin', 'onFrontAjaxAction'));
		$this->_framework->add_action('wp_ajax_nopriv_revslider_ajax_call_front', array('RevSliderAdmin', 'onFrontAjaxAction')); //for not logged in users

		$this->_framework->add_action( 'admin_head', array('RevSliderAdmin', 'include_custom_css' ));

        // Add-on Admin
        $addon_admin = new RevSliderAddonAdmin('rev_addon', RevSliderGlobals::SLIDER_REVISION, $this->_filesystem, $this->_framework);
        $this->_framework->add_action( 'admin_enqueue_scripts', array( $addon_admin, 'enqueue_styles') );
        $this->_framework->add_action( 'admin_enqueue_scripts', array( $addon_admin, 'enqueue_scripts') );
        $this->_framework->add_action( 'admin_menu', array( $addon_admin, 'add_plugin_admin_menu'), 11 );
        // Add-on Admin Button Ajax Actions
        $this->_framework->add_action( 'wp_ajax_activate_plugin', array( $addon_admin, 'activate_plugin') );
        $this->_framework->add_action( 'wp_ajax_deactivate_plugin', array( $addon_admin, 'deactivate_plugin'));
        $this->_framework->add_action( 'wp_ajax_install_plugin', array( $addon_admin, 'install_plugin'));
        $this->_framework->add_action('wp_ajax_update_addon', array($this, 'updateAddon'));
    }


    public static function add_plugins_page_notices(){
        $plugins = self::$framework->get_plugins();

        foreach($plugins as $plugin_id => $plugin){

            $slug = dirname($plugin_id);
            if(empty($slug)) continue;
            if($slug !== 'revslider') continue;

            //check version, latest updates and if registered or not
            $validated = self::$framework->get_option('revslider-valid', 'false');
            $latestv = self::$framework->get_option('revslider-latest-version', RevSliderGlobals::SLIDER_REVISION);

            if($validated == 'false'){ //activate for updates and support
                self::$framework->add_action( "after_plugin_row_" . $plugin_id, array('RevSliderAdmin', 'show_purchase_notice'), 10, 3);
            }

            if(version_compare($latestv, $plugin['Version'], '>')){
                self::$framework->add_action( "after_plugin_row_" . $plugin_id, array('RevSliderAdmin', 'show_update_notice'), 10, 3);
            }
        }
    }


    public static function show_purchase_notice(){
        $wp_list_table = self::$framework->_get_list_table('WP_Plugins_List_Table');
        ?>
        <tr class="plugin-update-tr"><td colspan="<?php echo $wp_list_table->get_column_count(); ?>" class="plugin-update colspanchange">
            <div class="update-message installer-q-icon">
			<?php echo __('Activate Slider Revolution for <a href="http://revolution.themepunch.com/direct-customer-benefits/" target="_blank">Premium Benefits (e.g. Add-Ons)</a>.'); ?>
			</div>
        </tr>
        <?php
    }


    public static function show_update_notice(){
        $wp_list_table = self::$framework->_get_list_table('WP_Plugins_List_Table');
        ?>
        <tr class="plugin-update-tr"><td colspan="<?php echo $wp_list_table->get_column_count(); ?>" class="plugin-update colspanchange">
            <div class="update-message">
            <?php echo __('A new version of Slider Revolution is available.'); ?>
            </div>
        </tr>
        <?php
    }


    public static function plugin_action_links($links, $file){
        if ($file == self::$framework->plugin_basename(RS_PLUGIN_FILE_PATH)){
            $rs_enabled = self::$framework->get_option('revslider-valid', 'false');

            if($rs_enabled == 'true'){
                krsort($links);
                end($links);
                $key = key($links);
                $links[$key] .= '';
            }
        }

        return $links;
	}


	public static function enqueue_styles(){
		self::$framework->wp_enqueue_style('rs-open-sans', '//fonts.googleapis.com/css?family=Open+Sans:400,300,700,600,800');
        self::$framework->wp_enqueue_style('revslider-global-styles', Framework::$RS_PLUGIN_URL . 'admin/assets/css/global.min.css', array(), RevSliderGlobals::SLIDER_REVISION );
	}


	public static function include_custom_css(){

		$type = (isset(Data::$_GET['view'])) ? Data::$_GET['view'] : '';
		$page = (isset(Data::$_GET['page'])) ? Data::$_GET['page'] : '';

		if($page !== 'slider' && $page !== 'revslider_navigation') return false; //showbiz fix

		$sliderID = '';

		switch($type){
			case 'slider':

				$sliderID = (isset(Data::$_GET['id'])) ? Data::$_GET['id'] : '';
			break;
			case 'slide':
				$slideID = (isset(Data::$_GET['id'])) ? Data::$_GET['id'] : '';
				if($slideID == 'new') break;

				$slide = new RevSliderSlide(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);
				$slide->initByID($slideID);
				$sliderID = $slide->getSliderID();
			break;
			default:
				if(isset(Data::$_GET['slider'])){
					$sliderID = Data::$_GET['slider'];
				}
			break;
		}

		$arrFieldsParams = array();

		if(!empty($sliderID)){
			$slider = new RevSliderSlider(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);
			$slider->initByID($sliderID);
			$settingsFields = $slider->getSettingsFields();
			$arrFieldsMain = $settingsFields['main'];
			$arrFieldsParams = $settingsFields['params'];
			$custom_css = @stripslashes($arrFieldsParams['custom_css']);
			$custom_css = RevSliderCssParser::compress_css($custom_css);
			echo '<style>'.$custom_css.'</style>';
		}
	}


	public static function enqueue_all_admin_scripts() {
		self::$framework->wp_localize_script('unite_admin', 'rev_lang', self::get_javascript_multilanguage()); //Load multilanguage for JavaScript
		self::$framework->wp_enqueue_style(array('wp-color-picker'));
		self::$framework->wp_enqueue_script(array('wp-color-picker'));
	}


	/**
	 * Include wanted submenu page
	 */
	public function display_plugin_submenu_page_navigation() {
		self::display_plugin_submenu('navigation-editor');
	}


	/**
	 * Include wanted submenu page
	 */
	public function display_plugin_submenu_page_google_fonts() {
		self::display_plugin_submenu('themepunch-google-fonts');
	}


	public static function display_plugin_submenu($subMenu){

		parent::adminPages();

		self::setMasterView('master-view');
		self::requireView($subMenu);
	}


	/**
	 * Create Multilanguage for JavaScript
	 */
	protected static function get_javascript_multilanguage(){
		$lang = array(
			'wrong_alias' => __('-- wrong alias -- '),
			'nav_bullet_arrows_to_none' => __('Navigation Bullets and Arrows are now set to none.'),
			'create_template' => __('Create Template'),
			'really_want_to_delete' => __('Do you really want to delete'),
			'sure_to_replace_urls' => __('Are you sure to replace the urls?'),
			'set_settings_on_all_slider' => __('Set selected settings on all Slides of this Slider? (This will be saved immediately)'),
			'select_slide_img' => __('Select Slide Image'),
			'select_layer_img' => __('Select Layer Image'),
			'select_slide_video' => __('Select Slide Video'),
			'show_slide_opt' => __('Show Slide Options'),
			'hide_slide_opt' => __('Hide Slide Options'),
			'close' => __('Close'),
			'really_update_global_styles' => __('Really update global styles?'),
			'really_clear_global_styles' => __('This will remove all Global Styles, continue?'),
			'global_styles_editor' => __('Global Styles Editor'),
			'select_image' => __('Select Image'),
			'video_not_found' => __('No Thumbnail Image Set on Video / Video Not Found / No Valid Video ID'),
			'handle_at_least_three_chars' => __('Handle has to be at least three character long'),
			'really_change_font_sett' => __('Really change font settings?'),
			'really_delete_font' => __('Really delete font?'),
			'class_exist_overwrite' => __('Class already exists, overwrite?'),
			'class_must_be_valid' => __('Class must be a valid CSS class name'),
			'really_overwrite_class' => __('Really overwrite Class?'),
			'relly_delete_class' => __('Really delete Class'),
			'class_this_cant_be_undone' => __('? This can\'t be undone!'),
			'this_class_does_not_exist' => __('This class does not exist.'),
			'making_changes_will_probably_overwrite_advanced' => __('Making changes to these settings will probably overwrite advanced settings. Continue?'),
			'select_static_layer_image' => __('Select Static Layer Image'),
			'select_layer_image' => __('Select Layer Image'),
			'really_want_to_delete_all_layer' => __('Do you really want to delete all the layers?'),
			'layer_animation_editor' => __('Layer Animation Editor'),
			'animation_exists_overwrite' => __('Animation already exists, overwrite?'),
			'really_overwrite_animation' => __('Really overwrite animation?'),
			'default_animations_cant_delete' => __('Default animations can\'t be deleted'),
			'must_be_greater_than_start_time' => __('Must be greater than start time'),
			'sel_layer_not_set' => __('Selected layer not set'),
			'edit_layer_start' => __('Edit Layer Start'),
			'edit_layer_end' => __('Edit Layer End'),
			'default_animations_cant_rename' => __('Default Animations can\'t be renamed'),
			'anim_name_already_exists' => __('Animationname already existing'),
			'css_name_already_exists' => __('CSS classname already existing'),
			'css_orig_name_does_not_exists' => __('Original CSS classname not found'),
			'enter_correct_class_name' => __('Enter a correct class name'),
			'class_not_found' => __('Class not found in database'),
			'css_name_does_not_exists' => __('CSS classname not found'),
			'delete_this_caption' => __('Delete this caption? This may affect other Slider'),
			'this_will_change_the_class' => __('This will update the Class with the current set Style settings, this may affect other Sliders. Proceed?'),
			'unsaved_changes_will_not_be_added' => __('Template will have the state of the last save, proceed?'),
			'please_enter_a_slide_title' => __('Please enter a Slide title'),
			'please_wait_a_moment' => __('Please Wait a Moment'),
			'copy_move' => __('Copy / Move'),
			'preset_loaded' => __('Preset Loaded'),
			'add_bulk_slides' => __('Add Bulk Slides'),
			'select_image' => __('Select Image'),
			'arrows' => __('Arrows'),
			'bullets' => __('Bullets'),
			'thumbnails' => __('Thumbnails'),
			'tabs' => __('Tabs'),
			'delete_navigation' => __('Delete this Navigation?'),
			'could_not_update_nav_name' => __('Navigation name could not be updated'),
			'name_too_short_sanitize_3' => __('Name too short, at least 3 letters between a-zA-z needed'),
			'nav_name_already_exists' => __('Navigation name already exists, please choose a different name'),
			'remove_nav_element' => __('Remove current element from Navigation?'),
			'create_this_nav_element' => __('This navigation element does not exist, create one?'),
			'overwrite_animation' => __('Overwrite current animation?'),
			'cant_modify_default_anims' => __('Default animations can\'t be changed'),
			'anim_with_handle_exists' => __('Animation already existing with given handle, please choose a different name.'),
			'really_delete_anim' => __('Really delete animation:'),
			'this_will_reset_navigation' => __('This will reset the navigation, continue?'),
			'preset_name_already_exists' => __('Preset name already exists, please choose a different name'),
			'delete_preset' => __('Really delete this preset?'),
			'update_preset' => __('This will update the preset with the current settings. Proceed?'),
			'maybe_wrong_yt_id' => __('No Thumbnail Image Set on Video / Video Not Found / No Valid Video ID'),
			'preset_not_found' => __('Preset not found'),
			'cover_image_needs_to_be_set' => __('Cover Image need to be set for videos'),
			'remove_this_action' => __('Really remove this action?'),
			'layer_action_by' => __('Layer is triggered by '),
			'due_to_action' => __(' due to action: '),
			'layer' => __('layer:'),
			'start_layer_in' => __('Start Layer "in" animation'),
			'start_layer_out' => __('Start Layer "out" animation'),
            'start_video' => __('Start Media'),
            'stop_video' => __('Stop Media'),
            'mute_video' => __('Mute Media'),
            'unmute_video' => __('Unmute Media'),
			'toggle_layer_anim' => __('Toggle Layer Animation'),
            'toggle_video' => __('Toggle Media'),
            'toggle_mute_video' => __('Toggle Mute Media'),
            'toggle_global_mute_video' => __('Toggle Mute All Media'),
			'last_slide' => __('Last Slide'),
			'simulate_click' => __('Simulate Click'),
			'togglefullscreen' => __('Toggle FullScreen'),
			'gofullscreen' => __('Go FullScreen'),
			'exitfullscreen' => __('Exit FullScreen'),
			'toggle_class' => __('Toogle Class'),
			'copy_styles_to_hover_from_idle' => __('Copy hover styles to idle?'),
			'copy_styles_to_idle_from_hover' => __('Copy idle styles to hover?'),
			'select_at_least_one_device_type' => __('Please select at least one device type'),
			'please_select_first_an_existing_style' => __('Please select an existing Style Template'),
			'cant_remove_last_transition' => __('Can not remove last transition!'),
			'name_is_default_animations_cant_be_changed' => __('Given animation name is a default animation. These can not be changed.'),
			'override_animation' => __('Animation exists, override existing animation?'),
			'this_feature_only_if_activated' => __('This feature is only available if you activate Slider Revolution for this installation'),
			'unsaved_data_will_be_lost_proceed' => __('Unsaved data will be lost, proceed?'),
			'delete_user_slide' => __('This will delete this Slide Template, proceed?'),
			'is_loading' => __('is Loading...'),
			'google_fonts_loaded' => __('Google Fonts Loaded'),
			'delete_layer' => __('Delete Layer?'),
			'this_template_requires_version' => __('This template requires at least version'),
            'of_slider_revolution' => __('of Slider Revolution to work.'),
            'slider_revolution_shortcode_creator' => __('Slider Revolution Shortcode Creator'),
            'slider_informations_are_missing' => __('Slider informations are missing!'),
            'shortcode_generator' => __('Shortcode Generator'),
            'please_add_at_least_one_layer' => __('Please add at least one Layer.'),
            'choose_image' => __('Choose Image'),
            'shortcode_parsing_successfull' => __('Shortcode parsing successfull. Items can be found in step 3'),
            'shortcode_could_not_be_correctly_parsed' => __('Shortcode could not be parsed.'),
            'background_video' => __('Background Video'),
            'active_video' => __('Video in Active Slide'),
            'empty_data_retrieved_for_slider' => __('Data could not be fetched for selected Slider'),
            'import_selected_layer' => __('Import Selected Layer?'),
            'import_all_layer_from_actions' => __('Layer Imported! The Layer has actions which include other Layers. Import all connected layers?'),
            'not_available_in_demo' => __('Not available in Demo Mode'),
            'leave_not_saved' => __('By leaving now, all changes since the last saving will be lost. Really leave now?'),
			'static_layers' => __('--- Static Layers ---'),
            'objects_only_available_if_activated' => __('Only available if plugin is activated'),
            'download_install_takes_longer' => __('Download/Install takes longer than usual, please wait'),
            'download_failed_check_server' => __('<div class="import_failure">Download/Install seems to have failed.</div><br>Please check your server <span class="import_failure">download speed</span> and  if the server can programatically connect to <span class="import_failure">http://templates.themepunch.com</span><br><br>'),
            'aborting_import' => __('<b>Aborting Import...</b>'),
            'create_draft' => __('Creating Draft Page...'),
            'draft_created' => __('Draft Page created. Popup will open'),
            'draft_not_created' => __('Draft Page could not be created.'),
            'slider_import_success_reload' => __('Slider import successful'),
            'save_changes' => __('Save Changes?')
		);

		return $lang;
	}


	public function addActivateNotification(){
		$nonce = $this->_framework->wp_create_nonce("revslider_actions");
		?>
		<div class="updated below-h2 rs-update-notice-wrap" id="message"><a href="javascript:void(0);" style="float: right;padding-top: 9px;" id="rs-dismiss-notice"><?php echo __( '(never show this message again)&nbsp;&nbsp;<b>X</b>'); ?></a><p><?php echo __( 'Hi! Would you like to activate your version of Revolution Slider to receive live updates & get premium support? This is optional and not needed if the slider came bundled with a theme. '); ?></p></div>
		<script type="text/javascript">
			require(['jquery'], function(jQuery) {
				jQuery('#rs-dismiss-notice').click(function () {
					var objData = {
						action: "revslider_ajax_action",
						client_action: 'dismiss_notice',
						nonce: '<?php echo $nonce; ?>',
						data: ''
					};

					jQuery.ajax({
						type: "post",
						url: ajaxurl,
						dataType: 'json',
						data: objData
					});

					jQuery('.rs-update-notice-wrap').hide();
				});
			});
		</script>
		<?php
	}


	/**
	 * add notices from ThemePunch
	 * @since: 4.6.8
	 */
	public function add_notices(){
		$operations = new RevSliderOperations($this->_framework, $this->_query, $this->_curl, $this->_filesystem, $this->_images, $this->_resource, $this->_googleFonts, $this->_registerHelper);
		$general_settings = $operations->getGeneralSettingsValues();

		//check permissions here
		if(!$this->_framework->current_user_can('administrator')) return true;

		$enable_newschannel = RevSliderBase::getVar($general_settings, 'enable_newschannel', 'on');

		$enable_newschannel = $this->_framework->apply_filters('revslider_set_notifications', $enable_newschannel);

		if($enable_newschannel == 'on'){

			$nonce = $this->_framework->wp_create_nonce("revslider_actions");

			$notices = $this->_framework->get_option('revslider-notices', false);

            $vn = $this->_framework->get_option('revslider-deact-notice', false);

            if($vn == true){
                $notices[99999] = new \stdClass();
                $notices[99999]->code = 'DISCARD';
                $notices[99999]->version = '99.99';
                $notices[99999]->is_global = false;
                $notices[99999]->color = 'error';
				$notices[99999]->text = '<p>'.__('Hi, your Purchase Code for Slider Revolution has been deregistered manually through <a href="https://www.themepunch.com/purchase-code-deactivation" target="_blank">https://www.themepunch.com/purchase-code-deactivation</a> and is no longer active for this installation.').'</p>';
                $notices[99999]->disable = false;
                $notices[99999]->additional = array();
            }

            $tmp = $this->_framework->get_option('revslider-temp-active-notice', 'false');

            if($tmp == 'true'){
                $notices[99998] = new \stdClass();
                $notices[99998]->code = 'DISCARDTEMPACT';
                $notices[99998]->version = '99.99';
                $notices[99998]->is_global = false;
                $notices[99998]->color = 'error';
                $notices[99998]->text = '<p>'.__('Hi, your Purchase Code for Slider Revolution has been deregistered as it was not a valid Slider Revolution Purchase Code').'</p>';
                $notices[99998]->disable = false;
                $notices[99998]->additional = array();
            }

			if(!empty($notices) && is_array($notices)){
				global $revslider_screens;

				$notices_discarded = $this->_framework->get_option('revslider-notices-dc', array());

				$screen = $this->_framework->get_current_screen();
				foreach($notices as $notice){
                    if($notice->is_global !== true && !in_array($screen->id, $revslider_screens)) continue; //check if global or just on plugin related pages

                    if(!in_array($notice->code, $notices_discarded) && version_compare($notice->version, RevSliderGlobals::SLIDER_REVISION, '>=')){
                        $text = '<div style="text-align:right;vertical-align:middle;display:table-cell; min-width:225px;border-left:1px solid #ddd; padding-left:15px;"><a href="javascript:void(0);"  class="rs-notices-button rs-notice-'. $this->_framework->esc_attr($notice->code) .'">'. __('Close & don\'t show again<b>X</b>') .'</a></div>';
                        if($notice->disable == true) $text = '';
                        ?>
                            <style>
                        .rs-notices-button            {    color:#999; text-decoration: none !important; font-size:14px;font-weight: 400;}
                        .rs-notices-button:hover     {    color:#3498DB !important;}

                        .rs-notices-button b         {    font-weight:800; vertical-align:bottom;line-height:15px;font-size:10px;margin-left:10px;margin-right:10px;border:2px solid #999; display:inline-block; width:15px;height:15px; text-align: center; border-radius: 50%; -webkit-border-radius: 50%; -moz-border-radius: 50%;}
                        .rs-notices-button:hover b  {     border-color:#3498DB;}
                        </style>
                        <div class="<?php echo $notice->color; ?> below-h2 rs-update-notice-wrap" id="message" style="clear:both;display: block;position:relative;margin:35px 20px 25px 0px"><div style="display:table;width:100%;"><div style="vertical-align:middle;display:table-cell;min-width:100%;padding-right:15px;"><?php echo $notice->text; ?></div><?php echo $text; ?></div></div>

                        <?php
					}
                }
				?>
				<script type="text/javascript">
                    require(['jquery'], function(jQuery) {
                        jQuery('.rs-notices-button').click(function () {

                            var notice_id = jQuery(this).attr('class').replace('rs-notices-button', '').replace('rs-notice-', '');

                            var objData = {
                                action: "revslider_ajax_action",
                                client_action: 'dismiss_dynamic_notice',
                                nonce: '<?php echo $nonce; ?>',
                                data: {'id': notice_id}
                            };

                            jQuery.ajax({
                                type: "post",
                                url: ajaxurl,
                                dataType: 'json',
                                data: objData
                            });

                            jQuery(this).closest('.rs-update-notice-wrap').slideUp(200);
                        });
                    });
				</script>
				<?php
			}
		}
	}


	/**
	 *
	 * add wildcards metabox variables to posts
	 */
	private function addSliderMetaBox($postTypes = null){ //null = all, post = only posts
		try{
			self::addMetaBox("Revolution Slider Options",'',array("RevSliderAdmin","customPostFieldsOutput"),$postTypes);
		}catch(\Exception $e){
            Data::logException($e);
        }
	}


	/**
	 *  custom output function
	 */
	public static function customPostFieldsOutput(){

		$meta = self::$framework->get_post_meta(get_the_ID(), 'slide_template', true);
		if($meta == '') $meta = 'default';

		$slider = new RevSliderSlider(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);
		$arrOutput = array();
		$arrOutput["default"] = "default";

		$arrSlides = $slider->getArrSlidersWithSlidesShort(RevSliderSlider::SLIDER_TYPE_TEMPLATE);
		$arrOutput = $arrOutput + $arrSlides;	//union arrays

		?>
		<ul class="revslider_settings">
			<li id="slide_template_row">
				<div title="" class="setting_text" id="slide_template_text"><?php echo __( 'Choose Slide Template'); ?></div>
				<div class="setting_input">
					<select name="slide_template" id="slide_template">
						<?php
						foreach($arrOutput as $handle => $name){
							echo '<option '.self::$framework->selected($handle, $meta).' value="'.$handle.'">'.$name.'</option>';
						}
						?>
					</select>
				</div>
				<div class="clear"></div>
			</li>
		</ul>
		<?php
	}


	/**
	 * a must function. please don't remove it.
	 * process activate event - install the db (with delta).
	 */
	public static function onActivate(){
		RevSliderFront::createDBTables();
	}


	/**
	 * a must function. adds scripts on the page
	 * add all page scripts and styles here.
	 * pelase don't remove this function
	 * common scripts even if the plugin not load, use this function only if no choise.
	 */
	public static function onAddScripts(){

		self::$framework->wp_enqueue_style('edit_layers', Framework::$RS_PLUGIN_URL .'admin/assets/css/edit_layers.min.css', array(), RevSliderGlobals::SLIDER_REVISION);

		self::$framework->wp_enqueue_script('unite_layers_timeline', Framework::$RS_PLUGIN_URL .'admin/assets/js/edit_layers_timeline.js', array(), RevSliderGlobals::SLIDER_REVISION );
		self::$framework->wp_enqueue_script('unite_context_menu', Framework::$RS_PLUGIN_URL .'admin/assets/js/context_menu.js', array(), RevSliderGlobals::SLIDER_REVISION );
		self::$framework->wp_enqueue_script('unite_layers', Framework::$RS_PLUGIN_URL .'admin/assets/js/edit_layers.js', array('jquery-ui-mouse'), RevSliderGlobals::SLIDER_REVISION );
		self::$framework->wp_enqueue_script('unite_css_editor', Framework::$RS_PLUGIN_URL .'admin/assets/js/css_editor.js', array(), RevSliderGlobals::SLIDER_REVISION );
		self::$framework->wp_enqueue_script('revAdmin', Framework::$RS_PLUGIN_URL .'admin/assets/js/rev_admin.js', array(), RevSliderGlobals::SLIDER_REVISION );

		self::$framework->wp_enqueue_script('tp-tools', Framework::$RS_PLUGIN_URL .'public/assets/js/jquery.themepunch.tools.min.js', array(), RevSliderGlobals::SLIDER_REVISION );

		//include all media upload scripts
		self::addMediaUploadIncludes();

		//add rs css:
		self::$framework->wp_enqueue_style('rs-plugin-settings', Framework::$RS_PLUGIN_URL .'public/assets/css/settings.css', array(), RevSliderGlobals::SLIDER_REVISION);

		//add icon sets
		self::$framework->wp_enqueue_style('rs-icon-set-fa-icon-', Framework::$RS_PLUGIN_URL .'public/assets/fonts/font-awesome/css/font-awesome.min.css', array(), RevSliderGlobals::SLIDER_REVISION);
		self::$framework->wp_enqueue_style('rs-icon-set-pe-7s-', Framework::$RS_PLUGIN_URL .'public/assets/fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.min.css', array(), RevSliderGlobals::SLIDER_REVISION);

		self::$framework->add_filter('revslider_mod_icon_sets', array('\Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderBase', 'set_icon_sets'));

		$db = new RevSliderDB(self::$query, self::$resource);

		$styles = $db->fetch(RevSliderGlobals::$table_css);
		$styles = RevSliderCssParser::parseDbArrayToCss($styles, "\n");
		$styles = RevSliderCssParser::compress_css($styles);
		self::$framework->wp_add_inline_style( 'rs-plugin-settings', '<style type="text/css" id="rs-inline-captions-css">'.$styles.'</style>' );

		$custom_css = RevSliderOperations::getStaticCss();
		$custom_css = RevSliderCssParser::compress_css($custom_css);
		self::$framework->wp_add_inline_style( 'rs-plugin-settings', '<style type="text/css" id="rs-inline-static-css">'.$custom_css.'</style>' );

	}


	/**
	 *
	 * admin main page function.
	 */
	public static function adminPages(){

		parent::adminPages();

		self::setMasterView('master-view');
		self::requireView(self::$view);

	}


	/**
	 *
	 * import slideer handle (not ajax response)
	 */
    private static function importSliderHandle($viewBack = null, $updateAnim = true, $updateStatic = true, $updateNavigation = true){

		$slider = new RevSliderSlider(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);
        $response = $slider->importSliderFromPost($updateAnim, $updateStatic, false, false, false, $updateNavigation);

        $sliderID = intval($response["sliderID"]);

		if(empty($viewBack)){
			$viewBack = self::getViewUrl(self::VIEW_SLIDER,"id=".$sliderID);
			if(empty($sliderID))
				$viewBack = self::getViewUrl(self::VIEW_SLIDERS);
		}

		//handle error this
		if($response["success"] == false){
			$message = $response["error"];
			RevSliderOperations::import_failed_message($message, $viewBack);

		}else{    //handle success, js redirect.
			//check here to create a page or not
			if(!empty($sliderID)){
				$page_creation = self::$framework->esc_attr(RevSliderFunctions::getPostVariable('page-creation'));
				if($page_creation === 'true'){
					$operations = new RevSliderOperations(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);
					$pageUrl = $operations->create_slider_page((array)$sliderID);
					if ($pageUrl) {
						echo "<script>window.open('$pageUrl', '_blank');</script>";
					}
				}
			}
			echo "<script>location.href='".$viewBack."';</script>";
		}
		exit();
	}


	/**
	 * import slider from TP servers
	 * @since: 5.0.5
	 */
	private static function importSliderOnlineTemplateHandleNew($data, $viewBack = null, $updateAnim = true, $updateStatic = true, $single_slide = false){

		$return = array('error' => array(), 'success' => array(), 'open' => false, 'view' => $viewBack);

		$uid = self::$framework->esc_attr($data['uid']);

		$added = array();

		if($uid == ''){
			$return['error'][] = __("ID missing, something went wrong. Please try again!");
		}else{
			$tmp = new RevSliderTemplate(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);

			$package = isset($data['package']) ? self::$framework->esc_attr($data['package']) : false;
			$package = ($package == 'true') ? true : false;

			//get all in the same package as the uid
			if($package === true){
				$uids = $tmp->get_package_uids($uid);
			}else{
				$uids = (array)$uid;
			}

			if(!empty($uids)){
				foreach($uids as $uid){
					set_time_limit(60); //reset the time limit

					$filepath = $tmp->_download_template($uid); //can be single or multiple, depending on $package beeing false or true

					//send request to TP server and download file
					if(is_array($filepath) && isset($filepath['error'])){
						$return['error'][] = $filepath['error'];
						break;
					}

					if($filepath !== false){
						//check if Slider Template was already imported. If yes, remove the old Slider Template as we now do an "update" (in reality we delete and insert again)
						//get all template sliders
						$tmp_slider = $tmp->getThemePunchTemplateSliders();

						foreach($tmp_slider as $tslider){
							if(isset($tslider['uid']) && $uid == $tslider['uid']){
								if(!isset($tslider['installed'])){ //slider is installed
									//delete template Slider!
									$mSlider = new RevSliderSlider(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);
									$mSlider->initByID($tslider['id']);

									$mSlider->deleteSlider();
									//remove the update flag from the slider

									$tmp->remove_is_new($uid);
								}
								break;
							}
						}


						$slider = new RevSliderSlider(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);
						$response = $slider->importSliderFromPost($updateAnim, $updateStatic, $filepath, $uid, $single_slide);

						$tmp->_delete_template($uid);

						if($single_slide === false){
							if(empty($viewBack)){
								$sliderID = $response["sliderID"];
								$viewBack = self::getViewUrl(self::VIEW_SLIDER,"id=".$sliderID);
								$return['view'] = $viewBack;
								if(empty($sliderID)){
									$viewBack = self::getViewUrl(self::VIEW_SLIDERS);
									$return['view'] = $viewBack;
								}
							}
						}

						if(isset($response["sliderID"])){
							$return['slider_id'] = $response["sliderID"];
							$added[] = $response["sliderID"];
						}
						//handle error
						if($response["success"] == false){
							$return['error'][] = $response["error"];
							break;
						}else{    //handle success, js redirect.
							$return['success'][] = __("Slider Import Success");
						}

					}else{
						if(is_array($filepath)){
							$return['error'][] = $filepath['error'];
						}else{
							$return['error'][] = __("Could not download from server. Please try again later!");
						}
						break;
					}
				}

				//check here to create a page or not
				if(!empty($added)){
					$page_creation = self::$framework->esc_attr($data['page-creation']);
					if($page_creation === 'true'){
						$operations = new RevSliderOperations(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);
						$page_url = $operations->create_slider_page($added);
						if($page_url) {
							$return['open'] = $page_url;
						}
					}
				}
			}else{
				$return['error'][] = __("Could not download package. Please try again later!");
			}
		}

		return $return;
	}


	/**
	 * import slider from TP servers
	 * @since: 5.0.5
	 */
	private static function importSliderOnlineTemplateHandle($data, $viewBack = null, $updateAnim = true, $updateStatic = true, $single_slide = false){

		$uid = self::$framework->esc_attr($data['uid']);

		$added = array();

		if($uid == ''){
			$message = __("ID missing, something went wrong. Please try again!");
			RevSliderOperations::import_failed_message($message, $viewBack);
			exit;
		}else{
			$tmp = new RevSliderTemplate(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);

			$package = self::$framework->esc_attr($data['package']);
            $package = ($package == 'true') ? true : false;

            //get all in the same package as the uid
            if($package === true){
                $uids = $tmp->get_package_uids($uid);
            }else{
                $uids = (array)$uid;
            }

            if(!empty($uids)){
                foreach($uids as $uid){
                    set_time_limit(60); //reset the time limit

                    $filepath = $tmp->_download_template($uid); //can be single or multiple, depending on $package beeing false or true

                    //send request to TP server and download file
                    if(is_array($filepath) && isset($filepath['error'])){
						$message = $filepath['error'];
						RevSliderOperations::import_failed_message($message, $viewBack);
                        exit;
					}

                    if($filepath !== false){
                        //check if Slider Template was already imported. If yes, remove the old Slider Template as we now do an "update" (in reality we delete and insert again)
                        //get all template sliders
                        $tmp_slider = $tmp->getThemePunchTemplateSliders();

                        foreach($tmp_slider as $tslider){
                            if(isset($tslider['uid']) && $uid == $tslider['uid']){
                                if(!isset($tslider['installed'])){ //slider is installed
                                    //delete template Slider!
                                    $mSlider = new RevSliderSlider(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);
                                    $mSlider->initByID($tslider['id']);

                                    $mSlider->deleteSlider();
                                    //remove the update flag from the slider

                                    $tmp->remove_is_new($uid);
                                }
                                break;
                            }
                        }


                        $slider = new RevSliderSlider(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);
                        $response = $slider->importSliderFromPost($updateAnim, $updateStatic, $filepath, $uid, $single_slide);

                        $tmp->_delete_template($uid);

                        if($single_slide === false){
                            if(empty($viewBack)){
                                $sliderID = $response["sliderID"];
                                $viewBack = self::getViewUrl(self::VIEW_SLIDER,"id=".$sliderID);
                                if(empty($sliderID))
                                    $viewBack = self::getViewUrl(self::VIEW_SLIDERS);
                            }
                        }

						if(isset($response["sliderID"])){
							$added[] = $response["sliderID"];
						}
                        //handle error
                        if($response["success"] == false){
                            $message = $response["error"];
							RevSliderOperations::import_failed_message($message, $viewBack);
                        }
                    }else{
                        if(is_array($filepath)){
							$message = $filepath['error'];
                        }else{
							$message = __("Could not download from server. Please try again later!");
                        }
						RevSliderOperations::import_failed_message($message, $viewBack);
                        exit;
                    }
				}

				//check here to create a page or not
				if(!empty($added)){
					$page_creation = self::$framework->esc_attr($data['page-creation']);
					if($page_creation === 'true'){
						$operations = new RevSliderOperations(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);
						$pageUrl = $operations->create_slider_page($added);
						if ($pageUrl) {
							echo "<script>window.open('$pageUrl', '_blank');</script>";
						}
					}
				}

				echo "<script>location.href='".$viewBack."';</script>";
			}else{
				$message = __("Could not download package. Please try again later!");
				RevSliderOperations::import_failed_message($message, $viewBack);
				exit;
			}
		}

		exit;
	}


	/**
	 *
	 * import slider handle (not ajax response)
	 */
	private static function importSliderTemplateHandle($viewBack = null, $updateAnim = true, $updateStatic = true, $single_slide = false){

		$uid = self::$framework->esc_attr(RevSliderFunctions::getPostVariable('uid'));
		if($uid == ''){
			$message = __("ID missing, something went wrong. Please try again!");
			RevSliderOperations::import_failed_message($message, $viewBack);
			exit;
		}

		//check if the filename is correct
		//import to templates, then duplicate Slider

		$slider = new RevSliderSlider(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);
		$response = $slider->importSliderFromPost($updateAnim, $updateStatic, false, $uid, $single_slide);

		if($single_slide === false){
			$sliderID = $response["sliderID"];
			if(empty($viewBack)){
				$viewBack = self::getViewUrl(self::VIEW_SLIDER,"id=".$sliderID);
				if(empty($sliderID))
					$viewBack = self::getViewUrl(self::VIEW_SLIDERS);
			}
		}

		//handle error
		if($response["success"] == false){
			$message = $response["error"];
			RevSliderOperations::import_failed_message($message, $viewBack);
		}else{	//handle success, js redirect.
			//check here to create a page or not
			if(isset($sliderID) && !empty($sliderID)){
				$page_creation = self::$framework->esc_attr(RevSliderFunctions::getPostVariable('page-creation'));
				if($page_creation === 'true'){
					$operations = new RevSliderOperations(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);
					$pageUrl = $operations->create_slider_page((array)$sliderID);
					if ($pageUrl) {
						echo "<script>window.open('$pageUrl', '_blank');</script>";
					}
				}
			}

			echo "<script>location.href='".$viewBack."';</script>";
		}
	}

	/**
	 * Get url to secific view.
	 */
	public static function getFontsUrl(){

		$link = self::$framework->admin_url('admin.php?page=themepunch-google-fonts');
		return($link);
	}


	/**
	 * Toggle Favorite State of Slider
	 * @since: 5.0
	 */
	public static function toggle_favorite_by_id($id){
		$id = intval($id);
		if($id === 0) return false;

		$wpdb = self::$query;

		$table_name = $wpdb->prefix . RevSliderGlobals::TABLE_SLIDERS_NAME;

		//check if ID exists
		$slider = $wpdb->get_row($wpdb->prepare("SELECT settings FROM $table_name WHERE id = %s", $id), Query::ARRAY_A);

		if(empty($slider))
			return __('Slider not found');

		$settings = json_decode($slider['settings'], true);

		if(!isset($settings['favorite']) || $settings['favorite'] == 'false' || $settings['favorite'] == false){
			$settings['favorite'] = 'true';
		}else{
			$settings['favorite'] = 'false';
		}

		$response = $wpdb->update($table_name, array('settings' => json_encode($settings)), array('id' => $id));

		if($response === false) return __('Slider setting could not be changed');

		return true;
	}

	/**
	 *
	 * onAjax action handler
	 *
	 * @param	bool	Is ajax request
	 */
	public static function onAjaxAction($isAjax = true) {

		$slider = new RevSliderSlider(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);
		$slide = new RevSliderSlide(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);
		$operations = new RevSliderOperations(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);

		$action = self::getPostGetVar("client_action");
		$data = self::getPostGetVar("data", array());
		$nonce = self::getPostGetVar("nonce");
		if(empty($nonce))
			$nonce = self::getPostGetVar("rs-nonce");

		try{

            if(Framework::RS_DEMO){
                switch($action){
                    case 'import_slider_online_template_slidersview':
                    case 'duplicate_slider':
                    case 'preview_slider':
                    case 'get_static_css':
                    case 'get_dynamic_css':
                    case 'preview_slide':
                        //these are all okay in demo mode
                    break;
                    default:
                        RevSliderFunctions::throwError(__('Function Not Available in Demo Mode'));
                        exit;
                    break;
                }
            }

            if(!RevSliderFunctionsWP::isAdminUser() && self::$framework->apply_filters('revslider_restrict_role', true)){
                switch($action){
                    case 'change_specific_navigation':
                    case 'change_navigations':
                    case 'update_static_css':
                    case 'add_new_preset':
                    case 'update_preset':
                    case 'import_slider':
                    case 'import_slider_slidersview':
                    case 'import_slider_template_slidersview':
                    case 'import_slide_template_slidersview':
					case 'import_slider_online_template_slidersview_new':
                    case 'fix_database_issues':
                    case 'trigger_font_deletion':
						RevSliderFunctions::throwError(__('Function Only Available for Adminstrators'));
                        exit;
                    break;
                    default:
                        $return = self::$framework->apply_filters('revslider_admin_onAjaxAction_user_restriction', true, $action, $data, $slider, $slide, $operations);
                        if($return !== true){
                            RevSliderFunctions::throwError(__('Function Only Available for Adminstrators'));
                            exit;
                        }
                    break;
                }
            }

			//verify the nonce
			$isVerified = self::$framework->wp_verify_nonce($nonce, "revslider_actions");

			if($isVerified == false){
				RevSliderFunctions::throwError("Wrong request");
				exit;
			}

			switch($action){
				case 'add_new_preset':

					if(!isset($data['settings']) || !isset($data['values'])) self::ajaxResponseError(__('Missing values to add preset'), false);

					$result = $operations->add_preset_setting($data);

					if($result === true){

						$presets = $operations->get_preset_settings();

						self::ajaxResponseSuccess(__('Preset created'), array('data' => $presets));
					}else{
						self::ajaxResponseError($result, false);
					}

					exit;
				break;
				case 'update_preset':

					if(!isset($data['name']) || !isset($data['values'])) self::ajaxResponseError(__('Missing values to update preset'), false);

					$result = $operations->update_preset_setting($data);

					if($result === true){

						$presets = $operations->get_preset_settings();

						self::ajaxResponseSuccess(__('Preset created'), array('data' => $presets));
					}else{
						self::ajaxResponseError($result, false);
					}

					exit;
				break;
				case 'remove_preset':

					if(!isset($data['name'])) self::ajaxResponseError(__('Missing values to remove preset'), false);

					$result = $operations->remove_preset_setting($data);

					if($result === true){

						$presets = $operations->get_preset_settings();

						self::ajaxResponseSuccess(__('Preset deleted'), array('data' => $presets));
					}else{
						self::ajaxResponseError($result, false);
					}

					exit;
				break;
				case "export_slider":
					$sliderID = self::getGetVar("sliderid");
					$dummy = self::getGetVar("dummy");
					$slider->initByID($sliderID);
					$slider->exportSlider($dummy);
				break;
				case "import_slider":
					$updateAnim = self::getPostGetVar("update_animations");
                    $updateNav = self::getPostGetVar("update_navigations");
					$updateStatic = 'none';
                    self::importSliderHandle(null, $updateAnim, $updateStatic, $updateNav);
				break;
				case "import_slider_slidersview":
					$viewBack = self::getViewUrl(self::VIEW_SLIDERS);
					$updateAnim = self::getPostGetVar("update_animations");
                    $updateNav = self::getPostGetVar("update_navigations");
					$updateStatic = 'none';
                    self::importSliderHandle($viewBack, $updateAnim, $updateStatic, $updateNav);
				break;
				case "import_slider_online_template_slidersview":
					$viewBack = self::getViewUrl(self::VIEW_SLIDERS);
					$data['uid'] = self::$framework->esc_attr(RevSliderFunctions::getPostVariable('uid'));
					$data['page-creation'] = self::$framework->esc_attr(RevSliderFunctions::getPostVariable('page-creation'));
					$data['package'] = self::$framework->esc_attr(RevSliderFunctions::getPostVariable('package'));

					self::importSliderOnlineTemplateHandle($data, $viewBack, 'true', 'none');
					break;
				case "import_slider_template_slidersview":
                    $viewBack = self::getViewUrl(self::VIEW_SLIDERS);
                    $updateAnim = self::getPostGetVar("update_animations");
                    $updateStatic = 'none';
                    self::importSliderTemplateHandle($viewBack, $updateAnim, $updateStatic);
                break;
                case "import_slider_online_template_slidersview_new":
                    $viewBack = self::getViewUrl(self::VIEW_SLIDERS);
                    $response = self::importSliderOnlineTemplateHandleNew($data, $viewBack, 'true', 'none');
                    self::ajaxResponseData($response);
                break;
                case 'create_draft_page':
                    $response = array('open' => false);

                    $page_url = $operations->create_slider_page($data['slider_ids']);
                    if($page_url){
						$response['open'] = $page_url;
					}
                    self::ajaxResponseData($response);
				break;
				case "import_slide_online_template_slidersview":
					$redirect_id = self::$framework->esc_attr(self::getPostGetVar("redirect_id"));
					$viewBack = self::getViewUrl(self::VIEW_SLIDE,"id=$redirect_id");
					$slidenum = intval(self::getPostGetVar("slidenum"));
					$sliderid = intval(self::getPostGetVar("slider_id"));

					$data['uid'] = self::$framework->esc_attr(RevSliderFunctions::getPostVariable('uid'));
					$data['page-creation'] = self::$framework->esc_attr(RevSliderFunctions::getPostVariable('page-creation'));
					$data['package'] = self::$framework->esc_attr(RevSliderFunctions::getPostVariable('package'));

					self::importSliderOnlineTemplateHandle($data, $viewBack, 'true', 'none', array('slider_id' => $sliderid, 'slide_id' => $slidenum));
				break;
				case "import_slide_template_slidersview":
					$redirect_id = self::$framework->esc_attr(self::getPostGetVar("redirect_id"));
					$viewBack = self::getViewUrl(self::VIEW_SLIDE,"id=$redirect_id");
					$updateAnim = self::getPostGetVar("update_animations");
					$updateStatic = 'none';
					$slidenum = intval(self::getPostGetVar("slidenum"));
					$sliderid = intval(self::getPostGetVar("slider_id"));

					self::importSliderTemplateHandle($viewBack, $updateAnim, $updateStatic, array('slider_id' => $sliderid, 'slide_id' => $slidenum));
				break;
				case "create_slider":
					$data = $operations->modifyCustomSliderParams($data);
					$newSliderID = $slider->createSliderFromOptions($data);
					self::ajaxResponseSuccessRedirect(__("Slider created"), self::getViewUrl(self::VIEW_SLIDE, 'id=new&slider='.self::$framework->esc_attr($newSliderID))); //redirect to slide now

				break;
				case "update_slider":
					$data = $operations->modifyCustomSliderParams($data);
					$slider->updateSliderFromOptions($data);
					self::ajaxResponseSuccess(__("Slider updated"));
				break;
				case "delete_slider":
				case "delete_slider_stay":

					$isDeleted = $slider->deleteSliderFromData($data);

					if(is_array($isDeleted)){
						$isDeleted = implode(', ', $isDeleted);
						self::ajaxResponseError(__("Template can't be deleted, it is still being used by the following Sliders: ").$isDeleted);
					}else{
						if($action == 'delete_slider_stay'){
							self::ajaxResponseSuccess(__("Slider deleted"));
						}else{
							self::ajaxResponseSuccessRedirect(__("Slider deleted"), self::getViewUrl(self::VIEW_SLIDERS));
						}
					}
				break;
				case "duplicate_slider":

					$slider->duplicateSliderFromData($data);

					self::ajaxResponseSuccessRedirect(__("Success! Refreshing page..."), self::getViewUrl(self::VIEW_SLIDERS));
				break;
                case "duplicate_slider_package":

                    $ret = $slider->duplicateSliderPackageFromData($data);

                    if($ret !== true){
                        RevSliderFunctions::throwError($ret);
                    }else{
                        self::ajaxResponseSuccessRedirect(__("Success! Refreshing page..."), self::getViewUrl(self::VIEW_SLIDERS));
                    }
                break;
				case "add_slide":
				case "add_bulk_slide":
					$numSlides = $slider->createSlideFromData($data);
					$sliderID = $data["sliderid"];

					if($numSlides == 1){
						$responseText = __("Slide Created");
					}else{
						$responseText = $numSlides . " ".__("Slides Created");
					}

					$urlRedirect = self::getViewUrl(self::VIEW_SLIDE,"id=new&slider=$sliderID");
					self::ajaxResponseSuccessRedirect($responseText,$urlRedirect);

				break;
				case "add_slide_fromslideview":
					$slideID = $slider->createSlideFromData($data,true);
					$urlRedirect = self::getViewUrl(self::VIEW_SLIDE,"id=$slideID");
					$responseText = __("Slide Created, redirecting...");
					self::ajaxResponseSuccessRedirect($responseText,$urlRedirect);
				break;
				case 'copy_slide_to_slider':
					$slideID = (isset($data['redirect_id'])) ? $data['redirect_id'] : -1;

					if($slideID === -1) RevSliderFunctions::throwError(__('Missing redirect ID!'));

					$return = $slider->copySlideToSlider($data);

					if($return !== true) RevSliderFunctions::throwError($return);

					$urlRedirect = self::getViewUrl(self::VIEW_SLIDE,"id=$slideID");
					$responseText = __("Slide copied to current Slider, redirecting...");
					self::ajaxResponseSuccessRedirect($responseText,$urlRedirect);
				break;
                case 'update_slide':
					if(isset($data['obj_favorites'])){
						$obj_favorites = $data['obj_favorites'];
						unset($data['obj_favorites']);
						//save object favourites
						$objlib = new RevSliderObjectLibrary(self::$filesystem, self::$framework, self::$curl, self::$images);
						$objlib->save_favorites($obj_favorites);
					}
					$slide->updateSlideFromData($data);
					self::ajaxResponseSuccess(__("Slide updated"));
				break;
				case "update_static_slide":
					if(isset($data['obj_favorites'])){
						$obj_favorites = $data['obj_favorites'];
						unset($data['obj_favorites']);
						//save object favourites
						$objlib = new RevSliderObjectLibrary(self::$filesystem, self::$framework, self::$curl, self::$images);
						$objlib->save_favorites($obj_favorites);
					}
					$slide->updateStaticSlideFromData($data);
					self::ajaxResponseSuccess(__("Static Global Layers updated"));
				break;
				case "delete_slide":
				case "delete_slide_stay":
					$isPost = $slide->deleteSlideFromData($data);
					if($isPost)
						$message = __("Post deleted");
					else
						$message = __("Slide deleted");

					$sliderID = RevSliderFunctions::getVal($data, "sliderID");
					if($action == 'delete_slide_stay'){
						self::ajaxResponseSuccess($message);
					}else{
						self::ajaxResponseSuccessRedirect($message, self::getViewUrl(self::VIEW_SLIDE,"id=new&slider=$sliderID"));
					}
				break;
				case "duplicate_slide":
				case "duplicate_slide_stay":
					$return = $slider->duplicateSlideFromData($data);
					if($action == 'duplicate_slide_stay'){
						self::ajaxResponseSuccess(__("Slide duplicated"), array('id' => $return[1]));
					}else{
						self::ajaxResponseSuccessRedirect(__("Slide duplicated"), self::getViewUrl(self::VIEW_SLIDE,"id=new&slider=".$return[0]));
					}
				break;
				case "copy_move_slide":
				case "copy_move_slide_stay":
					$sliderID = $slider->copyMoveSlideFromData($data);
					if($action == 'copy_move_slide_stay'){
						self::ajaxResponseSuccess(__("Success!"));
					}else{
						self::ajaxResponseSuccessRedirect(__("Success! Refreshing page..."), self::getViewUrl(self::VIEW_SLIDE,"id=new&slider=$sliderID"));
					}
				break;
				case "add_slide_to_template":
					$template = new RevSliderTemplate(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);
					if(!isset($data['slideID']) || intval($data['slideID']) == 0){
						RevSliderFunctions::throwError(__('No valid Slide ID given'));
						exit;
					}
					if(!isset($data['title']) || strlen(trim($data['title'])) < 3){
						RevSliderFunctions::throwError(__('No valid title given'));
						exit;
					}
					if(!isset($data['settings']) || !isset($data['settings']['width']) || !isset($data['settings']['height'])){
						RevSliderFunctions::throwError(__('No valid title given'));
						exit;
					}

					$return = $template->copySlideToTemplates($data['slideID'], $data['title'], $data['settings']);

					if($return == false){
						RevSliderFunctions::throwError(__('Could not save Slide as Template'));
						exit;
					}

					//get HTML for template section
					ob_start();

					$rs_disable_template_script = true; //disable the script output of template selector file

					include(self::$framework->getTemplatePath('Nwdthemes_Revslider::templates/template-selector.phtml'));

					$html = ob_get_contents();

					ob_clean();
					ob_end_clean();

					self::ajaxResponseSuccess(__('Slide added to Templates'),array('HTML' => $html));
					exit;
				break;
				case "get_slider_custom_css_js":
                    $slider_css = '';
                    $slider_js = '';
                    if(isset($data['slider_id']) && intval($data['slider_id']) > 0){
						$slider->initByID(intval($data['slider_id']));
						$slider_css = stripslashes($slider->getParam('custom_css', ''));
						$slider_js = stripslashes($slider->getParam('custom_javascript', ''));
					}
                    self::ajaxResponseData(array('css' => $slider_css, 'js' => $slider_js));
                break;
                case "update_slider_custom_css_js":
                    if(isset($data['slider_id']) && intval($data['slider_id']) > 0){
						$slider->initByID(intval($data['slider_id']));
						$slider->updateParam(array('custom_css' => $data['css']));
						$slider->updateParam(array('custom_javascript' => $data['js']));
					}
                    self::ajaxResponseSuccess(__('Slider CSS saved'));
                    exit;
				break;
				case "get_static_css":
					$contentCSS = $operations->getStaticCss();
					self::ajaxResponseData($contentCSS);
				break;
				case "get_dynamic_css":
					$contentCSS = $operations->getDynamicCss();
					self::ajaxResponseData($contentCSS);
				break;
				case "insert_captions_css":

                    if ( ! isset($data['handle']) || ! $data['handle'])
                        RevSliderFunctions::throwError(__('Style name is empty. CSS not saved.'));

                    if ( ! preg_match("/^[a-z]$/i", substr($data['handle'], 0, 1)))
                        RevSliderFunctions::throwError(__('Style name should start with alphabetic charcter. CSS not saved.'));

					$arrCaptions = $operations->insertCaptionsContentData($data);

					if($arrCaptions !== false){
						$db = new RevSliderDB(self::$query, self::$resource);
						$styles = $db->fetch(RevSliderGlobals::$table_css);
						$styles = RevSliderCssParser::parseDbArrayToCss($styles, "\n");
						$styles = RevSliderCssParser::compress_css($styles);
						$custom_css = RevSliderOperations::getStaticCss();
						$custom_css = RevSliderCssParser::compress_css($custom_css);

						$arrCSS = $operations->getCaptionsContentArray();
						$arrCssStyles = RevSliderFunctions::jsonEncodeForClientSide($arrCSS);
						$arrCssStyles = $arrCSS;

						self::ajaxResponseSuccess(__("CSS saved"),array("arrCaptions"=>$arrCaptions,'compressed_css'=>$styles.$custom_css,'initstyles'=>$arrCssStyles));
					}

					RevSliderFunctions::throwError(__('CSS could not be saved'));
					exit();
				break;
				case "update_captions_css":

                    if ( ! isset($data['handle']) || ! $data['handle'])
                        RevSliderFunctions::throwError(__('Style name is empty. CSS not saved.'));

                    if ( ! preg_match("/^[a-z]$/i", substr($data['handle'], 0, 1)))
                        RevSliderFunctions::throwError(__('Style name should start with alphabetic charcter. CSS not saved.'));

					$arrCaptions = $operations->updateCaptionsContentData($data);

					if($arrCaptions !== false){
						$db = new RevSliderDB(self::$query, self::$resource);
						$styles = $db->fetch(RevSliderGlobals::$table_css);
						$styles = RevSliderCssParser::parseDbArrayToCss($styles, "\n");
						$styles = RevSliderCssParser::compress_css($styles);
						$custom_css = RevSliderOperations::getStaticCss();
						$custom_css = RevSliderCssParser::compress_css($custom_css);

						$arrCSS = $operations->getCaptionsContentArray();
						$arrCssStyles = RevSliderFunctions::jsonEncodeForClientSide($arrCSS);
						$arrCssStyles = $arrCSS;

						self::ajaxResponseSuccess(__("CSS saved"),array("arrCaptions"=>$arrCaptions,'compressed_css'=>$styles.$custom_css,'initstyles'=>$arrCssStyles));
					}

					RevSliderFunctions::throwError(__('CSS could not be saved'));
					exit();
				break;
				case "update_captions_advanced_css":

					$arrCaptions = $operations->updateAdvancedCssData($data);
					if($arrCaptions !== false){
						$db = new RevSliderDB(self::$query, self::$resource);
						$styles = $db->fetch(RevSliderGlobals::$table_css);
						$styles = RevSliderCssParser::parseDbArrayToCss($styles, "\n");
						$styles = RevSliderCssParser::compress_css($styles);
						$custom_css = RevSliderOperations::getStaticCss();
						$custom_css = RevSliderCssParser::compress_css($custom_css);

						$arrCSS = $operations->getCaptionsContentArray();
						$arrCssStyles = RevSliderFunctions::jsonEncodeForClientSide($arrCSS);
						$arrCssStyles = $arrCSS;

						self::ajaxResponseSuccess(__("CSS saved"),array("arrCaptions"=>$arrCaptions,'compressed_css'=>$styles.$custom_css,'initstyles'=>$arrCssStyles));
					}

					RevSliderFunctions::throwError(__('CSS could not be saved'));
					exit();
				break;
				case "rename_captions_css":
					//rename all captions in all sliders with new handle if success
					$arrCaptions = $operations->renameCaption($data);

					$db = new RevSliderDB(self::$query, self::$resource);
					$styles = $db->fetch(RevSliderGlobals::$table_css);
					$styles = RevSliderCssParser::parseDbArrayToCss($styles, "\n");
					$styles = RevSliderCssParser::compress_css($styles);
					$custom_css = RevSliderOperations::getStaticCss();
					$custom_css = RevSliderCssParser::compress_css($custom_css);

					$arrCSS = $operations->getCaptionsContentArray();
					$arrCssStyles = RevSliderFunctions::jsonEncodeForClientSide($arrCSS);
					$arrCssStyles = $arrCSS;

					self::ajaxResponseSuccess(__("Class name renamed"),array("arrCaptions"=>$arrCaptions,'compressed_css'=>$styles.$custom_css,'initstyles'=>$arrCssStyles));
				break;
				case "delete_captions_css":
					$arrCaptions = $operations->deleteCaptionsContentData($data);

					$db = new RevSliderDB(self::$query, self::$resource);
					$styles = $db->fetch(RevSliderGlobals::$table_css);
					$styles = RevSliderCssParser::parseDbArrayToCss($styles, "\n");
					$styles = RevSliderCssParser::compress_css($styles);
					$custom_css = RevSliderOperations::getStaticCss();
					$custom_css = RevSliderCssParser::compress_css($custom_css);

					$arrCSS = $operations->getCaptionsContentArray();
					$arrCssStyles = RevSliderFunctions::jsonEncodeForClientSide($arrCSS);
					$arrCssStyles = $arrCSS;

					self::ajaxResponseSuccess(__("Style deleted!"),array("arrCaptions"=>$arrCaptions,'compressed_css'=>$styles.$custom_css,'initstyles'=>$arrCssStyles));
				break;
				case "update_static_css":
					$data = ''; //do not allow to add new global CSS anymore, instead, remove all!
					$staticCss = $operations->updateStaticCss($data);

					$db = new RevSliderDB(self::$query, self::$resource);
					$styles = $db->fetch(RevSliderGlobals::$table_css);
					$styles = RevSliderCssParser::parseDbArrayToCss($styles, "\n");
					$styles = RevSliderCssParser::compress_css($styles);
					$custom_css = RevSliderOperations::getStaticCss();
					$custom_css = RevSliderCssParser::compress_css($custom_css);

					self::ajaxResponseSuccess(__("CSS saved"),array("css"=>$staticCss,'compressed_css'=>$styles.$custom_css));
				break;
				case "insert_custom_anim":
					$arrAnims = $operations->insertCustomAnim($data); //$arrCaptions =
					self::ajaxResponseSuccess(__("Animation saved"), $arrAnims); //,array("arrCaptions"=>$arrCaptions)
				break;
				case "update_custom_anim":
					$arrAnims = $operations->updateCustomAnim($data);
					self::ajaxResponseSuccess(__("Animation saved"), $arrAnims); //,array("arrCaptions"=>$arrCaptions)
				break;
				case "update_custom_anim_name":
					$arrAnims = $operations->updateCustomAnimName($data);
					self::ajaxResponseSuccess(__("Animation saved"), $arrAnims); //,array("arrCaptions"=>$arrCaptions)
				break;
				case "delete_custom_anim":
					$arrAnims = $operations->deleteCustomAnim($data);
					self::ajaxResponseSuccess(__("Animation deleted"), $arrAnims); //,array("arrCaptions"=>$arrCaptions)
				break;
				case "update_slides_order":
					$slider->updateSlidesOrderFromData($data);
					self::ajaxResponseSuccess(__("Order updated"));
				break;
				case "change_slide_title":
					$slide->updateTitleByID($data);
					self::ajaxResponseSuccess(__('Title updated'));
				break;
				case "change_slide_image":
					$slide->updateSlideImageFromData($data);
					$sliderID = RevSliderFunctions::getVal($data, "slider_id");
					self::ajaxResponseSuccessRedirect(__("Slide changed"), self::getViewUrl(self::VIEW_SLIDE,"id=new&slider=$sliderID"));
				break;
				case "preview_slide":
					$operations->putSlidePreviewByData($data);
                    exit;
				break;
				case "preview_slider":
					$sliderID = RevSliderFunctions::getPostGetVariable("sliderid");
					$do_markup = RevSliderFunctions::getPostGetVariable("only_markup");

					if($do_markup == 'true')
						$operations->previewOutputMarkup($sliderID);
					else
						$operations->previewOutput($sliderID);

                    exit;
				break;
                case "get_import_slides_data":
                    $slides = array();
                    if(!is_array($data)){
                        $slider->initByID(intval($data));

                        $full_slides = $slider->getSlides(); //static slide is missing

                        if(!empty($full_slides)){
                            foreach($full_slides as $slide_id => $mslide){
                                $slides[$slide_id]['layers'] = $mslide->getLayers();
								foreach($slides[$slide_id]['layers'] as $k => $l){ //remove columns as they can not be imported
									if(isset($l['type']) && ($l['type'] == 'column' || $l['type'] == 'row' || $l['type'] == 'group')) unset($slides[$slide_id]['layers'][$k]);
								}
                                $slides[$slide_id]['params'] = $mslide->getParams();
                            }
                        }

                        $staticID = $slide->getStaticSlideID($slider->getID());
                        if($staticID !== false){
                            $msl = new RevSliderSlide(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);
                            if(strpos($staticID, 'static_') === false){
                                $staticID = 'static_'.$slider->getID();
                            }
                            $msl->initByID($staticID);
                            if($msl->getID() !== ''){
                                $slides[$msl->getID()]['layers'] = $msl->getLayers();
								foreach($slides[$msl->getID()]['layers'] as $k => $l){ //remove columns as they can not be imported
									if(isset($l['type']) && ($l['type'] == 'column' || $l['type'] == 'row' || $l['type'] == 'group')) unset($slides[$msl->getID()]['layers'][$k]);
								}
                                $slides[$msl->getID()]['params'] = $msl->getParams();
                                $slides[$msl->getID()]['params']['title'] = __('Static Slide');
                            }
                        }
                    }
                    if(!empty($slides)){
                        self::ajaxResponseData(array('slides' => $slides));
                    }else{
                        self::ajaxResponseData('');
                    }
                break;
                case "create_navigation_preset":
                    $nav = new RevSliderNavigation(self::$framework, self::$query);

                    $return = $nav->add_preset($data);

                    if($return === true){
                        self::ajaxResponseSuccess(__('Navigation preset saved/updated'), array('navs' => $nav->get_all_navigations()));
                    }else{
                        if($return === false) $return = __('Preset could not be saved/values are the same');
                        self::ajaxResponseError($return);
                    }
                break;
                case "delete_navigation_preset":
                    $nav = new RevSliderNavigation(self::$framework, self::$query);

                    $return = $nav->delete_preset($data);

                    if($return){
                        self::ajaxResponseSuccess(__('Navigation preset deleted'), array('navs' => $nav->get_all_navigations()));
                    }else{
                        if($return === false) $return = __('Preset not found');
                        self::ajaxResponseError($return);
                    }
                break;
				case "toggle_slide_state":
					$currentState = $slide->toggleSlideStatFromData($data);
					self::ajaxResponseData(array("state"=>$currentState));
				break;
				case "toggle_hero_slide":
					$currentHero = $slider->setHeroSlide($data);
					self::ajaxResponseSuccess(__('Slide is now the new active Hero Slide'));
				break;
				case "slide_lang_operation":
					$responseData = $slide->doSlideLangOperation($data);
					self::ajaxResponseData($responseData);
				break;
				case "update_general_settings":
					$operations->updateGeneralSettings($data);
					self::ajaxResponseSuccess(__("General settings updated"));
				break;
                case "fix_database_issues":
                    self::$framework->update_option('revslider_change_database', true);
                    RevSliderFront::createDBTables();

                    self::ajaxResponseSuccess(__('Database structure creation/update done'));
                break;
                case "trigger_font_deletion":
                    RevSliderOperations::deleteGoogleFonts();

                    self::ajaxResponseSuccess(__('Downloaded Google Fonts will be updated'));
                break;
				case "update_posts_sortby":
					$slider->updatePostsSortbyFromData($data);
					self::ajaxResponseSuccess(__("Sortby updated"));
				break;
				case "replace_image_urls":
					$slider->replaceImageUrlsFromData($data);
                    self::ajaxResponseSuccess(__("All Urls replaced"));
				break;
				case "reset_slide_settings":
					$slider->resetSlideSettings($data);
					self::ajaxResponseSuccess(__("Settings in all Slides changed"));
				break;
				case "delete_template_slide":

					$slideID = (isset($data['slide_id'])) ? $data['slide_id'] : -1;

					if($slideID === -1) RevSliderFunctions::throwError(__('Missing Slide ID!'));

					$slide->initByID($slideID);
					$slide->deleteSlide();

					$responseText = __("Slide deleted");
					self::ajaxResponseSuccess($responseText);
					break;
				case "activate_purchase_code":
					$result = false;
                    if(!empty($data['code'])){
						$result = $operations->checkPurchaseVerification($data);
					}else{
                        RevSliderFunctions::throwError(__('The Purchase Code need to be set!'));
						exit();
					}

                    if($result === true){
						self::ajaxResponseSuccessRedirect(__("Purchase Code Successfully Activated"), self::getViewUrl(self::VIEW_SLIDERS));
                    }elseif($result === false){
						RevSliderFunctions::throwError(__('Purchase Code is invalid'));
                    }else{
                        if($result == 'temp'){
                            self::ajaxResponseSuccessRedirect(__("Purchase Code Temporary Activated"), self::getViewUrl(self::VIEW_SLIDERS));
                        }
                        if($result == 'exist'){
                            self::ajaxResponseData(array('error'=>$result,'msg'=> __('Purchase Code already registered!')));
                        }
                        RevSliderFunctions::throwError(__('Purchase Code could not be validated'));
					}
				break;
				case "deactivate_purchase_code":
					$result = $operations->doPurchaseDeactivation($data);

					if($result){
						self::ajaxResponseSuccessRedirect(__("Successfully removed validation"), self::getViewUrl(self::VIEW_SLIDERS));
					}else{
						RevSliderFunctions::throwError(__('Could not remove Validation!'));
					}
				break;
				case 'dismiss_notice':
					self::$framework->update_option('revslider-valid-notice', 'false');
					self::ajaxResponseSuccess(".");
				break;
				case 'dismiss_dynamic_notice':
                    if(trim($data['id']) == 'DISCARD'){
                        self::$framework->update_option('revslider-deact-notice', false);
                    }elseif(trim($data['id']) == 'DISCARDTEMPACT'){
                        self::$framework->update_option('revslider-temp-active-notice', 'false');
                    }else{
                        $notices_discarded = self::$framework->get_option('revslider-notices-dc', array());
                        $notices_discarded[] = self::$framework->esc_attr(trim($data['id']));
                        self::$framework->update_option('revslider-notices-dc', $notices_discarded);
                    }

					self::ajaxResponseSuccess(".");
				break;
				case 'toggle_favorite':
					if(isset($data['id']) && intval($data['id']) > 0){
						$return = self::toggle_favorite_by_id($data['id']);
						if($return === true){
							self::ajaxResponseSuccess(__('Setting Changed!'));
						}else{
							$error = $return;
						}
					}else{
						$error = __('No ID given');
					}
					self::ajaxResponseError($error);
				break;
				case 'change_specific_navigation':
					$nav = new RevSliderNavigation(self::$framework, self::$query);

					$found = false;
					$navigations = $nav->get_all_navigations();
					foreach($navigations as $navig){
						if($data['id'] == $navig['id']){
							$found = true;
							break;
						}
					}
					if($found){
						$nav->create_update_navigation($data, $data['id']);
					}else{
						$nav->create_update_navigation($data);
					}

					self::ajaxResponseSuccess(__('Navigation saved/updated'), array('navs' => $nav->get_all_navigations()));

				break;
				case 'change_navigations':
					$nav = new RevSliderNavigation(self::$framework, self::$query);

					$nav->create_update_full_navigation($data);

					self::ajaxResponseSuccess(__('Navigations updated'), array('navs' => $nav->get_all_navigations()));
				break;
				case 'delete_navigation':
					$nav = new RevSliderNavigation(self::$framework, self::$query);

					if(isset($data) && intval($data) > 0){
						$return = $nav->delete_navigation($data);

						if($return !== true){
							self::ajaxResponseError($return);
						}else{
							self::ajaxResponseSuccess(__('Navigation deleted'), array('navs' => $nav->get_all_navigations()));
						}
					}

					self::ajaxResponseError(__('Wrong ID given'));
				break;
				case "get_facebook_photosets":
					if(!empty($data['url'])){
						$facebook = new RevSliderFacebook(self::$framework);
						$return = $facebook->get_photo_set_photos_options($data['url'],$data['album'],$data['app_id'],$data['app_secret']);
						if(!empty($return)){
							self::ajaxResponseSuccess(__('Successfully fetched Facebook albums'), array('html'=>implode(' ', $return)));
						}
						else{
							$error = __('Could not fetch Facebook albums');
							self::ajaxResponseError($error);
						}
					}
					else {
						self::ajaxResponseSuccess(__('Cleared Albums'), array('html'=>implode(' ', $return)));
					}
				break;
				case "get_flickr_photosets":
					if(!empty($data['url']) && !empty($data['key'])){
						$flickr = new RevSliderFlickr(self::$framework, $data['key']);
						$user_id = $flickr->get_user_from_url($data['url']);
						$return = $flickr->get_photo_sets($user_id,$data['count'],$data['set']);
						if(!empty($return)){
							self::ajaxResponseSuccess(__('Successfully fetched flickr photosets'), array("data"=>array('html'=>implode(' ', $return))));
						}
						else{
							$error = __('Could not fetch flickr photosets');
							self::ajaxResponseError($error);
						}
					}
					else {
						if(empty($data['url']) && empty($data['key'])){
							self::ajaxResponseSuccess(__('Cleared Photosets'), array('html'=>implode(' ', $return)));
						}
						elseif(empty($data['url'])){
							$error = __('No User URL - Could not fetch flickr photosets');
							self::ajaxResponseError($error);
						}
						else{
							$error = __('No API KEY - Could not fetch flickr photosets');
							self::ajaxResponseError($error);
						}
					}
				break;
				case "get_youtube_playlists":
					if(!empty($data['id'])){
						$youtube = new RevSliderYoutube(self::$framework, trim($data['api']),trim($data['id']));
						$return = $youtube->get_playlist_options($data['playlist']);
						self::ajaxResponseSuccess(__('Successfully fetched YouTube playlists'), array("data"=>array('html'=>implode(' ', $return))));
					}
					else {
						$error = __('Could not fetch YouTube playlists');
						self::ajaxResponseError($error);
					}
				break;
				case 'load_library_object':
					$obj_library = new RevSliderObjectLibrary(self::$filesystem, self::$framework, self::$curl, self::$images);

					$thumbhandle = $data['handle'];
					$type = $data['type'];
					if($type == 'thumb'){
						$thumb = $obj_library->_get_object_thumb($thumbhandle, 'thumb');
					}elseif($type == 'orig'){
						$thumb = $obj_library->_get_object_thumb($thumbhandle, 'original');
					}
					if($thumb['error']){
						self::ajaxResponseError(__('Object could not be loaded'));
					}else{
						self::ajaxResponseData(array('url'=> $thumb['url'], 'width' => $thumb['width'], 'height' => $thumb['height']));
					}
					break;
				case 'load_template_store_sliders':
					$tmpl = new RevSliderTemplate(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);

                    $tmpl->updateImages(true);

					$tp_template_slider = $tmpl->getThemePunchTemplateSliders();

					ob_start();
					$tmpl->create_html_sliders($tp_template_slider);
					$html = ob_get_contents();
					ob_clean();
					ob_end_clean();

					self::ajaxResponseData(array('html'=> $html));

					break;
				case 'load_template_store_slides':
					$tmpl = new RevSliderTemplate(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);

                    $tmpl->updateImages();

					$templates = $tmpl->getTemplateSlides();
					$tp_template_slider = $tmpl->getThemePunchTemplateSliders();

					$tmp_slider = new RevSliderSlider(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);
					$all_slider = self::$framework->apply_filters('revslider_slide_templates', $tmp_slider->getArrSliders());

					ob_start();
					$tmpl->create_html_slides($tp_template_slider, $all_slider, $templates);
					$html = ob_get_contents();
					ob_clean();
					ob_end_clean();

					self::ajaxResponseData(array('html'=> $html));

					break;
				case 'load_object_library':
					$html = '';
					$obj = new RevSliderObjectLibrary(self::$filesystem, self::$framework, self::$curl, self::$images);
					$mdata = $obj->retrieve_all_object_data();

					self::ajaxResponseData(array('data'=> $mdata));
					break;
				case 'slide_editor_sticky_menu':
                    if(isset($data['set_sticky']) && $data['set_sticky'] == 'true'){
						self::$framework->update_option('revslider_slide_editor_sticky', 'true');
					}else{
						self::$framework->update_option('revslider_slide_editor_sticky', 'false');
					}
                    self::ajaxResponseData(array());
					break;
                case 'save_color_preset':

                    $presets = TPColorpicker::save_color_presets($data['presets']);
                    self::ajaxResponseData(array('presets' => $presets));

                    break;
                default:
                    $return = self::$framework->apply_filters('revslider_admin_onAjaxAction_switch', false, $action, $data, $slider, $slide, $operations);
					if ($isAjax) {
						if($return === false)
							self::ajaxResponseError("wrong ajax action: ".self::$framework->esc_attr($action));

						exit;
					}
				break;
			}

		}
		catch(\Exception $e){
            Data::logException($e);

			$message = $e->getMessage();
			if($action == "preview_slide" || $action == "preview_slider"){
				echo $message;
				exit();
			}

			if ($isAjax) {
				self::ajaxResponseError($message);
			} else {
				echo $message;
			}
		}

		if ($isAjax) {
			//it's an ajax action, so exit
			self::ajaxResponseError("No response output on $action action. please check with the developer.");
			exit();
		}
	}


	/**
	 * onAjax action handler
	 */
	public static function onFrontAjaxAction(){
		$db = new RevSliderDB(self::$query, self::$resource);
		$slider = new RevSliderSlider(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);
		$slide = new RevSliderSlide(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);
		$operations = new RevSliderOperations(self::$framework, self::$query, self::$curl, self::$filesystem, self::$images, self::$resource, self::$googleFonts, self::$registerHelper);

		$token = self::getPostVar("token", false);

		//verify the token
		$isVerified = self::$framework->wp_verify_nonce($token, 'RevSlider_Front');

		$error = false;
		if($isVerified){
			$data = self::getPostVar('data', false);
			switch(self::getPostVar('client_action', false)){
				case 'get_slider_html':
					$id = intval(self::getPostVar('id', 0));
					if($id > 0){
						$html = '';
						ob_start();
						$slider_class = RevSliderOutput::putSlider($id);
						$html = ob_get_contents();

						//add styling
						$custom_css = RevSliderOperations::getStaticCss();
						$custom_css = RevSliderCssParser::compress_css($custom_css);
						$styles = $db->fetch(RevSliderGlobals::$table_css);
						$styles = RevSliderCssParser::parseDbArrayToCss($styles, "\n");
						$styles = RevSliderCssParser::compress_css($styles);

						$html .= '<style type="text/css">'.$custom_css.'</style>';
						$html .= '<style type="text/css">'.$styles.'</style>';

						ob_clean();
						ob_end_clean();

						$result = (!empty($slider_class) && $html !== '') ? true : false;

						if(!$result){
							$error = __('Slider not found');
						}else{

							if($html !== false){
								self::ajaxResponseData($html);
							}else{
								$error = __('Slider not found');
							}
						}
					}else{
						$error = __('No Data Received');
					}
				break;
			}

		}else{
			$error = true;
		}

		if($error !== false){
			$showError = __('Loading Error');
			if($error !== true)
				$showError = __('Loading Error: ').$error;

			self::ajaxResponseError($showError, false);
		}
		exit();
	}


	/**
	 *	Get javascript transactions json
	 *	added to return it to outside controller
	 *
	 *	@return	string
	 */

	static public function get_javascript_multilanguage_json() {
		return json_encode( self::get_javascript_multilanguage() );
	}

    /**
     * Update Add-On from server
     */

    public static function updateAddon() {
        $plugin = self::getGetVar('plugin');
        if ($plugin && self::$pluginHelper->updatePlugin(self::$revSliderLoadBalancer->get_url('updates'), $plugin)) {
            $message = __('Add-On updated.');
        } else {
            $message = __('Failed to update Add-On.');
        }
        self::$framework->dmp($message . ' ' . __('Redirecting...'));
        echo "<script>location.href='" . self::getViewUrl('addon') . "'</script>";
        exit();
    }

}
