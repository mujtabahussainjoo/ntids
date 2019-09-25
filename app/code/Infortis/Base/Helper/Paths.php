<?php

namespace Infortis\Base\Helper;

class Paths
{
    public function getImportPath()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/infortis.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Deprecated method \Infortis\Base\Helper\Paths::getImportPath() was used.');
    }
}
