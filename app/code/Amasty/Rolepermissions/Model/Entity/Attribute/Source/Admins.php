<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Model\Entity\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\ObjectManagerInterface;

class Admins extends AbstractSource
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     *
     * @internal param \Magento\Eav\Model\ResourceModel\Entity\AttributeFactory $eavAttrEntity
     * @codeCoverageIgnore
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $users = $this->objectManager->create('Magento\User\Model\ResourceModel\User\Collection');

            $this->_options = [[
                'label' => __('- Not Specified -'),
                'value' => 0
            ]];

            foreach ($users as $user) {
                $this->_options []= [
                    'label' => $user->getFirstname() . ' ' . $user->getLastname(),
                    'value' => $user->getId()
                ];
            }
        }
        return $this->_options;
    }
}
