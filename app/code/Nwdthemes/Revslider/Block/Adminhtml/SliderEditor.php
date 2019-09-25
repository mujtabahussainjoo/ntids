<?php

namespace Nwdthemes\Revslider\Block\Adminhtml;

use \Nwdthemes\Revslider\Helper\Data;
use \Nwdthemes\Revslider\Model\Revslider\Framework\RevSliderFunctions;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderAdmin;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderOperations;
use \Nwdthemes\Revslider\Model\Revslider\RevSliderSlider;

class SliderEditor extends \Magento\Backend\Block\Template {

	/**
	 * Constructor
	 */

	public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroupCollection,
        \Nwdthemes\Revslider\Helper\Framework $framework,
        \Nwdthemes\Revslider\Helper\Query $query,
        \Nwdthemes\Revslider\Helper\Curl $curl,
        \Nwdthemes\Revslider\Helper\Filesystem $filesystemHelper,
        \Nwdthemes\Revslider\Helper\Images $images,
        \Magento\Framework\App\ResourceConnection $resource,
        \Nwdthemes\Revslider\Model\Revslider\GoogleFonts $googleFonts,
        \Nwdthemes\Revslider\Helper\Register $registerHelper
    ) {
		parent::__construct($context);

		//get taxonomies with cats
		$postTypesWithCats = RevSliderOperations::getPostTypesWithCatsForClient();
		$jsonTaxWithCats = RevSliderFunctions::jsonEncodeForClientSide($postTypesWithCats);

		//check existing slider data:
		$sliderID = $this->getRequest()->getParam('id');

		$arrFieldsParams = array();

		$uslider = new RevSliderSlider($framework, $query, $curl, $filesystemHelper, $images, $resource, $googleFonts, $registerHelper);

        $customerGroups = array();
        foreach($customerGroupCollection->toOptionArray() as $group) {
            $customerGroups[] = $group;
        }

        if(!empty($sliderID)){

			$slider = new RevSliderSlider($framework, $query, $curl, $filesystemHelper, $images, $resource, $googleFonts, $registerHelper);
			$slider->initByID($sliderID);

			//get setting fields
			$settingsFields = $slider->getSettingsFields();
			$arrFieldsMain = $settingsFields['main'];
			$arrFieldsParams = $settingsFields['params'];

			$linksEditSlides = $framework->getBackendUrl(RevSliderAdmin::VIEW_SLIDE,'id=new&slider='.intval($sliderID));

			$this->assign([
				'framework' => $framework,
				'query' => $query,
				'curl' => $curl,
				'filesystemHelper' => $filesystemHelper,
                'registerHelper' => $registerHelper,
				'images' => $images,
				'resource' => $resource,
				'googleFonts' => $googleFonts,
				'postTypesWithCats' => $postTypesWithCats,
				'jsonTaxWithCats' => $jsonTaxWithCats,
				'sliderID' => $sliderID,
				'arrFieldsParams' => $arrFieldsParams,
				'uslider' => $uslider,
				'slider' => $slider,
				'linksEditSlides' => $linksEditSlides,
                'customerGroups' => $customerGroups
			]);
			$this->setTemplate('Nwdthemes_Revslider::templates/edit-slider.phtml');
			
		}else{
			
			$this->assign([
				'framework' => $framework,
				'query' => $query,
				'curl' => $curl,
				'filesystemHelper' => $filesystemHelper,
                'registerHelper' => $registerHelper,
				'images' => $images,
				'resource' => $resource,
				'googleFonts' => $googleFonts,
				'postTypesWithCats' => $postTypesWithCats,
				'jsonTaxWithCats' => $jsonTaxWithCats,
				'sliderID' => $sliderID,
				'arrFieldsParams' => $arrFieldsParams,
				'uslider' => $uslider,
                'customerGroups' => $customerGroups
			]);
			$this->setTemplate('Nwdthemes_Revslider::templates/create-slider.phtml');
			
		}
	}
}
