<?php

namespace Infortis\Cgen\Controller\Dynamic;

class Assets extends \Magento\Framework\App\Action\Action
{
    protected $frameworkViewLayout;
    protected $resultPageFactory;
    protected $response;

    /**
     * @var \Infortis\Cgen\Helper\Definitions
     */
    protected $configHelper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Layout $frameworkViewLayout
     * @param \Infortis\Cgen\Helper\Definitions $configHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Layout $frameworkViewLayout,
        \Infortis\Cgen\Helper\Definitions $configHelper
    ) {
        $this->response = $context->getResponse();
        $this->frameworkViewLayout = $frameworkViewLayout;
        $this->configHelper = $configHelper;

        return parent::__construct($context);
    }
    
    public function execute()
    {
        // // TODO:
        // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/my.log');
        // $logger = new \Zend\Log\Logger();
        // $logger->addWriter($writer);
        // $logger->info("\n"); ///
        // $logger->info('>>> Infortis\Cgen\Controller\Dynamic\Assets:' ); ///

        $output = '';
        $moduleShort = $this->getRequest()->getParam('m');
        $moduleName = $this->configHelper->getModuleName($moduleShort);
        $moduleAssets = $this->configHelper->getModuleAssets($moduleName);
        foreach ($moduleAssets as $id => $assetInfo)
        {
            $template = $moduleName . '::' . $assetInfo['template'];
            $tag = $assetInfo['cache_tag'];
            // $logger->info(" "); ///
            // $logger->info($id . ' => ' . $tag .', ' . $template ); ///

            $output .= $this->frameworkViewLayout
                ->createBlock("Infortis\Cgen\Block\Asset\Css", 'Cgen.'.$id, ['data' => ['dynamic_asset_tag' => $tag]])
                ->setData('area', 'frontend')
                ->setTemplate($template)
                ->toHtml();
        }

        $this->response
            ->setHeader('Content-Type', 'text/css')
            ->setBody($output);

        return $this->response;
    }
}
