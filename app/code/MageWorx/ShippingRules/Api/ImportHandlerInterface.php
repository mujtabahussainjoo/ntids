<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Api;

interface ImportHandlerInterface
{
    /**
     * Import Carriers, Methods, Rates from CSV file
     *
     * @param mixed[] $file file info retrieved from $_FILES array
     * @param mixed[] $entities
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function importFromCsvFile($file, $entities = []);
}