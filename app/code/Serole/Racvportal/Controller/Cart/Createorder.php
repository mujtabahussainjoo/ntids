<?php

namespace Serole\Racvportal\Controller\Cart;

use Magento\Framework\View\Result\PageFactory;

class Createorder extends \Serole\Racvportal\Controller\Cart\Ajax {

    public $_sku;

    public $_skuQty;

    public function __construct(\Magento\Framework\App\Action\Context $context,
	                            \Magento\Framework\Registry $registry,
                                \Magento\Eav\Model\Config $eavConfig,
                                \Magento\Catalog\Model\Product $product,
                                \Magento\Framework\Data\Form\FormKey $formKey,
                                \Magento\Checkout\Model\Cart $cart,
                                \Magento\Quote\Model\QuoteManagement $quoteManagement,
                                \Magento\Sales\Model\Order $order,
                                \Magento\Sales\Model\Service\InvoiceService $invoice,
                                \Magento\Customer\Model\Session $customerSession,
                                \Magento\Framework\DB\Transaction $transaction,
                                \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory,
                                \Magento\Sales\Model\Order\Invoice $invoiceObj,
                                \Magento\Sales\Model\Service\CreditmemoService $creditmemoService,
                                \Magento\Store\Model\StoreManagerInterface $store,
                                \Serole\Racvportal\Helper\Data $helper,
                                \Serole\Sage\Model\Inventory $sageInventory,
                                \Serole\Sage\Helper\Data $sageHelper,
                                \Magento\Checkout\Model\Session $checkoutSession,
                                \Magento\Framework\App\ResourceConnection $resourceConnection,
                                \Magento\Framework\Session\SessionManagerInterface $coreSession,
                                \Magento\Framework\Controller\ResultFactory $result,
                                \Magento\Cms\Model\Template\FilterProvider $templateFilter,
                                \Serole\Racvportal\Model\Ravportal $shops,
                                PageFactory $resultPageFactory)
    {
        parent::__construct($context, $registry, $eavConfig, $product, $formKey, $cart, $quoteManagement, $order, $invoice, $customerSession, $transaction, $creditmemoFactory, $invoiceObj, $creditmemoService, $store, $helper, $sageInventory, $sageHelper, $checkoutSession, $resourceConnection, $coreSession, $result, $templateFilter, $shops, $resultPageFactory);
        $this->_sku  = array();
        $this->_skuQty  = array();
    }

