<?php

namespace Nwdthemes\Revslider\Block\Adminhtml;

use \Nwdthemes\Revslider\Helper\Data;
use \Nwdthemes\Revslider\Helper\Framework;
use \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderBase;
use \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderCssParser;
use \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderFunctions;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderAdmin;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderGlobals;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderNavigation;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderOperations;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderSlide;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderSlider;

class SlideEditor extends \Nwdthemes\Revslider\Block\Adminhtml\Block\Template {

	/**
	 * Constructor
	 */

	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
		\Nwdthemes\Revslider\Helper\Data $dataHelper,
		\Nwdthemes\Revslider\Helper\Framework $framework,
        \Nwdthemes\Revslider\Helper\Query $query,
        \Nwdthemes\Revslider\Helper\Curl $curl,
        \Nwdthemes\Revslider\Helper\Filesystem $filesystemHelper,
        \Nwdthemes\Revslider\Helper\Images $images,
        \Magento\Framework\App\ResourceConnection $resource,
        \Nwdthemes\Revslider\Model\Revslider\GoogleFonts $googleFonts,
        \Nwdthemes\Revslider\Helper\Register $registerHelper
    ) {
		parent::__construct(
			$context,
			$dataHelper,
			$framework,
			$query,
			$curl,
			$filesystemHelper,
			$images,
			$resource,
			$googleFonts,
            $registerHelper
		);

		//get input
		$slideID = RevSliderFunctions::getGetVar("id");
		if($slideID == 'new'){ //add new transparent slide
			$sID = intval(RevSliderFunctions::getGetVar("slider"));
			if($sID > 0){
				$revs = new RevSliderSlider($framework, $query, $curl, $filesystemHelper, $images, $resource, $googleFonts, $registerHelper);
				$revs->initByID($sID);
				//check if we already have slides, if yes, go to first
				$arrS = $revs->getSlides(false);
				if(empty($arrS)){
					$slideID = $revs->createSlideFromData(array('sliderid'=>$sID),true);
				}else{
					$slideID = key($arrS);
				}
			}
		}

		$patternViewSlide = $this->getViewUrl("slide","id=[slideid]");

		//init slide object
		$slide = new RevSliderSlide($framework, $query, $curl, $filesystemHelper, $images, $resource, $googleFonts, $registerHelper);
		$slide->initByID($slideID);

		$slideParams = $slide->getParams();

		$operations = new RevSliderOperations($framework, $query, $curl, $filesystemHelper, $images, $resource, $googleFonts, $registerHelper);

		$rs_nav = new RevSliderNavigation($framework, $query);
		$arr_navigations = $rs_nav->get_all_navigations();
		
		//init slider object
		$sliderID = $slide->getSliderID();
		$slider = new RevSliderSlider($framework, $query, $curl, $filesystemHelper, $images, $resource, $googleFonts, $registerHelper);
		$slider->initByID($sliderID);
		$sliderParams = $slider->getParams();
		$arrSlideNames = $slider->getArrSlideNames();

		$arrSlides = $slider->getSlides(false);

		$arrSliders = $slider->getArrSlidersShort($sliderID);
		$arrSlidersFull = $slider->getArrSlidersShort();
		$selectSliders = RevSliderFunctions::getHTMLSelect($arrSliders,"","id='selectSliders'",true);

		//check if slider is template
		$sliderTemplate = $slider->getParam("template","false");

		//set slide delay
		$sliderDelay = $slider->getParam("delay","9000");
		$slideDelay = $slide->getParam("delay","");
		if(empty($slideDelay))
			$slideDelay = $sliderDelay;

		//add tools.min.js
		$framework->wp_enqueue_script('tp-tools', Framework::$RS_PLUGIN_URL .'public/assets/js/jquery.themepunch.tools.min.js', array(), RevSliderGlobals::SLIDER_REVISION );

		$arrLayers = $slide->getLayers();

		//set Layer settings
		$cssContent = $operations->getCaptionsContent();

		$arrCaptionClasses = $operations->getArrCaptionClasses($cssContent);
		//$arrCaptionClassesSorted = $operations->getArrCaptionClasses($cssContent);
		$arrCaptionClassesSorted = RevSliderCssParser::get_captions_sorted();

		$arrFontFamily = $operations->getArrFontFamilys($slider);
		$arrCSS = $operations->getCaptionsContentArray();

		$arrAnim = $operations->getFullCustomAnimations();
		$arrAnimDefaultIn = $operations->getArrAnimations(false);
		$arrAnimDefaultOut = $operations->getArrEndAnimations(false);

		$arrAnimDefault = array_merge($arrAnimDefaultIn, $arrAnimDefaultOut);

		//set various parameters needed for the page
		$width = $sliderParams["width"];
		$height = $sliderParams["height"];
		$imageUrl = $slide->getImageUrl();
		$imageID = $slide->getImageID();

		$slider_type = $slider->getParam('source_type','gallery');

        if (in_array($slider_type, ['posts', 'specific_posts'])) {
            $framework->add_action('rs_action_add_layer_action', [$this, 'addProductSliderActions']);
        }

        /**
		 * Get Slider params which will be used as default on Slides
		 * @since: 5.0
		 **/
		$def_background_fit = $slider->getParam('def-background_fit', 'cover');
		$def_image_source_type = $slider->getParam('def-image_source_type', 'full');
		$def_bg_fit_x = $slider->getParam('def-bg_fit_x', '100');
		$def_bg_fit_y = $slider->getParam('def-bg_fit_y', '100');
		$def_bg_position = $slider->getParam('def-bg_position', 'center center');
		$def_bg_position_x = $slider->getParam('def-bg_position_x', '0');
		$def_bg_position_y = $slider->getParam('def-bg_position_y', '0');
		$def_bg_repeat = $slider->getParam('def-bg_repeat', 'no-repeat');
		$def_kenburn_effect = $slider->getParam('def-kenburn_effect', 'off');
		$def_kb_start_fit = $slider->getParam('def-kb_start_fit', '100');
		$def_kb_easing = $slider->getParam('def-kb_easing', 'Linear.easeNone');
		$def_kb_end_fit = $slider->getParam('def-kb_end_fit', '100');
		$def_kb_duration = $slider->getParam('def-kb_duration', '10000');
		$def_transition = $slider->getParam('def-slide_transition', 'fade');
		$def_transition_duration = $slider->getParam('def-transition_duration', 'default');

		$def_use_parallax = $slider->getParam('use_parallax', 'on');

		/* NEW KEN BURN INPUTS */
		$def_kb_start_offset_x = $slider->getParam('def-kb_start_offset_x', '0');
        $def_kb_blur_start = $slider->getParam('def-kb_blur_start', '0');
        $def_kb_blur_end = $slider->getParam('def-kb_blur_end', '0');
		$def_kb_start_offset_y = $slider->getParam('def-kb_start_offset_y', '0');
		$def_kb_end_offset_x = $slider->getParam('def-kb_end_offset_x', '0');
		$def_kb_end_offset_y = $slider->getParam('def-kb_end_offset_y', '0');
		$def_kb_start_rotate = $slider->getParam('def-kb_start_rotate', '0');
		$def_kb_end_rotate = $slider->getParam('def-kb_end_rotate', '0');
		/* END OF NEW KEN BURN INPUTS */

		$imageFilename = $slide->getImageFilename();

		$style = "height:".$height."px;"; //

		$divLayersWidth = "width:".$width."px;";
		$divbgminwidth = "min-width:".$width."px;";
		$maxbgwidth = "max-width:".$width."px;";

		//set iframe parameters
		$iframeWidth = $width+60;
		$iframeHeight = $height+50;

		$iframeStyle = "width:".$iframeWidth."px;height:".$iframeHeight."px;";

		$closeUrl = $this->getViewUrl(RevSliderAdmin::VIEW_SLIDES, "id=".$sliderID);

		$jsonLayers = RevSliderFunctions::jsonEncodeForClientSide($arrLayers);
		$jsonFontFamilys = RevSliderFunctions::jsonEncodeForClientSide($arrFontFamily);
		$jsonCaptions = RevSliderFunctions::jsonEncodeForClientSide($arrCaptionClassesSorted);

		$arrCssStyles = RevSliderFunctions::jsonEncodeForClientSide($arrCSS);

		$arrCustomAnim = RevSliderFunctions::jsonEncodeForClientSide($arrAnim);
		$arrCustomAnimDefault = RevSliderFunctions::jsonEncodeForClientSide($arrAnimDefault);

		//bg type params
		$bgType = RevSliderFunctions::getVal($slideParams, 'background_type', 'image');
		$slideBGColor = RevSliderFunctions::getVal($slideParams, 'slide_bg_color', '#E7E7E7');
		$divLayersClass = "slide_layers";

		$meta_handle = RevSliderFunctions::getVal($slideParams, 'meta_handle','');

		$bgFit = RevSliderFunctions::getVal($slideParams, 'bg_fit', $def_background_fit);
		$bgFitX = intval(RevSliderFunctions::getVal($slideParams, 'bg_fit_x', $def_bg_fit_x));
		$bgFitY = intval(RevSliderFunctions::getVal($slideParams, 'bg_fit_y', $def_bg_fit_y));

		$bgPosition = RevSliderFunctions::getVal($slideParams, 'bg_position', $def_bg_position);
		$bgPositionX = intval(RevSliderFunctions::getVal($slideParams, 'bg_position_x', $def_bg_position_x));
		$bgPositionY = intval(RevSliderFunctions::getVal($slideParams, 'bg_position_y', $def_bg_position_y));

		$slide_parallax_level = RevSliderFunctions::getVal($slideParams, 'slide_parallax_level', '-');
		$kenburn_effect = RevSliderFunctions::getVal($slideParams, 'kenburn_effect', $def_kenburn_effect);
		$kb_duration = RevSliderFunctions::getVal($slideParams, 'kb_duration', $def_kb_duration);
		$kb_easing = RevSliderFunctions::getVal($slideParams, 'kb_easing', $def_kb_easing);
		$kb_start_fit = RevSliderFunctions::getVal($slideParams, 'kb_start_fit', $def_kb_start_fit);
		$kb_end_fit = RevSliderFunctions::getVal($slideParams, 'kb_end_fit', $def_kb_end_fit);

		$ext_width = RevSliderFunctions::getVal($slideParams, 'ext_width', '1920');
		$ext_height = RevSliderFunctions::getVal($slideParams, 'ext_height', '1080');
		$use_parallax = RevSliderFunctions::getVal($slideParams, 'use_parallax', $def_use_parallax);

        $mediafilter = RevSliderFunctions::getVal($slideParams, 'media-filter-type', 'none');

        $parallax_level[] =  RevSliderFunctions::getVal($sliderParams,"parallax_level_1","5");
		$parallax_level[] =  RevSliderFunctions::getVal($sliderParams,"parallax_level_2","10");
		$parallax_level[] =  RevSliderFunctions::getVal($sliderParams,"parallax_level_3","15");
		$parallax_level[] =  RevSliderFunctions::getVal($sliderParams,"parallax_level_4","20");
		$parallax_level[] =  RevSliderFunctions::getVal($sliderParams,"parallax_level_5","25");
		$parallax_level[] =  RevSliderFunctions::getVal($sliderParams,"parallax_level_6","30");
		$parallax_level[] =  RevSliderFunctions::getVal($sliderParams,"parallax_level_7","35");
		$parallax_level[] =  RevSliderFunctions::getVal($sliderParams,"parallax_level_8","40");
		$parallax_level[] =  RevSliderFunctions::getVal($sliderParams,"parallax_level_9","45");
		$parallax_level[] =  RevSliderFunctions::getVal($sliderParams,"parallax_level_10","45");
		$parallax_level[] =  RevSliderFunctions::getVal($sliderParams,"parallax_level_11","46");
		$parallax_level[] =  RevSliderFunctions::getVal($sliderParams,"parallax_level_12","47");
		$parallax_level[] =  RevSliderFunctions::getVal($sliderParams,"parallax_level_13","48");
		$parallax_level[] =  RevSliderFunctions::getVal($sliderParams,"parallax_level_14","49");
		$parallax_level[] =  RevSliderFunctions::getVal($sliderParams,"parallax_level_15","50");
		$parallax_level[] =  RevSliderFunctions::getVal($sliderParams,"parallax_level_16","55");

		$parallaxisddd = RevSliderFunctions::getVal($sliderParams,"ddd_parallax","off");
		$parallaxbgfreeze = RevSliderFunctions::getVal($sliderParams,"ddd_parallax_bgfreeze","off");


		$slideBGYoutube = RevSliderFunctions::getVal($slideParams, 'slide_bg_youtube', '');
		$slideBGVimeo = RevSliderFunctions::getVal($slideParams, 'slide_bg_vimeo', '');
		$slideBGhtmlmpeg = RevSliderFunctions::getVal($slideParams, 'slide_bg_html_mpeg', '');
		$slideBGhtmlwebm = RevSliderFunctions::getVal($slideParams, 'slide_bg_html_webm', '');
		$slideBGhtmlogv = RevSliderFunctions::getVal($slideParams, 'slide_bg_html_ogv', '');

		$stream_do_cover = RevSliderFunctions::getVal($slideParams, 'stream_do_cover', 'on');
		$stream_do_cover_both = RevSliderFunctions::getVal($slideParams, 'stream_do_cover_both', 'on');

		$video_force_cover = RevSliderFunctions::getVal($slideParams, 'video_force_cover', 'on');
		$video_dotted_overlay = RevSliderFunctions::getVal($slideParams, 'video_dotted_overlay', 'none');
		$video_ratio = RevSliderFunctions::getVal($slideParams, 'video_ratio', 'none');
		$video_loop = RevSliderFunctions::getVal($slideParams, 'video_loop', 'none');
		$video_nextslide = RevSliderFunctions::getVal($slideParams, 'video_nextslide', 'off');
		$video_allowfullscreen = RevSliderFunctions::getVal($slideParams, 'video_allowfullscreen', 'on');
		$video_force_rewind = RevSliderFunctions::getVal($slideParams, 'video_force_rewind', 'on');
		$video_speed = RevSliderFunctions::getVal($slideParams, 'video_speed', '1');
		$video_mute = RevSliderFunctions::getVal($slideParams, 'video_mute', 'on');
		$video_volume = RevSliderFunctions::getVal($slideParams, 'video_volume', '100');
		$video_start_at = RevSliderFunctions::getVal($slideParams, 'video_start_at', '');
		$video_end_at = RevSliderFunctions::getVal($slideParams, 'video_end_at', '');
		$video_arguments = RevSliderFunctions::getVal($slideParams, 'video_arguments', RevSliderGlobals::DEFAULT_YOUTUBE_ARGUMENTS);
		$video_arguments_vim = RevSliderFunctions::getVal($slideParams, 'video_arguments_vimeo', RevSliderGlobals::DEFAULT_VIMEO_ARGUMENTS);

		/* NEW KEN BURN INPUTS */
		$kbStartOffsetX = intval(RevSliderFunctions::getVal($slideParams, 'kb_start_offset_x', $def_kb_start_offset_x));
		$kbStartOffsetY = intval(RevSliderFunctions::getVal($slideParams, 'kb_start_offset_y', $def_kb_start_offset_y));
		$kbEndOffsetX = intval(RevSliderFunctions::getVal($slideParams, 'kb_end_offset_x', $def_kb_end_offset_x));
		$kbEndOffsetY = intval(RevSliderFunctions::getVal($slideParams, 'kb_end_offset_y', $def_kb_end_offset_y));
		$kbStartRotate = intval(RevSliderFunctions::getVal($slideParams, 'kb_start_rotate', $def_kb_start_rotate));
		$kbEndRotate = intval(RevSliderFunctions::getVal($slideParams, 'kb_end_rotate', $def_kb_end_rotate));
        $kbBlurStart = intval(RevSliderFunctions::getVal($slideParams, 'kb_blur_start', $def_kb_blur_start));
        $kbBlurEnd = intval(RevSliderFunctions::getVal($slideParams, 'kb_blur_end', $def_kb_blur_end));
		/* END OF NEW KEN BURN INPUTS*/

		$bgRepeat = RevSliderFunctions::getVal($slideParams, 'bg_repeat', $def_bg_repeat);

		$slideBGExternal = RevSliderFunctions::getVal($slideParams, "slide_bg_external","");

		$img_sizes = RevSliderBase::get_all_image_sizes($slider_type);

		$bg_image_size = RevSliderFunctions::getVal($slideParams, 'image_source_type', $def_image_source_type);

		$style_wrapper = '';
		$class_wrapper = '';


		switch($bgType){
			case "trans":
				$divLayersClass = "slide_layers";
				$class_wrapper = "trans_bg";
			break;
			case "solid":
				$style_wrapper .= "background-color:".$slideBGColor.";";
			break;
			case "image":
				switch($slider_type){
					case 'posts':
						$imageUrl = Framework::$RS_PLUGIN_URL.'public/assets/assets/sources/post.png';
					break;
					case 'woocommerce':
						$imageUrl = Framework::$RS_PLUGIN_URL.'public/assets/assets/sources/wc.png';
					break;
					case 'facebook':
						$imageUrl = Framework::$RS_PLUGIN_URL.'public/assets/assets/sources/fb.png';
					break;
					case 'twitter':
						$imageUrl = Framework::$RS_PLUGIN_URL.'public/assets/assets/sources/tw.png';
					break;
					case 'instagram':
						$imageUrl = Framework::$RS_PLUGIN_URL.'public/assets/assets/sources/ig.png';
					break;
					case 'flickr':
						$imageUrl = Framework::$RS_PLUGIN_URL.'public/assets/assets/sources/fr.png';
					break;
					case 'youtube':
						$imageUrl = Framework::$RS_PLUGIN_URL.'public/assets/assets/sources/yt.png';
					break;
					case 'vimeo':
						$imageUrl = Framework::$RS_PLUGIN_URL.'public/assets/assets/sources/vm.png';
					break;
				}
				$style_wrapper .= "background-image:url('".$imageUrl."');";
				if($bgFit == 'percentage'){
					$style_wrapper .= "background-size: ".$bgFitX.'% '.$bgFitY.'%;';
				}else{
					$style_wrapper .= "background-size: ".$bgFit.";";
				}
				if($bgPosition == 'percentage'){
					$style_wrapper .= "background-position: ".$bgPositionX.'% '.$bgPositionY.'%;';
				}else{
					$style_wrapper .= "background-position: ".$bgPosition.";";
				}
				$style_wrapper .= "background-repeat: ".$bgRepeat.";";
			break;
			case "external":
				$style_wrapper .= "background-image:url('".$slideBGExternal."');";
				if($bgFit == 'percentage'){
					$style_wrapper .= "background-size: ".$bgFitX.'% '.$bgFitY.'%;';
				}else{
					$style_wrapper .= "background-size: ".$bgFit.";";
				}
				if($bgPosition == 'percentage'){
					$style_wrapper .= "background-position: ".$bgPositionX.'% '.$bgPositionY.'%;';
				}else{
					$style_wrapper .= "background-position: ".$bgPosition.";";
				}
				$style_wrapper .= "background-repeat: ".$bgRepeat.";";
			break;
		}

		if(!$slide->isStaticSlide()){
			//get static slide, check all layers and add them to the action list
			$static_slide_id = $slide->getStaticSlideID($sliderID);
			
			if($static_slide_id !== false){
				$static_slide = new RevSliderSlide($framework, $query, $curl, $filesystemHelper, $images, $resource, $googleFonts, $registerHelper);
				$static_slide->initByStaticID($static_slide_id);
				$static_layers = $static_slide->getLayers();
				$jsonStaticLayers = RevSliderFunctions::jsonEncodeForClientSide($static_layers);

                $this->assign(['jsonStaticLayers' => $jsonStaticLayers]);
			}
		}
		
		$this->assign([
			'dataHelper' => $dataHelper,
			'framework' => $framework,
			'query' => $query,
			'curl' => $curl,
			'filesystemHelper' => $filesystemHelper,
			'images' => $images,
			'resource' => $resource,
            'googleFonts' => $googleFonts,
            'registerHelper' => $registerHelper,
			'slideID' => $slideID,
			'patternViewSlide' => $patternViewSlide,
			'slide' => $slide,
			'slideParams' => $slideParams,
			'operations' => $operations,
			'arr_navigations' => $arr_navigations,
			'sliderID' => $sliderID,
			'slider' => $slider,
			'arrSlideNames' => $arrSlideNames,
			'arrSlides' => $arrSlides,
			'arrSliders' => $arrSliders,
			'arrSlidersFull' => $arrSlidersFull,
			'selectSliders' => $selectSliders,
			'sliderTemplate' => $sliderTemplate,
			'slideDelay' => $slideDelay,
			'imageUrl' => $imageUrl,
			'imageID' => $imageID,
			'slider_type' => $slider_type,
			'def_transition' => $def_transition,
			'def_transition_duration' => $def_transition_duration,
			'style' => $style,
			'divLayersWidth' => $divLayersWidth,
			'divbgminwidth' => $divbgminwidth,
			'maxbgwidth' => $maxbgwidth,
			'closeUrl' => $closeUrl,
			'jsonLayers' => $jsonLayers,
			'jsonFontFamilys' => $jsonFontFamilys,
			'jsonCaptions' => $jsonCaptions,
			'arrCssStyles' => $arrCssStyles,
			'arrCustomAnim' => $arrCustomAnim,
			'arrCustomAnimDefault' => $arrCustomAnimDefault,
			'bgType' => $bgType,
			'slideBGColor' => $slideBGColor,
			'divLayersClass' => $divLayersClass,
			'meta_handle' => $meta_handle,
			'bgFit' => $bgFit,
			'bgFitX' => $bgFitX,
			'bgFitY' => $bgFitY,
			'bgPosition' => $bgPosition,
			'bgPositionX' => $bgPositionX,
			'bgPositionY' => $bgPositionY,
			'slide_parallax_level' => $slide_parallax_level,
			'kenburn_effect' => $kenburn_effect,
			'kb_duration' => $kb_duration,
			'kb_easing' => $kb_easing,
			'kb_start_fit' => $kb_start_fit,
			'kb_end_fit' => $kb_end_fit,
			'ext_width' => $ext_width,
			'ext_height' => $ext_height,
			'use_parallax' => $use_parallax,
			'parallax_level' => $parallax_level,
			'parallaxisddd' => $parallaxisddd,
			'parallaxbgfreeze' => $parallaxbgfreeze,
			'slideBGYoutube' => $slideBGYoutube,
			'slideBGVimeo' => $slideBGVimeo,
			'slideBGhtmlmpeg' => $slideBGhtmlmpeg,
			'slideBGhtmlwebm' => $slideBGhtmlwebm,
			'slideBGhtmlogv' => $slideBGhtmlogv,
			'stream_do_cover' => $stream_do_cover,
			'stream_do_cover_both' => $stream_do_cover_both,
			'video_force_cover' => $video_force_cover,
			'video_dotted_overlay' => $video_dotted_overlay,
			'video_ratio' => $video_ratio,
			'video_loop' => $video_loop,
			'video_nextslide' => $video_nextslide,
			'video_force_rewind' => $video_force_rewind,
			'video_speed' => $video_speed,
			'video_mute' => $video_mute,
			'video_volume' => $video_volume,
			'video_start_at' => $video_start_at,
			'video_end_at' => $video_end_at,
			'video_arguments' => $video_arguments,
			'video_arguments_vim' => $video_arguments_vim,
			'kbStartOffsetX' => $kbStartOffsetX,
			'kbStartOffsetY' => $kbStartOffsetY,
			'kbEndOffsetX' => $kbEndOffsetX,
			'kbEndOffsetY' => $kbEndOffsetY,
			'kbStartRotate' => $kbStartRotate,
            'kbEndRotate' => $kbEndRotate,
            'kbBlurStart' => $kbBlurStart,
            'kbBlurEnd' => $kbBlurEnd,
			'bgRepeat' => $bgRepeat,
			'slideBGExternal' => $slideBGExternal,
			'img_sizes' => $img_sizes,
			'bg_image_size' => $bg_image_size,
			'style_wrapper' => $style_wrapper,
            'class_wrapper' => $class_wrapper,
            'mediafilter' => $mediafilter
		]);
	}

	/**
     * Add product slider related actions
     *
     * @return  string
     */

	public function addProductSliderActions() {
	    echo '<option <# if( data[\'action\'] == \'add_to_cart\' ){ #>selected="selected" <# } #>value="add_to_cart">' . __("Add to Cart") . '</option>' . "\n";
	}

}
