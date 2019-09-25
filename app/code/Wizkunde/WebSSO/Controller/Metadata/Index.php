<?php

namespace Wizkunde\WebSSO\Controller\Metadata;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $connectionFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Wizkunde\WebSSO\Connection\ConnectionFactory $connectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Wizkunde\WebSSO\Connection\ConnectionFactory $connectionFactory
    ) {
        $this->connectionFactory = $connectionFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $connection = $this->connectionFactory->create();

        if($connection->getConnection() !== null) {
            $this->getResponse()->setHeader(
                'Content-Type',
                "application/xml"
            );

            $this->getResponse()->setBody(
                $connection->getConnection()->getMetadataXml()
            );
        } else {
            $this->getResponse()->setHeader(
                'Content-Type',
                "text/html"
            );

            $this->getResponse()->setBody(
                'Please configure SSO for the frontend and come back to this URL'
            );
        }
    }
}
