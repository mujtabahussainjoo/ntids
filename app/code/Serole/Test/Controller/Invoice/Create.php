<?php
   namespace Serole\Test\Controller\Invoice;

   use Magento\Framework\App\Action\Context;
   //use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
   use Magento\Backend\App\Action;
   use Magento\Framework\Exception\LocalizedException;
   use Magento\Framework\Registry;
   use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
   use Magento\Sales\Model\Order\Email\Sender\ShipmentSender;
   use Magento\Sales\Model\Order\ShipmentFactory;
   use Magento\Sales\Model\Order\Invoice;
   use Magento\Sales\Model\Service\InvoiceService;

   class Create extends \Magento\Framework\App\Action\Action{  //implements HttpPostActionInterface{

       public function __construct(
           Action\Context $context,
           Registry $registry,
           InvoiceSender $invoiceSender,
           ShipmentSender $shipmentSender,
           ShipmentFactory $shipmentFactory,
           InvoiceService $invoiceService
       ) {
           $this->registry = $registry;
           $this->invoiceSender = $invoiceSender;
           $this->shipmentSender = $shipmentSender;
           $this->shipmentFactory = $shipmentFactory;
           $this->invoiceService = $invoiceService;
           parent::__construct($context);
       }

       public function execute()
       {
           $orderId = $this->getRequest()->getParam('orderid');
           $order = $this->_objectManager->create(\Magento\Sales\Model\Order::class)->load($orderId);

           $invoiceItems = [];
           $invoice = $this->invoiceService->prepareInvoice($order);


           //$this->registry->register('current_invoice', $invoice);
           /*if (!empty($data['capture_case'])) {
               $invoice->setRequestedCaptureCase($data['capture_case']);
           }*/

           $invoice->register();

           //$invoice->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
           //$invoice->getOrder()->setIsInProcess(true);

           $transactionSave = $this->_objectManager->create(
               \Magento\Framework\DB\Transaction::class
           )->addObject(
               $invoice
           )->addObject(
               $invoice->getOrder()
           );

           $transactionSave->save();

       }
   }