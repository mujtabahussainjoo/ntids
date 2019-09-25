<?php

namespace Wizkunde\WebSSO\Connection\SAML2\Service;

use \RobRichards\XMLSecLibs\XMLSecurityKey;

class Signature
{
    private $serverHelper;
    private $certificate;
    private $signature;
    private $encryption;
    private $serverInfo;

    /**
     * SAML2 constructor.
     * @param \Wizkunde\WebSSO\Helper\Server $serverHelper
     * @param \Wizkunde\SAMLBase\Certificate $certificate
     * @param \Wizkunde\SAMLBase\Security\Signature $signature
     * @param \Wizkunde\SAMLBase\Security\Encryption $encryption
     */
    public function __construct(
        \Wizkunde\WebSSO\Helper\Server $serverHelper,
        \Wizkunde\SAMLBase\Certificate $certificate,
        \Wizkunde\SAMLBase\Security\Signature $signature,
        \Wizkunde\SAMLBase\Security\Encryption $encryption
    ) {
    
        $this->serverHelper = $serverHelper;
        if (isset($this->serverHelper->getServerInfo()['type_saml2'])) {
            $this->serverInfo = $this->serverHelper->getServerInfo()['type_saml2'];
        }

        $this->signature = $signature;
        $this->certificate = $certificate;
        $this->encryption = $encryption;
    }

    public function getCertificateService()
    {
        $algorithm = (isset($this->serverInfo['algorithm']) && $this->serverInfo['algorithm'] == 'sha1')
            ? XMLSecurityKey::RSA_SHA1
            : XMLSecurityKey::RSA_SHA256;

        if (isset($this->serverInfo['passphrase'])) {
            $this->certificate->setPassphrase($this->serverInfo['passphrase']);
        }

        if (isset($this->serverInfo['crt_data'])) {
            $this->certificate->setPublicKey($this->serverInfo['crt_data'], false, $algorithm);
        }

        if (isset($this->serverInfo['pem_data'])) {
            $this->certificate->setPrivateKey($this->serverInfo['pem_data'], false, $algorithm);
        }

        return $this->certificate;
    }

    public function getSigningService()
    {
        $this->signature->setCertificate($this->getCertificateService());

        return $this->signature;
    }

    public function getEncryptionService()
    {
        $this->encryption->setCertificate($this->getCertificateService());

        return $this->encryption;
    }
}