    public function execute(){
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Racvportal-ajaxcart-createorder.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $data = array();
        $items = array();
        $sageError = 0;

        try{
           if ($this->getRequest()->isAjax()) {
                    $customerSession = $this->customerSession;
                    if ($customerSession->isLoggedIn()) {
                        $customerData = $this->customerSession->getCustomerData();
                        $parms = $this->getRequest()->getParams();
                        $shopData = $this->helper->getShopData();
                        if ($shopData) {

                            $address = ['firstname' => $customerData->getFirstname(),
                                        'lastname' => $customerData->getLastname(),
                                        'street' => $shopData['street'],
                                        'city' => $shopData['suburb'],
                                        'country_id' => 'AU',
                                        'region' => $shopData['region'],
                                        'postcode' => $shopData['postcode'],
                                        'telephone' => $shopData['phone'],
                                        'fax' => '',
                                    ];

                            $this->_quoteId = $this->cart->getQuote()->getId();
                            $cartItems = $this->cart->getQuote()->getAllItems();

                            $i=0;
                            foreach ($cartItems as $key => $cartItem) {
                                $logger->info("in-side".$i);
                                $items[$key]['identifier'] = $cartItem->getSku();
                                $items[$key]['type'] = "sku";
                                $items[$key]['qty'] = $cartItem->getQty();

                                $prod = $this->sageHelper->getProductBySku($cartItem->getSku());
                                $typeId = $prod->getTypeId();
                                if($typeId == "bundle"){
                                    $this->getBundleProductOptionsData($prod, $cartItem->getQty(), $i);
                                }else{
                                    $isStockItem = $prod->getIsStockItem();
                                    if(isset($isStockItem) && $isStockItem == 1){
                                        $itemSku = $cartItem->getSku();
                                        $itemQty = $cartItem->getQty();
                                        $quoteId = $this->_quoteId;
                                        $this->_stockUpdateArray[] = "$quoteId,$itemSku,$itemQty,1";
                                        $this->_sku[] = $prod->getSku();
                                        $this->_skuQty[$i][trim($prod->getSku())]['qty'] = $cartItem->getQty();
                                        $this->_skuQty[$i][trim($prod->getSku())]['type'] = "not-bundle";
                                        $this->_skuQty[$i][trim($prod->getSku())]['bundle-sku'] = "NA";
                                    }
                                }
                               $i++;
                            }

                            $sageResponse = $this->sageInventory->getSageStockCheck($items);

                            if($sageResponse['error'] == 1){
                                $data['outofstock'] = 1;
                                $data['status'] = 'error';
                                $data['message'] = $sageResponse['errorString'];
                                $data['customersession'] = 'yes';
                                echo json_encode($data);
                            }else {
								 if(!empty($this->_stockUpdateArray))
								 {
									$logger->info($this->_stockUpdateArray);
									$updateResult = $this->sageInventory->stockUpdate($this->_stockUpdateArray);
									if($updateResult['error'] == 1){
										$data['status'] = 'error';
										$data['message'] = $updateResult["errorString"];
										$data['customersession'] = 'yes';
										$data['sageerror'] = 'yes';
										echo json_encode($data);
									}
								   else
								   {
										$this->cart->getQuote()->getBillingAddress()->addData($address);
										$this->cart->getQuote()->getShippingAddress()->addData($address);
										$shippingAddress = $this->cart->getQuote()->getShippingAddress();
										$shippingAddress->setCollectShippingRates(true)
														->collectShippingRates()
														->setShippingMethod('freeshipping_freeshipping'); //shipping method

										$this->cart->getQuote()->setPaymentMethod('portalpayment'); //payment method
										$this->cart->getQuote()->setInventoryProcessed(false); //not effetc inventory

										$this->cart->save(); //Now Save quote and your quote is ready

										$this->cart->getQuote()->getPayment()->importData(['method' => 'portalpayment']);
										$this->cart->getQuote()->collectTotals()->save();
										

										$order = $this->quoteManagement->submit($this->cart->getQuote());
										$order->setState('pending', true);
										$order->setStatus('pending', true);
										$order->save();

										$incrementId = $order->getRealOrderId();
										if ($incrementId) {
											$this->coreSession->setOrderconfirmId($incrementId);
											//$this->checkoutSession->setTestramesh($incrementId);
											$resultPage = $this->resultPageFactory->create();
											$block = $resultPage->getLayout()
												->createBlock('Serole\Racvportal\Block\Ajaxcart')
												->setTemplate('Serole_Racvportal::ajaxcart.phtml');
											$htmlResponse = $block->toHtml();
											$data['html'] = $htmlResponse;
											$data['status'] = 'sucess';
											$data['orderid'] = $incrementId;
											$data['customersession'] = 'yes';
											
											echo json_encode($data);
										}
							      }
                                }
                            }
                        } else {
                            $data['status'] = 'error';
                            $data['message'] = "You don't have Billing Address, Please contact us";
                            $data['customersession'] = 'yes';
                            echo json_encode($data);
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
        }catch(\Exception $e){
            $logger->info($e->getMessage());
            $data['status'] = 'error';
            $data['message'] = $e->getMessage();
            $data['customersession'] = 'yes';
            echo json_encode($data);
        }
    }

    public function getBundleProductOptionsData($product, $qty, $i){
        //get all the selection products used in bundle product.
        //$product = $this->_productFactory->create()->load($productId);

        $selectionCollection = $product->getTypeInstance(true)
            ->getSelectionsCollection(
                $product->getTypeInstance(true)->getOptionsIds($product),
                $product
            );
        $quoteId = $this->_quoteId;
        foreach ($selectionCollection as $proselection) {
            $chldProd = $this->sageHelper->getProductBySku($proselection->getSku());
            $isStockItm = $chldProd->getIsStockItem();
            if(isset($isStockItm) && $isStockItm == 1) {
                $itemSku = $proselection->getSku();
                $itemQty = $qty*$proselection->getSelectionQty();
                $this->_stockUpdateArray[] = "$quoteId,$itemSku,$itemQty,1";
                $this->_sku[] = $proselection->getSku();
                $this->_skuQty[$i][trim($proselection->getSku())]['qty'] = $qty*$proselection->getSelectionQty();
                $this->_skuQty[$i][trim($proselection->getSku())]['type'] = "bundle";
                $this->_skuQty[$i][trim($proselection->getSku())]['bundle-sku'] = $product->getSku();
            }
        }

    }

}

/*
      Array(
          [entity_id] => 2
          [name] => Bendigo RACV Shop
          [street] => 112 Mitchell Street
          [suburb] => Bendigo
          [region] => 0
          [postcode] => 3550
          [phone] => 03 5443 9622
          [status] => 1
          [updated_at] => 2017-10-01 23:17:49
          [created_at] => 2017-10-01 23:17:45
          [store_id] => 0
       )*/