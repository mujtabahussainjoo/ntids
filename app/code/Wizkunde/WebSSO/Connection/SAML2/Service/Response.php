<?php

namespace Wizkunde\WebSSO\Connection\SAML2\Service;

class Response
{
    private $response;

    public function __construct(
        \Wizkunde\WebSSO\Connection\SAML2\Service\Signature $signature,
        \Wizkunde\SAMLBase\Response\AuthnResponse $response
    ) {
    
        $this->response = $response;
        $this->response->setSignatureService($signature->getSigningService());
        $this->response->setEncryptionService($signature->getEncryptionService());
    }

    public function getResponseService()
    {
        return $this->response;
    }
}
