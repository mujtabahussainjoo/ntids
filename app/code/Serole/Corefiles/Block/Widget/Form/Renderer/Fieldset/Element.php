<?php
    namespace Serole\Corefiles\Block\Widget\Form\Renderer\Fieldset;

    use \Magento\Framework\Data\Form\Element\AbstractElement;
    use \Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

    class Element extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element{

        protected $_element;

        protected $_template = 'Serole_Corefiles::widget/form/renderer/fieldset/element.phtml';


        public function getElement(){
            return $this->_element;
        }

        public function render(AbstractElement $element){
            $this->_element = $element;
            return $this->toHtml();
        }
    }