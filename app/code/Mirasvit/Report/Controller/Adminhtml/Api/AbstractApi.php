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

use Magento\Backend\App\Action;

abstract class AbstractApi extends Action
{
    public function __construct(
        Action\Context $context
    ) {
        parent::__construct($context);

        try {
            $payload = \Zend_Json::decode(file_get_contents('php://input'));
        } catch (\Exception $e) {
            $payload = [];
        }

        if (is_array($payload)) {
            /** @var \Magento\Framework\App\Request\Http $request */
            $request = $this->getRequest();
            foreach ($payload as $key => $value) {
                $request->setParam($key, $value);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function _processUrlKeys()
    {
        return true;
    }
}