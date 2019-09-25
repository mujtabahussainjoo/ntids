<?php

namespace Wizkunde\WebSSO\Api;

/**
 * Ensure we can create certificates
 *
 * Interface CertificateManagementInterface
 * @package Wizkunde\WebSSO\Api
 */
interface CertificateManagementInterface
{
    /**
     * @api
     *
     * @param Data\CertificateInterface $certificate
     * @return Data\CertificateInterface $certificate
     */
    public function generateCertificate(Data\CertificateInterface $certificate);
}
