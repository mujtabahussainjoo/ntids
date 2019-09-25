<?php

namespace Serole\Racvportal\Controller\Cart;

use Magento\Framework\View\Result\PageFactory;

class Confirmorder extends \Serole\Racvportal\Controller\Cart\Ajax {

    public function execute(){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/ajaxcart-confirm-order.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        try {
            if ($this->getRequest()->isAjax()) {
                if ($this->getRequest()->getParams()) {
                    $customerSession = $this->customerSession;
                    if ($customerSession->isLoggedIn()) {
                        $incrementId = $this->getRequest()->getParam('orderId');
                        if($incrementId) {
                            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                            /*
                            $order = $objectManager->create(\Magento\Sales\Model\Order::class)->loadByIncrementId($incrementId);
                            $invoiceItems = [];
                            $invoice = $objectManager->create(\Magento\Sales\Model\Service\InvoiceService::class)->prepareInvoice($order);
                            $invoice->register();
                            $transactionSave = $objectManager->create(
                                \Magento\Framework\DB\Transaction::class
                            )->addObject(
                                $invoice
                            )->addObject(
                                $invoice->getOrder()
                            );
                            $transactionSave->save();
                            //$order->setState("complete");
                            $order->setStatus("complete");
                            $order->save();*/

                            $order = $objectManager->create(\Magento\Sales\Model\Order::class)->loadByIncrementId($incrementId);
                            if ($order->canInvoice()) {
                                $invoiceObj = $this->invoice->prepareInvoice($order);
                                $invoiceObj->setRequestedCaptureCase('offline');
                                $invoiceObj->register();
                                //$invoiceObj->setState(\Magento\Sales\Model\Order\Invoice::STATE_PAID);
                                $invoiceObj->getOrder()->setIsInProcess(true);
                                $invoiceObj->save();
                            }
                            //$transactionSave = $this->transaction->addObject($invoiceObj)->addObject($invoiceObj->getOrder());
                            $transactionSave = $objectManager->create(
                                \Magento\Framework\DB\Transaction::class
                            )->addObject(
                                $invoiceObj
                            )->addObject(
                                $invoiceObj->getOrder()
                            );
                            $transactionSave->save();

                            //$order->setState(\Magento\Sales\Model\Order::STATE_COMPLETE, true);
                            $order->setStatus(\Magento\Sales\Model\Order::STATE_COMPLETE, true);
                            $order->save();
                        }
                         $this->coreSession->unsOrderconfirmId();
                         $shopId = $this->helper->getShopId();
                         $shopName = $this->helper->getShopName();

                         $sql = "insert into racvportal (incrementid,shop_id,shop_name) values ($incrementId,$shopId,'$shopName')";
                         $connection = $this->resourceConnection->getConnection();
                         $result = $connection->query($sql);

                        $baseUrl = $this->helper->getStoreBaseUrl();
                        $url = $baseUrl . "racvportal/pdf/download/incrementid/" . $incrementId;

                        $baseRootDir = $this->helper->getRootDir();
                        $fileName = $incrementId.".pdf";
                        $filePath = $baseRootDir."/neatideafiles/pdf/".$fileName;

                        $resultPage = $this->resultPageFactory->create();
                        $block = $resultPage->getLayout()
                                            ->createBlock('Serole\Racvportal\Block\Ajaxcart')
                                            ->setTemplate('Serole_Racvportal::ajaxcart.phtml');
                        $htmlResponse = $block->toHtml();
                        $data['html'] = $htmlResponse;
                        $data['status'] = 'sucess';
                        $data['customersession'] = 'yes';
                        $data['orderid'] = $incrementId;
                        $data['url'] = $url;
                        if(file_exists($filePath)) {
                            $data['filestatus'] = "yes";
                        }else{
                            $data['filestatus'] = "no";
                        }

                        echo json_encode($data);
                       // }
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
        }catch (\Exception $e){
            $logger->info($e->getMessage());
            $data['status'] = 'error';
            $data['message'] = $e->getMessage();
            echo json_encode($data);
        }
    }
}