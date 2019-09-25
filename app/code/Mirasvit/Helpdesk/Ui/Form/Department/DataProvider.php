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


namespace Mirasvit\Helpdesk\Ui\Form\Department;

use Mirasvit\Helpdesk\Api\Data\DepartmentInterface;
use Mirasvit\Helpdesk\Helper\Storeview;
use Mirasvit\Helpdesk\Model\ResourceModel\Department\CollectionFactory;
use Mirasvit\Helpdesk\Model\ResourceModel\Department;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\RequestInterface;

class DataProvider extends \Mirasvit\Helpdesk\Ui\Form\DataProvider
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Storeview $helpdeskStoreview,
        Department $departmentResource,
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
        $this->departmentResource = $departmentResource;
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
            'helpdesk/department/save',
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
        /** @var \Mirasvit\Helpdesk\Model\Department $department */
        foreach ($this->getCollection() as $department) {
            $this->departmentResource->afterLoad($department);

            $data[$department->getId()] = $department->getData();

            $storeId = (int) $this->request->getParam('store');
            $department->setStoreId($storeId);
            $name  = $this->helpdeskStoreview->getStoreViewValue($department, DepartmentInterface::KEY_NAME);
            $email = $this->helpdeskStoreview->getStoreViewValue(
                $department, DepartmentInterface::KEY_NOTIFICATION_EMAIL
            );
            $department->unsetData('store_id');
            $data[$department->getId()][DepartmentInterface::KEY_NAME] = $name;
            $data[$department->getId()][DepartmentInterface::KEY_NOTIFICATION_EMAIL] = $email;
        }
        return $data;
    }
}
