<?php
 
   namespace Serole\Racvportal\Controller\Cart;

   use Magento\Framework\View\Result\PageFactory;


   class Ajax extends \Magento\Framework\App\Action\Action {

       protected $product;

       protected $cart;

       protected $formKey;
	   
	   protected $registry;

       protected $quoteManagement;

       protected $order;

       protected $invoice;

       protected $customerSession;

       protected $transaction;

       protected $creditmemoFactory;

       protected $invoiceObj;

       protected $creditmemoService;

       protected $store;

       protected $helper;

       protected $resourceConnection;

       protected $sageInventory;

       protected $sageHelper;

       protected $checkoutSession;

       protected $coreSession;

       protected $templateFilter;

       protected $shops;

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
                                   PageFactory $resultPageFactory){
           $this->resultPageFactory = $resultPageFactory;
           $this->eavConfig = $eavConfig;
           $this->product = $product;
           $this->cart = $cart;
           $this->formKey = $formKey;
		   $this->registry = $registry;
           $this->order = $order;
           $this->invoice = $invoice;
           $this->customerSession = $customerSession;
           $this->quoteManagement = $quoteManagement;
           $this->transaction = $transaction;
           $this->creditmemoFactory = $creditmemoFactory;
           $this->creditmemoService = $creditmemoService;
           $this->invoiceObj = $invoiceObj;
           $this->store = $store;
           $this->helper = $helper;
           $this->resourceConnection = $resourceConnection;
           $this->sageInventory = $sageInventory;
           $this->sageHelper = $sageHelper;
           $this->checkoutSession  = $checkoutSession;
           $this->coreSession = $coreSession;
           $this->resultRedirect = $result;
           $this->templateFilter = $templateFilter;
           $this->shops = $shops;
           parent::__construct($context);
       }

       public function execute(){
           $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Racvportal-ajaxcart.log');
           $logger = new \Zend\Log\Logger();
           $logger->addWriter($writer);

           $data = array();
           $items = array();
            try{
               if ($this->getRequest()->isAjax()) {
                   if ($this->getRequest()->getParams()) {
                       if ($this->helper->isCustomerLoggedIn()) {
                           $parms = $this->getRequest()->getParams();
                           if ($parms['addtocart']) {
                               if ($parms['qty'] && $parms['productId']) {
                                   $product = $this->product->load($parms['productId']);
                                   $items[0]['identifier'] = $product->getSku();
                                   $items[0]['type'] = "sku";
                                   $items[0]['qty'] = $parms['qty'];

                                   $sageResponse = $this->sageInventory->getSageStockCheck($items);

                                   if($sageResponse['error'] == 1){
                                       $data['outofstock'] = 1;
                                       $data['status'] = 'error';
                                       $data['message'] = $sageResponse['errorString'];
                                       $data['customersession'] = 'yes';
                                       echo json_encode($data);
                                   }else {
                                       $cartParams = array('form_key' => $this->formKey->getFormKey(),
                                           'product' => $parms['productId'],
                                           'qty' => $parms['qty']
                                       );

                                       $this->cart->addProduct($product, $cartParams);
                                       $this->cart->save();

                                       $resultPage = $this->resultPageFactory->create();
                                       $block = $resultPage->getLayout()
                                                            ->createBlock('Serole\Racvportal\Block\Ajaxcart')
                                                            ->setTemplate('Serole_Racvportal::ajaxcart.phtml');
                                       $htmlResponse = $block->toHtml();

                                       $data['html'] = $htmlResponse;
                                       $data['status'] = 'sucess';
                                       $data['productadded'] = 'yes';
                                       $data['customersession'] = 'yes';
                                       echo json_encode($data);
                                   }
                               } else {
                                   $data['status'] = 'error';
                                   $data['message'] = "something went wrong";
                                   echo json_encode($data);
                               }
                           } else {
                               #only cart ajax response
                               $itemsCount = $this->cart->getQuote()->getItemsCount();
                               $cartItems = 0;
                               if($itemsCount>0){
                                   $cartItems = 1;
                               }

                               $orderConfirmId = $this->coreSession->getOrderconfirmId();

                               $resultPage = $this->resultPageFactory->create();
                               $block = $resultPage->getLayout()
                                   ->createBlock('Serole\Racvportal\Block\Ajaxcart')
                                   ->setTemplate('Serole_Racvportal::ajaxcart.phtml');

                               $buttonBlock = $resultPage->getLayout()
                                   ->createBlock('Serole\Racvportal\Block\Ajaxcart')
                                   ->setTemplate('Serole_Racvportal::buttons.phtml')
                                   ->setData('data',$orderConfirmId) ;
                               $buttonHtmlResponse = $buttonBlock->toHtml();

                               //$block->assign(['swatchid' => "ramesh"]);

                               $htmlResponse = $block->toHtml();
                               $data['html'] = $htmlResponse;
                               $data['buttonhtml'] = $buttonHtmlResponse;
                               $data['status'] = 'sucess';
                               $data['customersession'] = 'yes';
                               $data['productadded'] = 'no';
                               $data['cartitems'] = $cartItems;
                               $data['isOrderPending'] = $orderConfirmId;
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