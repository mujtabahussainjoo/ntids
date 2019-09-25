<?php

namespace Serole\Pdf\Controller\Adminhtml\Pdf;

use Magento\Backend\App\Action;


class Filedownload extends \Magento\Backend\App\Action
{


    protected $orderObj;

    private $coreRegistry = null;

    private $resultPageFactory;

    private $backSession;

    private $pdfHelper;


    public function __construct(Action\Context $context,
                                \Magento\Framework\View\Result\PageFactory $resultPageFactory,
                                \Magento\Framework\Registry $registry,
                                \Serole\Pdf\Helper\Pdf $pdfHelper,
                                \Magento\Sales\Model\Order $orderObj)
    {

        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        $this->backSession = $context->getSession();
        $this->order = $orderObj;
        $this->pdfHelper = $pdfHelper;
        parent::__construct($context);

    }

    public function execute(){
        $orderId = $this->getRequest()->getParam('orderid');
        if($orderId){
            $pdfUrl = "https://store.neatideas.com.au/var/orderPdf/".$orderId.".pdf";
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary");
            header("Content-disposition: attachment; filename=\"" . basename($pdfUrl) . "\"");
            readfile($pdfUrl);
        }
    }
}