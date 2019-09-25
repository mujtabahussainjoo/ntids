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
 * @package   mirasvit/module-helpdesk
 * @version   1.1.77
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Helpdesk\Controller\Contact;

use Magento\Framework\Controller\ResultFactory;
use Mirasvit\Helpdesk\Controller\Contact;
use Magento\Framework\App\ObjectManager;

class Kb extends Contact
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $om = ObjectManager::getInstance();

        $result = [
            'success' => true,
            'query'   => $this->getRequest()->getParam('s'),
            'html'    => $om->create('Mirasvit\Helpdesk\Block\Contact\Kb')->toHtml(),
        ];

        /** @var \Magento\Framework\App\Response\Http\Interceptor $response */
        $response = $this->getResponse();

        return $response->representJson(json_encode($result));
    }
}
