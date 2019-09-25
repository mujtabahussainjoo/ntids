<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\ImportExport;

use MageWorx\ShippingRules\Api\Data\CarrierInterface;
use MageWorx\ShippingRules\Api\Data\MethodInterface;
use MageWorx\ShippingRules\Api\Data\RateInterface;

class ExportHandler extends AbstractImportExport
{
    /**
     * Get content as a CSV string
     *
     * @param array $entities - list of available entities
     * @param array $ids - format: ['entity1_code' => [id1,id2,...], ...]
     * @return string
     */
    public function getContent($entities = [], $ids = [])
    {
        if (empty($entities)) {
            $entities = $this->getAllAvailableEntitiesName();
        }

        $headers = $this->getHeaders($entities);
        $template = $this->createStringCsvTemplate($headers);
        // Add header (titles)
        $content[] = $headers->toString($template);

        foreach ($entities as $entity) {
            $getEntityDataMethodName = 'get' . ucfirst($entity) . 's';
            if (!method_exists($this, $getEntityDataMethodName)) {
                continue;
            }

            $entityIds = empty($ids[$entity]) ? [] : $ids[$entity];
            $data = $this->$getEntityDataMethodName($entityIds);

            foreach ($data as $datum) {
                if (!$datum instanceof \Magento\Framework\DataObject) {
                    continue;
                }
                $datum->addData(
                    [
                        'ie_type' => $entity,
                        'id' => $datum->getId()
                    ]
                );
                $content[] = $datum->toString($template);
            }
        }

        // Add system data: time, host, template
        if ($this->useSystemData()) {
            $content[] = '"",""';
            $content[] = '"Timestamp:","' . $this->getTime() . '"';
            $content[] = '"Host:","' . $this->getHost() . '"';
            $content[] = '"Template:","' . str_replace(',"', '`"', $template) . '"';
        }

        $contentAsAString = implode("\n", $content);

        return $contentAsAString;
    }

    /**
     * Create data template from headers
     *
     * @param \Magento\Framework\DataObject $headers
     * @return string
     */
    private function createStringCsvTemplate(\Magento\Framework\DataObject $headers)
    {
        $data = $headers->getData();
        $templateData = [];
        foreach ($data as $propertyKey => $value) {
            $templateData[] = '"{{' . $propertyKey . '}}"';
        }
        $template = implode(',', $templateData);

        return $template;
    }

    /**
     * @param array $ids
     * @return CarrierInterface[]
     */
    private function getCarriers($ids = [])
    {
        if (empty($this->carriers)) {
            if (!empty($ids)) {
                $this->searchCriteriaBuilder->addFilter(
                    CarrierInterface::ENTITY_ID_FIELD_NAME,
                    $ids,
                    'in'
                );
            }
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $this->carriers = $this->carrierRepository
                ->getList($searchCriteria, true)
                ->getItems();
        }

        return $this->carriers;
    }

    /**
     * @param array $ids
     * @return MethodInterface[]
     */
    private function getMethods($ids = [])
    {
        if (empty($this->methods)) {
            if (!empty($ids)) {
                $this->searchCriteriaBuilder->addFilter(
                    MethodInterface::ENTITY_ID_FIELD_NAME,
                    $ids,
                    'in'
                );
            }
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $this->methods = $this->methodRepository
                ->getList($searchCriteria, true)
                ->getItems();
        }

        return $this->methods;
    }

    /**
     * @param array $ids
     * @return RateInterface[]
     */
    private function getRates($ids = [])
    {
        if (empty($this->rates)) {
            if (!empty($ids)) {
                $this->searchCriteriaBuilder->addFilter(
                    RateInterface::ENTITY_ID_FIELD_NAME,
                    $ids,
                    'in'
                );
            }
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $this->rates = $this->rateRepository
                ->getList($searchCriteria, true)
                ->getItems();
        }

        return $this->rates;
    }
}