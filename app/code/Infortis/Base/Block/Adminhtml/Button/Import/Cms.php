<?php

namespace Infortis\Base\Block\Adminhtml\Button\Import;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\LayoutFactory;

class Cms extends Field
{
    /**
     * @var LayoutFactory
     */
    protected $_viewLayoutFactory;

    public function __construct(
        Context $context, 
        LayoutFactory $viewLayoutFactory,    
        array $data = []
    ) {
        $this->_viewLayoutFactory = $viewLayoutFactory;

        parent::__construct($context, $data);
    }

    /**
     * Import static blocks
     *
     * @param AbstractElement $element
     * @return String
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $elementOriginalData = $element->getOriginalData();     

        $params = str_replace("_", "/", $elementOriginalData['id']);

        $buttonLabel = '';
        if (isset($elementOriginalData['label']))
        {
            $buttonLabel = $elementOriginalData['label'];
        }

        $url = $this->getUrl('adminhtml/cmsimport/' . $params . '/package/Infortis_Base');
        
        $html = $this->_viewLayoutFactory->create()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setType('button')
            ->setClass('import-cms')
            ->setLabel('Import ' . $buttonLabel)
            ->setOnClick("setLocation('$url')")
            ->toHtml();
            
        return $html;
    }
}
