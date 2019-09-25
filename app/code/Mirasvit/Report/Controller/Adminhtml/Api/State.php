<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-report
 * @version   1.3.52
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Report\Controller\Adminhtml\Api;

use Magento\Backend\App\Action\Context;
use Mirasvit\Report\Service\StateService;

class State extends AbstractApi
{
    private $stateService;

    public function __construct(
        StateService $stateService,
        Context $context
    ) {
        $this->stateService = $stateService;

        parent::__construct($context);
    }

    public function execute()
    {
        $request = $this->getRequest();

        $identifier = $this->getRequest()->getParam('identifier');

        $state = [
            'columns'    => $request->getParam('columns'),
            'dimensions' => $request->getParam('dimensions'),
            'sortOrders' => $request->getParam('sortOrders'),
            'filters'    => $request->getParam('filters'),
            'pageSize'   => $request->getParam('pageSize'),
        ];

        //        $this->stateService->saveState($identifier, $state);

        /** @var \Magento\Framework\App\Response\Http $response */
        $response = $this->getResponse();
        $response->representJson(\Zend_Json::encode(
            ['success' => true]
        ));
    }
}