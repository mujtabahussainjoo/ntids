<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Surcharge
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Fooman\Surcharge\Controller\Adminhtml\Manage;

class Edit extends \Magento\Backend\App\Action
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Fooman\Surcharge\Model\Config
     */
    private $suchargeConfig;

    /**
     * @var \Fooman\Surcharge\Model\SurchargeFactory
     */
    private $surchargeFactory;

    /**
     * @param \Magento\Backend\App\Action\Context        $context
     * @param \Magento\Framework\Registry                $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Fooman\Surcharge\Model\Config $surchargeConfig,
        \Fooman\Surcharge\Model\SurchargeFactory $surchargeFactory
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->suchargeConfig = $surchargeConfig;
        $this->surchargeFactory = $surchargeFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $surchargeType = preg_replace('[^a-z]', '', $this->getRequest()->getParam('type'));
        $surcharge = $this->surchargeFactory->create();

        if ($id) {
            $surcharge->load($id);
            $surchargeType = $surcharge->getType();
        } elseif (empty($surchargeType)) {
            $surchargeType = $this->suchargeConfig->getTypes()[0]['type'];
        }
        $this->coreRegistry->register('fooman_surcharge', $surcharge);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Surcharges'));
        $resultPage->setActiveMenu('Fooman_Surcharge::surcharge');
        $resultPage->getConfig()->getTitle()->prepend($surcharge->getId() ? __('Edit Surcharge') : __('New Surcharge'));
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock(\Fooman\Surcharge\Block\Adminhtml\Surcharge\Edit::class)
        );
        $resultPage->addLeft($resultPage->getLayout()->createBlock(
            \Fooman\Surcharge\Block\Adminhtml\Surcharge\Edit\Tabs::class,
            'surcharge_tabs',
            ['data' => ['surcharge_type' => $surchargeType]]
        ));

        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Fooman_Surcharge::surcharge');
    }
}
