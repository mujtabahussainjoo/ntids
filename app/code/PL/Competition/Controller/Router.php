<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/21/2016
 * Time: 1:51 AM
 */
namespace PL\Competition\Controller;

class Router implements \Magento\Framework\App\RouterInterface{


    protected $actionFactory;

    protected $_eventManager;

    protected $_storeManager;

    protected $_competitionFactory;

    protected $_appState;

    protected $_url;

    protected $_response;

    protected $_competitionHelper;

    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\UrlInterface $url,
        \PL\Competition\Model\CompetitionFactory $competitionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResponseInterface $response,
        \PL\Competition\Helper\Data $helper
    ) {
        $this->actionFactory = $actionFactory;
        $this->_eventManager = $eventManager;
        $this->_url = $url;
        $this->_competitionFactory = $competitionFactory;
        $this->_storeManager = $storeManager;
        $this->_response = $response;
        $this->_competitionHelper = $helper;
    }

    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        $path = explode('/', $identifier);
       // echo $path[0]; exit;
        if($this->_competitionHelper->getPrefix() ==$path[0] || $path[0]=='competition'){
            if(count($path)==1){
                $request->setActionName('index');
            }
            if(count($path) > 1){
                $identifier = $path[1];
                $competition = $this->_competitionFactory->create();
                $competition_id = $competition->checkIdentifier($identifier, $this->_storeManager->getStore()->getId());
                if (!$competition_id) {
                    return null;
                }
                $request->setActionName('view');
                $request->setParam('id', $competition_id);
            }

            $request->setModuleName('competition');
            $request->setControllerName('competition');

            $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
            return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
        }
        return false;

    }
}