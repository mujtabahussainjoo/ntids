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


namespace Mirasvit\Helpdesk\Ui\Form\Status;

use Mirasvit\Helpdesk\Api\Data\StatusInterface;
use Mirasvit\Helpdesk\Helper\Storeview;
use Mirasvit\Helpdesk\Model\Config;
use Mirasvit\Helpdesk\Model\ResourceModel\Status\CollectionFactory;
use Mirasvit\Helpdesk\Model\ResourceModel\Status;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\RequestInterface;

class DataProvider extends \Mirasvit\Helpdesk\Ui\Form\DataProvider
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Storeview $helpdeskStoreview,
        Status $statusResource,
        CollectionFactory $collectionFactory,
        UrlInterface $url,
        RequestInterface $request,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->helpdeskStoreview  = $helpdeskStoreview;
        $this->statusResource   = $statusResource;
        $this->collection         = $collectionFactory->create();
        $this->url                = $url;
        $this->request            = $request;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigData()
    {
        $config = parent::getConfigData();

        $config['submit_url'] = $this->url->getUrl(
            'helpdesk/status/save',
            [
                'id'    => (int) $this->request->getParam('id'),
                'store' => (int) $this->request->getParam('store'),
            ]
        );

        return $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = [];
        /** @var \Mirasvit\Helpdesk\Model\Status $status */
        foreach ($this->getCollection() as $status) {
            $this->statusResource->afterLoad($status);

            $data[$status->getId()] = $status->getData();
            $data[$status->getId()]['disabled'] = $this->isCodeEditable($status);

            $storeId = (int) $this->request->getParam('store');
            $status->setStoreId($storeId);

            $name  = $this->helpdeskStoreview->getStoreViewValue($status, StatusInterface::KEY_NAME);
            $data[$status->getId()][StatusInterface::KEY_NAME] = $name;

            $status->unsetData('store_id');
        }

        return $data;
    }

    /**
     * @param \Mirasvit\Helpdesk\Model\Status $status
     * @return bool
     */
    private function isCodeEditable($status)
    {
        if (in_array($status->getCode(), [Config::STATUS_CLOSED, Config::STATUS_OPEN])) {
            return true;
        }

        return false;
    }
}
