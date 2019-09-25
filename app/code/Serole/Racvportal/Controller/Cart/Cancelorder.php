<?php

namespace Serole\Racvportal\Controller\Cart;

use Magento\Framework\View\Result\PageFactory;

class Cancelorder extends \Serole\Racvportal\Controller\Cart\Ajax {

    public function execute(){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/ajaxcart-order-cancel.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        try {
            if ($this->getRequest()->isAjax()) {
                if ($this->getRequest()->getParams()) {
                    $logger->info($this->getRequest()->getParams());
                    $customerSession = $this->customerSession;
                    if($customerSession->isLoggedIn()) {
                        $incrementId = $this->getRequest()->getParam('orderId');
                        $order = $this->order->loadByIncrementId($incrementId);
                        $order->setState(\Magento\Sales\Model\Order::STATE_CANCELED, true);
                        $order->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED, true);
                        $order->save();

                        $this->coreSession->unsOrderconfirmId();

                        $resultPage = $this->resultPageFactory->create();
                        $block = $resultPage->getLayout()
                                            ->createBlock('Serole\Racvportal\Block\Ajaxcart')
                                            ->setTemplate('Serole_Racvportal::ajaxcart.phtml');
                        $htmlResponse = $block->toHtml();
                        $data['html'] = $htmlResponse;
                        $data['status'] = 'sucess';
                        $data['customersession'] = 'yes';
                        echo json_encode($data);
                    }else{
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
        }catch (\Exception $e){
            $logger->info($e->getMessage());
            $data['status'] = 'error';
            $data['message'] = $e->getMessage();
            echo json_encode($data);
        }
    }

}