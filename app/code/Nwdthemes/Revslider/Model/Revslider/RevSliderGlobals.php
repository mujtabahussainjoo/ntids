<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2015 ThemePunch
 */

namespace Nwdthemes\Revslider\Model\Revslider;

class RevSliderGlobals {

    const SLIDER_REVISION = '5.4.8.4';
    const PLUGIN_SLUG = 'revslider_magento2';

	const TABLE_SLIDERS_NAME = "nwdthemes_revslider_sliders";
	const TABLE_SLIDES_NAME = "nwdthemes_revslider_slides";
	const TABLE_STATIC_SLIDES_NAME = "nwdthemes_revslider_static_slides";
	const TABLE_SETTINGS_NAME = "nwdthemes_revslider_settings"; // not used
	const TABLE_CSS_NAME = "nwdthemes_revslider_css";
	const TABLE_LAYER_ANIMS_NAME = "nwdthemes_revslider_animations";
	const TABLE_NAVIGATION_NAME = "nwdthemes_revslider_navigations";

	const FIELDS_SLIDE = "slider_id,slide_order,params,layers";
    const FIELDS_SLIDER = "title,alias,params,type";

	const YOUTUBE_EXAMPLE_ID = "iyuxFo-WBiU";
	const DEFAULT_YOUTUBE_ARGUMENTS = "hd=1&amp;wmode=opaque&amp;showinfo=0&amp;ref=0;";
	const DEFAULT_VIMEO_ARGUMENTS = "title=0&amp;byline=0&amp;portrait=0&amp;api=1";
    const LINK_HELP_SLIDERS = "https://www.themepunch.com/revslider-doc/slider-revolution-documentation/?rev=rsb";
    const LINK_HELP_SLIDER = "https://www.themepunch.com/revslider-doc/slider-settings/?rev=rsb#generalsettings";
    const LINK_HELP_SLIDE_LIST = "https://www.themepunch.com/revslider-doc/individual-slide-settings/?rev=rsb";
    const LINK_HELP_SLIDE = "https://www.themepunch.com/revslider-doc/individual-slide-settings/?rev=rsb";

	public static $table_sliders;
	public static $table_slides;
	public static $table_static_slides;
	public static $table_settings;
	public static $table_css;
	public static $table_layer_anims;
	public static $table_navigation;
	public static $filepath_backup;
	public static $filepath_captions;
	public static $filepath_dynamic_captions;
	public static $filepath_captions_original;
	public static $urlCaptionsCSS;
	public static $uploadsUrlExportZip;
	public static $isNewVersion;

}
