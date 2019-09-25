<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;

class Owner extends AbstractModifier
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var \Amasty\Rolepermissions\Model\Entity\Attribute\Source\Admins
     */
    private $admins;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    private $authorization;

    public function __construct(
        ArrayManager $arrayManager,
        \Amasty\Rolepermissions\Model\Entity\Attribute\Source\Admins $admins,
        \Magento\Framework\AuthorizationInterface $authorization
    ) {
        $this->arrayManager = $arrayManager;
        $this->admins = $admins;
        $this->authorization = $authorization;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        foreach ($meta as $sectionName => &$section) {
            if (isset($section['children']['container_amrolepermissions_owner'])) {

                if (!$this->authorization->isAllowed('Amasty_Rolepermissions::product_owner')) {
                    return $this->arrayManager->remove(
                        "$sectionName/children/container_amrolepermissions_owner",
                        $meta
                    );
                } else {
                    $owner['arguments']['data']['config'] = [
                        'formElement' => 'select',
                        'dataType' => 'select',
                        'options' => $this->admins->getAllOptions()
                    ];

                    return $this->arrayManager->merge(
                        "$sectionName/children/container_amrolepermissions_owner/children/amrolepermissions_owner",
                        $meta,
                        $owner
                    );
                }
            }
        }

        return $meta;
    }
}
