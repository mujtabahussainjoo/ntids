<?php

namespace Nwdthemes\Revslider\Block\Adminhtml;

use \Nwdthemes\Revslider\Helper\Data;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderAdmin;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderOperations;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderSlider;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderGlobals;

class SliderOverview extends \Magento\Backend\Block\Template {

	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Nwdthemes\Revslider\Helper\Framework $framework,
        \Nwdthemes\Revslider\Helper\Query $query,
        \Nwdthemes\Revslider\Helper\Curl $curl,
        \Nwdthemes\Revslider\Helper\Filesystem $filesystemHelper,
        \Nwdthemes\Revslider\Helper\Images $images,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\App\ResourceConnection $resource,
        \Nwdthemes\Revslider\Model\Revslider\GoogleFonts $googleFonts,
        \Nwdthemes\Revslider\Helper\Register $registerHelper
    ) {
		parent::__construct($context);

		$orders = false;
		//order=asc&ot=name&type=reg
		if(isset(Data::$_GET['ot']) && isset(Data::$_GET['order']) && isset(Data::$_GET['type'])){
			$order = array();
			switch(Data::$_GET['ot']){
				case 'alias':
					$order['alias'] = (Data::$_GET['order'] == 'asc') ? 'ASC' : 'DESC';
				break;
				case 'favorite':
					$order['favorite'] = (Data::$_GET['order'] == 'asc') ? 'ASC' : 'DESC';
				break;
				case 'name':
				default:
					$order['title'] = (Data::$_GET['order'] == 'asc') ? 'ASC' : 'DESC';
				break;
			}

			$orders = $order;
		}

		$slider = new RevSliderSlider($framework, $query, $curl, $filesystemHelper, $images, $resource, $googleFonts, $registerHelper);
        $operations = new RevSliderOperations($framework, $query, $curl, $filesystemHelper, $images, $resource, $googleFonts, $registerHelper);
		$arrSliders = $slider->getArrSliders($orders);
        $glob_vals = $operations->getGeneralSettingsValues();

		$addNewLink = RevSliderAdmin::getViewUrl(RevSliderAdmin::VIEW_SLIDER);


		$fav = $framework->get_option('rev_fav_slider', array());
		if($orders == false){ //sort the favs to top
			if(!empty($fav) && !empty($arrSliders)){
				$fav_sort = array();
				foreach($arrSliders as $skey => $sort_slider){
					if(in_array($sort_slider->getID(), $fav)){
						$fav_sort[] = $arrSliders[$skey];
						unset($arrSliders[$skey]);
					}
				}
				if(!empty($fav_sort)){
					//revert order of favs
					krsort($fav_sort);
					foreach($fav_sort as $fav_arr){
						array_unshift($arrSliders, $fav_arr);
					}
				}
			}
		}

		$exampleID = '"slider1"';
		if(!empty($arrSliders))
			$exampleID = '"'.$arrSliders[0]->getAlias().'"';
		
		$latest_version = $framework->get_option('revslider-latest-version', RevSliderGlobals::SLIDER_REVISION);
		$stable_version = $framework->get_option('revslider-stable-version', '4.1');

		$this->assign([
			'framework' => $framework,
			'query' => $query,
			'curl' => $curl,
			'filesystemHelper' => $filesystemHelper,
            'registerHelper' => $registerHelper,
			'images' => $images,
			'resource' => $resource,
			'googleFonts' => $googleFonts,
			'slider' => $slider,
			'arrSliders' => $arrSliders,
			'addNewLink' => $addNewLink,
			'exampleID' => $exampleID,
			'latest_version' => $latest_version,
			'stable_version' => $stable_version,
            'operations' => $operations,
            'glob_vals' => $glob_vals
		]);
	}
}
