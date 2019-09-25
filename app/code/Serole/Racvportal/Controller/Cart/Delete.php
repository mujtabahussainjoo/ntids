<?php

namespace Serole\Racvportal\Controller\Cart;

use Magento\Framework\View\Result\PageFactory;

class Delete extends \Serole\Racvportal\Controller\Cart\Ajax {

    public function execute(){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Racvportal-ajaxcart-delete.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $data = array();

        try{
            if ($this->getRequest()->isAjax()) {
                if ($this->getRequest()->getParams()) {
                    $logger->info($this->getRequest()->getParams());
                    $customerSession = $this->customerSession;
                    if ($customerSession->isLoggedIn()) {
                        $parms = $this->getRequest()->getParams();
                        if ($parms['itemId']) {
                            $this->cart->removeItem($parms['itemId']);
                            $this->cart->save();
                            $resultPage = $this->resultPageFactory->create();
                            $block = $resultPage->getLayout()
                                    ->createBlock('Serole\Racvportal\Block\Ajaxcart')
                                    ->setTemplate('Serole_Racvportal::ajaxcart.phtml');
                            $htmlResponse = $block->toHtml();
                            $data['html'] = $htmlResponse;
                            $data['status'] = 'sucess';
                            $data['message'] = $parms['itemId'];
                            $data['customersession'] = 'yes';
                            echo json_encode($data);

                        } else {
                            #only cart ajax response
                            $resultPage = $this->resultPageFactory->create();
                            $block = $resultPage->getLayout()
                                ->createBlock('Serole\Racvportal\Block\Ajaxcart')
                                ->setTemplate('Serole_Racvportal::ajaxcart.phtml');
                            //$block->assign(['swatchid' => "ramesh"]);
                            $htmlResponse = $block->toHtml();
                            $data['html'] = $htmlResponse;
                            $data['status'] = 'error';
                            $data['message'] = 'something went wrong';
                            $data['customersession'] = 'yes';
                            echo json_encode($data);
                            //echo "ramesh";
                        }
                    } else {
                        #customer not log-in action
                        $resultPage = $this->resultPageFactory->create();
                        $block = $resultPage->getLayout()
                            ->createBlock('Serole\Racvportal\Block\Ajaxcart')
                            ->setTemplate('Serole_Racvportal::customersession.phtml');
                        //$htmlResponse = $this->getResponse()->setBody($block->toHtml());
                        $htmlResponse = $block->toHtml();
                        $data['html'] = $htmlResponse;
                        $data['status'] = 'sucess';
                        $data['customersession'] = 'no';
                        echo json_encode($data);
                    }
                }
            }
        }catch(\Exception $e){
            $logger->info($e->getMessage());
            $data['status'] = 'error';
            $data['message'] = $e->getMessage();
            echo json_encode($data);
        }
    }
}