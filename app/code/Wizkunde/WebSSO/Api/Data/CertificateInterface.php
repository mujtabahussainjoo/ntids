<?php

namespace Wizkunde\WebSSO\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface CertificateInterface extends ExtensibleDataInterface
{
    /**
     * @return string
     */
    public function getCountryName();

    /**
     * @param $countryName
     * @return void
     */
    public function setCountryName($countryName);

    /**
     * @return string
     */
    public function getStateOrProvinceName();

    /**
     * @param $stateOrProvinceName
     * @return void
     */
    public function setStateOrProvinceName($stateOrProvinceName);

    /**
     * @return string
     */
    public function getLocalityName();

    /**
     * @param $localityName
     * @return void
     */
    public function setLocalityName($localityName);

    /**
     * @return string
     */
    public function getOrganizationName();

    /**
     * @param $organizationName
     * @return void
     */
    public function setOrganizationName($organizationName);

    /**
     * @return string
     */
    public function getOrganizationalUnitName();

    /**
     * @param $organizationalUnitName
     * @return void
     */
    public function setOrganizationalUnitName($organizationalUnitName);

    /**
     * @return string
     */
    public function getCommonName();

    /**
     * @param $commonName
     * @return void
     */
    public function setCommonName($commonName);

    /**
     * @return string
     */
    public function getEmailAddress();

    /**
     * @param $emailAddress
     * @return void
     */
    public function setEmailAddress($emailAddress);

    /**
     * @return string
     */
    public function getCsr();

    /**
     * @param $csr
     * @return void
     */
    public function setCsr($csr);

    /**
     * @return string
     */
    public function getCertificate();

    /**
     * @param $certificate
     * @return void
     */
    public function setCertificate($certificate);

    /**
     * @return string
     */
    public function getPrivateKey();

    /**
     * @param $privateKey
     * @return void
     */
    public function setPrivateKey($privateKey);
}
