<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\ShippingRules\Api;

interface ImportExportEntity
{
    /**
     * Get columns which should be removed during import\export process
     *
     * @return mixed[]
     */
    public static function getIgnoredColumnsForImportExport();
}