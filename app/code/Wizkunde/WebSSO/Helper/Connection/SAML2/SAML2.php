<?php

namespace Wizkunde\WebSSO\Connection\SAML2;

class SAML2
{
    protected $objectManager;

    protected $serverInfo;

    protected $metadataInformation;

    protected $configuration = [];

    protected $binding;
    
    public function __construct()
    {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    public function setServerInfo($serverInfo)
    {
        $this->serverInfo = $serverInfo;
    }

    /**
     * Resolve the SAML2 Metadata from the IDP
     * @return mixed
     */
    protected function resolveMetadata()
    {
        $metadataClass = $this->objectManager->create('Wizkunde\SAMLBase\Metadata\IDPMetadata');
        $resolver = $this->objectManager->create('Wizkunde\SAMLBase\Metadata\ResolveService');

        $this->metadataInformation =  $resolver->resolve($metadataClass, $this->serverInfo['metadata_url']);
    }

    protected function setupConfiguration()
    {
        $twigLoader = $this->objectManager->create('Twig_Loader_Filesystem');
        $twigLoader->addPath(BP . '/lib/internal/Wizkunde/SAMLBase/Template/Twig');

        $twigEnvironment = $this->objectManager->create('Twig_Environment');
        $twigEnvironment->setLoader($twigLoader);

        $this->configuration['twig'] = $twigEnvironment;
    }

    protected function getCertificateService()
    {
        $signingCertificate = $this->objectManager->create('Wizkunde\SAMLBase\Certificate');
        $signingCertificate->setPublicKey($this->serverInfo['crt_data']);
        $signingCertificate->setPrivateKey($this->serverInfo['pem_data']);

        return $signingCertificate;
    }

    protected function getSignatureService()
    {
        $signatureClass = $this->objectManager->create('Wizkunde\SAMLBase\Security\Signature');
        $signatureClass->setCertificate($this->getCertificateService());

        return $signatureClass;
    }

    protected function getEncryptionService()
    {
        $signatureClass = $this->objectManager->create('Wizkunde\SAMLBase\Security\Encryption');
        $signatureClass->setCertificate($this->getCertificateService());

        return $signatureClass;
    }


    /**
     * Setup the binding to handle a dataset
     *
     * @param $bindingType
     */
    protected function setupBinding($bindingType)
    {
        if ($bindingType == 'post') {
            $this->binding = $this->objectManager->create('Wizkunde\SAMLBase\Binding\Post');
        } elseif ($bindingType == 'artifact') {
            $this->binding = $this->objectManager->create('Wizkunde\SAMLBase\Binding\Artifact');
        } else {
            $this->binding = $this->objectManager->create('Wizkunde\SAMLBase\Binding\Redirect');
        }

        $this->binding->setMetadata($this->metadataInformation);
        $this->binding->setTwigService($this->configuration['twig']);
        $this->binding->setUniqueIdService($this->objectManager->create('Wizkunde\SAMLBase\Configuration\UniqueID'));
        $this->binding->setTimestampService($this->objectManager->create('Wizkunde\SAMLBase\Configuration\Timestamp'));
        $this->binding->setSignatureService($this->getSignatureService());
        $this->binding->setHttpService($this->objectManager->create('GuzzleHttp\Client'));
        $this->binding->setSettings($this->getIdpSettings());
    }

    /**
     * PRepare all the IDP settings
     *
     * @return mixed
     */
    protected function getIdpSettings()
    {
        $idpSettings = $this->objectManager->create('Wizkunde\SAMLBase\Configuration\Settings');
        $mappingHelper = $this->objectManager->get('\Magento\Framework\App\State');

        $returnUrl = ($mappingHelper->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML) ? '/admin/' : '/';

        $idpSettings->setValues([
                'NameID' => $this->serverInfo['name_id'],
                'Issuer' => $this->serverInfo['name_id'],
                'MetadataExpirationTime' => $this->serverInfo['metadata_expiration'],
                'SPReturnUrl' => $returnUrl,
                'ForceAuthn' => (int)$this->serverInfo['forceauthn'],
                'IsPassive' => (int)$this->serverInfo['is_passive'],
                'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
                'ComparisonLevel' => 'exact',
                'OptionalURLParameters'   =>
                    [
                        'source' => 'saml'
                    ]
            ]);

        return $idpSettings;
    }

    /**
     * Setup the SAML request
     */
    public function setup()
    {
        $this->resolveMetadata();
        $this->setupConfiguration();
        $this->setupBinding($this->serverInfo['sso_binding']);
    }

    /**
     * Do the actual authentication
     */
    public function authenticate()
    {
        $this->binding->request('AuthnRequest');
    }

    /**
     * Resolve artifact data
     *
     * @param $artifactData
     * @return mixed
     */
    public function resolveArtifact($artifactData)
    {
        $this->setupBinding('artifact');
        return $this->binding->resolveArtifact($artifactData);
    }

    /**
     * @param $SAMLData
     * @return mixed
     */
    public function handleResponse($SAMLData)
    {
        $responseClass = $this->objectManager->create('Wizkunde\SAMLBase\Response\AuthnResponse');
        $responseClass->setSignatureService($this->getSignatureService());
        $responseClass->setEncryptionService($this->getEncryptionService());

        return $responseClass->decode($SAMLData);
    }
}
