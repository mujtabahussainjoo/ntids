<?php

namespace Wizkunde\WebSSO\Controller\Adminhtml;

use Magento\Framework\Registry;

abstract class Server extends \Magento\Backend\App\AbstractAction
{
    private $resultPageFactory;
    protected $serverModelFactory;
    protected $oauth2ModelFactory;
    protected $saml2ModelFactory;
    protected $mappingCollectionFactory;
    protected $mappingModelFactory;
    private $coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Wizkunde\WebSSO\Model\ServerFactory $serverModelFactory
     * @param \Wizkunde\WebSSO\Connection\SAML2\Model\TypeFactory $saml2ModelFactory
     * @param \Wizkunde\WebSSO\Connection\OAuth2\Model\TypeFactory $oauth2ModelFactory
     * @param \Wizkunde\WebSSO\Model\ResourceModel\Mapping\CollectionFactory $mappingCollectionFactory
     * @param \Wizkunde\WebSSO\Model\MappingFactory $mappingModelFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Wizkunde\WebSSO\Model\ServerFactory $serverModelFactory,
        \Wizkunde\WebSSO\Connection\SAML2\Model\TypeFactory $saml2ModelFactory,
        \Wizkunde\WebSSO\Connection\OAuth2\Model\TypeFactory $oauth2ModelFactory,
        \Wizkunde\WebSSO\Model\ResourceModel\Mapping\CollectionFactory $mappingCollectionFactory,
        \Wizkunde\WebSSO\Model\MappingFactory $mappingModelFactory
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $pageFactory;
        $this->serverModelFactory = $serverModelFactory;
        $this->oauth2ModelFactory = $oauth2ModelFactory;
        $this->saml2ModelFactory = $saml2ModelFactory;
        $this->mappingCollectionFactory = $mappingCollectionFactory;
        $this->mappingModelFactory = $mappingModelFactory;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function getResultPageFactory()
    {
        return $this->resultPageFactory;
    }

    /**
     * @return Registry|null
     */
    public function getCoreRegistry()
    {
        return $this->coreRegistry;
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }

    /**
     * Retrieve well-formed admin user data from the form input
     *
     * @param array $data
     * @return array
     */

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Wizkunde_WebSSO::server');
    }
}
