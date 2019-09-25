<?php
namespace WeltPixel\GoogleTagManager\Controller\Index;

/**
 * Workaround for sections.xml to be able to set the custom dimensions via CustomerData
 * Class Dimensions
 * @package WeltPixel\GoogleTagManager\Controller\Index
 */
class Dimensions extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            $this->_redirect('/');
            return;
        }

        $jsonData = json_encode(array('result' => true));
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }
}
