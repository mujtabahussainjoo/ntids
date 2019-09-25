<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Model\ImportExport;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class ImportHandler extends AbstractImportExport
{
    /**
     * 2 different values needed because value can be mess of the numeric and string types, like:
     * [200000,"!20",12300000]
     */
    const ESCAPED_VALUE_1 = '`"';
    const ESCAPED_VALUE_2 = '"`';
    const UNESCAPED_VALUE_1 = ',"';
    const UNESCAPED_VALUE_2 = '",';

    /**
     * Store unrecognized row data here
     *
     * @var array
     */
    protected $unrecognizedRows = [];

    /**
     * Store rows which trows an error/exception
     *
     * @var array
     */
    protected $rowsThrowError = [];

    /**
     * Import Carriers, Methods, Rates from CSV file
     *
     * @param array $file file info retrieved from $_FILES array
     * @param array $entities
     * @return void
     * @throws LocalizedException
     * @throws \Exception
     */
    public function importFromCsvFile($file, $entities = [])
    {
        if (!isset($file['tmp_name'])) {
            throw new LocalizedException(__('Invalid file upload attempt.'));
        }

        $rawData = $this->csvProcessor->getData($file['tmp_name']);
        $headersAsArray = $this->parseHeaders($rawData[0]);
        $data = $this->filterRawData($rawData);

        foreach ($data as $dataIndex => $datum) {
            // Combine data with corresponding keys
            $keys = array_values($headersAsArray);
            $values = array_values($datum);
            $result = array_combine($keys, $values);

            // Recognize object entity type: is it a carrier, method or rate
            $type = !empty($result[static::ENTITY_TYPE]) ? $result[static::ENTITY_TYPE] : null;
            if (empty($type) || empty($this->entitiesMap[$type])) {
                // Do not process unrecognized entities
                $this->unrecognizedRows[$dataIndex] = $datum;
                continue;
            }

            // Detect ignored columns
            $entityInterfaceName = $this->entitiesMap[$type];
            $classInstance = $this->objectManager->get($entityInterfaceName);
            if ($classInstance instanceof \MageWorx\ShippingRules\Api\ImportExportEntity) {
                $ignoredColumns = $classInstance::getIgnoredColumnsForImportExport();
                foreach ($ignoredColumns as $ignoredColumnKey) {
                    unset($result[$ignoredColumnKey]);
                }
            }

            // Compose method for the entity to save or update a data
            $method = 'saveUpdate' . ucfirst($type);
            if (!method_exists($this, $method)) {
                throw new \Exception(__('Unrecognized method %1', $method));
            }

            // Make update or insert new data
            try {
                /** @var \Magento\Framework\DataObject $datumDataObject */
                $resultDataObject = $this->dataObjectFactory->create($result);
                $this->{$method}($resultDataObject);
            } catch (\Exception $e) {
                $this->rowsThrowError[$dataIndex] = $datum;
                $this->messageManager->addErrorMessage(
                    __(
                        'Import row with index %1 fails because it cause the error: %2',
                        $dataIndex,
                        $e->getMessage()
                    )
                );
                continue;
            }
        }
    }

    /**
     * Parse headers from the first line of the CSV file
     *
     * @param array $firstRow
     * @return array
     */
    private function parseHeaders($firstRow)
    {
        $headers = [];
        foreach ($firstRow as $column) {
            $headers[] = implode('_', explode(' ', trim(mb_strtolower($column))));
        }

        return $headers;
    }

    /**
     * Prepare data before processing
     *
     * @param array $data
     * @return array
     */
    private function filterRawData($data)
    {
        $result = [];
        foreach ($data as $key => $value) {
            foreach ($value as $fieldName => $fieldValue) {
                $result[$key][$fieldName] = $this->unEscape($fieldValue);
            }
        }

        return $result;
    }

    /**
     * Replace escaped value tio the regular one
     *
     * @param $value
     * @return string
     */
    protected function unEscape($value)
    {
        $result = str_ireplace(static::ESCAPED_VALUE_1, static::UNESCAPED_VALUE_1, $value);
        $result = str_ireplace(static::ESCAPED_VALUE_2, static::UNESCAPED_VALUE_2, $result);

        return $result;
    }

    /**
     * Save or update carrier
     *
     * @param \Magento\Framework\DataObject $data
     * @return \MageWorx\ShippingRules\Model\Carrier
     * @throws LocalizedException
     */
    protected function saveUpdateCarrier(\Magento\Framework\DataObject $data)
    {
        $id = $this->useIds() ? $data->getData(static::ENTITY_ID) : null;
        $code = $data->getData('carrier_code');
        $newObject = false;

        try {
            if ($id) {
                $carrier = $this->carrierRepository->getById($id);
            } elseif ($code) {
                $carrier = $this->carrierRepository->getByCode($code);
            }
        } catch (NoSuchEntityException $e) {
            $carrier = $this->carrierRepository->getEmptyEntity();
            $newObject = true;
        } finally {
            if (!isset($carrier)) {
                $carrier = $this->carrierRepository->getEmptyEntity();
                $newObject = true;
            }
        }

        foreach ($data->getData() as $propertyKey => $property) {
            if ($this->isJson($property)) {
                $property = json_decode($property, true);
            } elseif (empty($property)) {
                $property = null;
            }

            $setterMethod = 'set' . implode('', array_map('ucfirst', explode('_', $propertyKey)));
            if (method_exists($carrier, $setterMethod)) {
                $carrier->{$setterMethod}($property);
            } else {
                $carrier->setData($propertyKey, $property);
            }
        }

        // Make new object when id exists but there is nothing in database
        if ($newObject) {
            $carrier->isObjectNew(true);
            $carrier->setId(null);
        }

        return $this->carrierRepository->save($carrier);
    }

    /**
     * Save or update method
     *
     * @param \Magento\Framework\DataObject $data
     * @return \MageWorx\ShippingRules\Model\Carrier\Method
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    protected function saveUpdateMethod(\Magento\Framework\DataObject $data)
    {
        $id = $this->useIds() ? $data->getData(static::ENTITY_ID) : null;
        $code = $data->getData('code');
        $newObject = false;

        try {
            if ($id) {
                $method = $this->methodRepository->getById($id);
            } elseif ($code) {
                $method = $this->methodRepository->getByCode($code);
            }
        } catch (NoSuchEntityException $e) {
            $method = $this->methodRepository->getEmptyEntity();
            $newObject = true;
        } finally {
            if (!isset($method)) {
                $method = $this->methodRepository->getEmptyEntity();
                $newObject = true;
            }
        }

        // Carrier code should be specified for a data without id (new entity)
        $carrierCode = $data->getData('carrier_code');
        if ($carrierCode) {
            $carrier = $this->carrierRepository->getByCode($carrierCode);
            if (!$carrier->getId()) {
                throw new NoSuchEntityException(__('Carrier with the code %1 does not exists', $carrierCode));
            }
            $data->setData('carrier_id', $carrier->getId());
        }
        
        foreach ($data->getData() as $propertyKey => $property) {
            if ($this->isJson($property)) {
                $property = json_decode($property, true);
            } elseif (empty($property)) {
                $property = null;
            }

            $setterMethod = 'set' . implode('', array_map('ucfirst', explode('_', $propertyKey)));
            if (method_exists($method, $setterMethod)) {
                $method->{$setterMethod}($property);
            } else {
                $method->setData($propertyKey, $property);
            }
        }

        // Make new object when id exists but there is nothing in database
        if ($newObject) {
            $method->isObjectNew(true);
            $method->setId(null);
        }

        return $this->methodRepository->save($method);
    }

    /**
     * Save or update rate
     *
     * @param \Magento\Framework\DataObject $data
     * @return \MageWorx\ShippingRules\Model\Carrier\Method\Rate
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    protected function saveUpdateRate(\Magento\Framework\DataObject $data)
    {
        $id = $this->useIds() ? $data->getData(static::ENTITY_ID) : null;
        $code = $data->getData('rate_code');
        $newObject = false;

        try {
            if ($id) {
                $rate = $this->rateRepository->getById($id);
            } elseif ($code) {
                $rate = $this->rateRepository->getByCode($code);
            }
        } catch (NoSuchEntityException $e) {
            $rate = $this->rateRepository->getEmptyEntity();
            $newObject = true;
        } finally {
            if (!isset($rate)) {
                $rate = $this->rateRepository->getEmptyEntity();
                $newObject = true;
            }
        }

        // Method code should be specified for a data without id (new entity)
        $methodCode = $data->getData('method_code');
        if ($methodCode) {
            $method = $this->methodRepository->getByCode($methodCode);
            if (!$method->getId()) {
                throw new NoSuchEntityException(__('Method with the code %1 does not exists', $methodCode));
            }
            $data->setData('method_id', $method->getId());
        }
        
        foreach ($data->getData() as $propertyKey => $property) {
            if ($this->isJson($property)) {
                $property = json_decode($property, true);
            } elseif (empty($property)) {
                $property = null;
            }

            $setterMethod = 'set' . implode('', array_map('ucfirst', explode('_', $propertyKey)));
            if (method_exists($rate, $setterMethod)) {
                $rate->{$setterMethod}($property);
            } else {
                $rate->setData($propertyKey, $property);
            }
        }

        // Make new object when id exists but there is nothing in database
        if ($newObject) {
            $rate->isObjectNew(true);
            $rate->setId(null);
        }

        return $this->rateRepository->save($rate);
    }

    /**
     * Check is it a valid json string
     *
     * @source https://stackoverflow.com/a/6041773/3699902
     * @param $string
     * @return bool
     */
    protected function isJson($string) {
        json_decode($string);

        return (json_last_error() == JSON_ERROR_NONE);
    }
}