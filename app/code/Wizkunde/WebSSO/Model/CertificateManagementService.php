<?php

namespace Wizkunde\WebSSO\Model;

class CertificateManagementService implements \Wizkunde\WebSSO\Api\CertificateManagementInterface
{
    /**
     * @param \Wizkunde\WebSSO\Api\Data\CertificateInterface $certificate
     * @return \Wizkunde\WebSSO\Api\Data\CertificateInterface
     */
    public function generateCertificate(\Wizkunde\WebSSO\Api\Data\CertificateInterface $certificate)
    {
        $privkey = openssl_pkey_new(array(
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ));

        $dn = array(
            "countryName" => $certificate->getCountryName(),
            "stateOrProvinceName" => $certificate->getStateOrProvinceName(),
            "localityName" => $certificate->getLocalityName(),
            "organizationName" => $certificate->getOrganizationName(),
            "organizationalUnitName" => $certificate->getOrganizationalUnitName(),
            "commonName" => $certificate->getCommonName(),
            "emailAddress" => $certificate->getEmailAddress()
        );

        $csr = openssl_csr_new($dn, $privkey, array('digest_alg' => 'sha256'));
        $x509 = openssl_csr_sign($csr, null, $privkey, $days=365, array('digest_alg' => 'sha256'));

        openssl_csr_export($csr, $csrout);
        openssl_x509_export($x509, $certout);
        openssl_pkey_export($privkey, $pkeyout);

        $certificate->setCsr($csrout);
        $certificate->setPrivateKey($pkeyout);
        $certificate->setCertificate($certout);

        return $certificate;
    }
}
