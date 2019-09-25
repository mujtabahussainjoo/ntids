<?php
namespace Wizkunde\WebSSO\Helper;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Certificate extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $dn = array(
        "countryName" => "NL",
        "stateOrProvinceName" => "Zuid-Holland",
        "localityName" => "Rotterdam",
        "organizationName" => "Wizkunde",
        "organizationalUnitName" => "SSO Integrations",
        "commonName" => "Sso Integration",
        "emailAddress" => "support@wizkunde.nl"
    );

    protected $configParams = array(
        'digest_alg' => 'sha256'
    );

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function createCertificate(){
        $privkey = openssl_pkey_new();

        if ($privkey === FALSE){
            return FALSE;
        }

        // generates a certificate signing request
        $csr = openssl_csr_new($this->dn, $privkey, $this->configParams);
        if ($csr === FALSE){
            return FALSE;
        }
        // This creates a self-signed cert that is valid for $duration days
        $sscert = openssl_csr_sign($csr, null, $privkey, 365, $this->configParams);

        if ($sscert === FALSE){
            return FALSE;
        }

        // expport the certificate and the private key
        openssl_x509_export($sscert, $certout);
        openssl_pkey_export($privkey, $pkout, null, $this->configParams);

        return array('cer'=>$certout, 'pem'=>$pkout);
    }
}
