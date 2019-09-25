<?php

namespace Serole\Racvportal\Controller\Cart;

use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{

    protected $orderCollectionFactory;

    protected $json;

    public function __construct(Context $context,
                                \Magento\Framework\Controller\Result\JsonFactory $json,
                                \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory)
    {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->json = $json;
        parent::__construct($context);
    }

    public function execute()
    {
        echo "1234";
    }

}

