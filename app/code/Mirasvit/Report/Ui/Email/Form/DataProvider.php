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



namespace Mirasvit\Report\Ui\Email\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\Report\Api\Data\EmailInterface;
use Mirasvit\Report\Api\Repository\EmailRepositoryInterface;

class DataProvider extends AbstractDataProvider
{
    public function __construct(
        EmailRepositoryInterface $emailRepository,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $emailRepository->getCollection();

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $result = [];

        foreach ($this->collection as $email) {
            $data = [
                EmailInterface::ID        => $email->getId(),
                EmailInterface::TITLE     => $email->getTitle(),
                EmailInterface::IS_ACTIVE => $email->getIsActive(),
                EmailInterface::SUBJECT   => $email->getSubject(),
                EmailInterface::RECIPIENT => $email->getRecipient(),
                EmailInterface::SCHEDULE  => $email->getSchedule(),
                EmailInterface::BLOCKS    => $email->getBlocks(),
            ];

            $result[$email->getId()] = $data;
        }

        return $result;
    }
}
