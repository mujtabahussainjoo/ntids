<?php

namespace Nwdthemes\Revslider\Model\Config\Source;

class Revslider implements \Magento\Framework\Option\ArrayInterface {

    protected $_revSliderSlider;
        
	public function __construct(
        \Nwdthemes\Revslider\Model\Revslider\RevSliderAdmin $revSliderAdmin,
        \Nwdthemes\Revslider\Model\Revslider\RevSliderSlider $revSliderSlider
    ) {
        $this->_revSliderSlider = $revSliderSlider;
	}

	public function toOptionArray() {
        $options = array();
		foreach ($this->_revSliderSlider->getArrSliders() as $slider) {
			$options[] = [
                'value' => $slider->getAlias(),
                'label' => $slider->getTitle()
            ];
		}
		return $options;
	}

    public function toArray() {
        $options = array();
		foreach ($this->_revSliderSlider->getArrSliders() as $slider) {
			$options[$slider->getAlias()] = $slider->getTitle();
		}
		return $options;
    }

}