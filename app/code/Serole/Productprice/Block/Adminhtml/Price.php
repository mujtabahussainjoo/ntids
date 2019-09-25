<?php

namespace Serole\Productprice\Block\Adminhtml;

class Price extends \Magento\Backend\Block\Widget\Container
{

    protected $_template = 'productprice/productprice.phtml';


    public function __construct(\Magento\Backend\Block\Widget\Context $context,array $data = [])
    {
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {

        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Serole\Productprice\Block\Adminhtml\Price\Grid', 'productprice.grid')
        );
        return parent::_prepareLayout();
    }


    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

}