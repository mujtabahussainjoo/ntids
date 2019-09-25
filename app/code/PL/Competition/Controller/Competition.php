<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/21/2016
 * Time: 12:53 AM
 */
namespace PL\Competition\Controller;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
abstract class Competition extends Action{

    protected $_pageFactory;

    protected $_competitionFactory;

    protected $_competitionHelper;

    protected $_competitorFactory;

    protected $subscriberFactory;

    protected $storeManager;
    
    protected  $customerRepository;

    protected $customerFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \PL\Competition\Helper\Data $compettionHelper,
        \PL\Competition\Model\CompetitionFactory $competitionFactory,
        \PL\Competition\Model\CompetitorFactory $competitorFactory,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        CustomerRepository $customerRepository,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        parent::__construct($context);
        $this->_pageFactory = $pageFactory;
        $this->_competitionHelper = $compettionHelper;
        $this->_competitionFactory = $competitionFactory;
        $this->_competitorFactory = $competitorFactory;
        $this->subscriberFactory = $subscriberFactory;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
    }

    public function dispatch(RequestInterface $request)
    {
        return parent::dispatch($request);
    }
}