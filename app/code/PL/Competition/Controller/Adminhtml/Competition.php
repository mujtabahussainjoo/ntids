<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/20/2016
 * Time: 11:09 AM
 */
namespace PL\Competition\Controller\Adminhtml;

abstract class Competition extends \Magento\Backend\App\Action{

    protected $_coreRegistry;


    protected $_resultPageFactory;


    protected $_competitionFactory;

    protected $_competitorFactory;

    protected $compHelper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \PL\Competition\Model\CompetitionFactory $competitionFactory,
        \PL\Competition\Model\CompetitorFactory $competitorFactory,
        \PL\Competition\Helper\Data $compHelper
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_competitionFactory = $competitionFactory;
        $this->_competitorFactory = $competitorFactory;
        $this->compHelper = $compHelper;

    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('PL_Competition::competition');
    }
}