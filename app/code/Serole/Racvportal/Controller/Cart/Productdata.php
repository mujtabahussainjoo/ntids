<?php

namespace Serole\Racvportal\Controller\Cart;

use Magento\Framework\View\Result\PageFactory;

class Productdata extends \Serole\Racvportal\Controller\Cart\Ajax {

    public function execute(){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Racvportal-ajaxcart-productdata.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

            if ($this->getRequest()->isAjax()) {
                if ($this->getRequest()->getParam('productid')) {
                    $customerSession = $this->customerSession;
                    try {
                        if ($customerSession->isLoggedIn()) {
                            $prodcutId = $this->getRequest()->getParam('productid');
                            $store = $this->store->getStore()->getId();
                            $productData = array();
                            $description = $this->product->getResource()->getAttributeRawValue($prodcutId, 'description', $store);
                            $productData['desscription'] = $this->getDescription($description);
                            $productData['productName'] = $this->product->getResource()->getAttributeRawValue($prodcutId, 'name', $store);
                            $resultPage = $this->resultPageFactory->create();
                            $block = $resultPage->getLayout()
                                                ->createBlock('Serole\Racvportal\Block\Ajaxcart')
                                                ->setTemplate('Serole_Racvportal::productdetails.phtml')
                                                ->setData('data',$productData);
                            $htmlResponse = $block->toHtml();
                            $data['html'] = $htmlResponse;
                            $data['status'] = 'sucess';
                            $data['customersession'] = 'yes';
                            echo json_encode($data);
                        } else {
                            $resultPage = $this->resultPageFactory->create();
                            $block = $resultPage->getLayout()
                                ->createBlock('Serole\Racvportal\Block\Ajaxcart')
                                ->setTemplate('Serole_Racvportal::ajaxcart.phtml');
                            //$block->assign(['swatchid' => "ramesh"]);
                            $htmlResponse = $block->toHtml();
                            $data['html'] = $htmlResponse;
                            $data['status'] = 'sucess';
                            $data['customersession'] = 'yes';
                            echo json_encode($data);
                        }
                    }catch (\Exception $e){
                        $logger->info($e->getMessage());
                        $data['status'] = 'error';
                        echo json_encode($data);
                    }
                }
           }
    }


    public function getDescription($description){
        return $this->templateFilter->getBlockFilter()->filter($description);
    }

}