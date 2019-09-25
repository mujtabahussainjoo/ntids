<?php

namespace Wizkunde\WebSSO\Connection\SAML2\Service;

class Binding
{
    private $signature;
    private $uniqueId;

    private $redirectBinding;
    private $postBinding;
    private $artifactBinding;
    private $idpMetadata;
    private $resolveService;
    
    private $timestamp;
    private $httpClient;
    private $settings;
    private $appState;
    private $twig;
    private $storeManager;
    private $localeResolver;
    private $domDocument;
    private $backendHelper;

    private $serverData;
    private $serverHelper;

    /**
     * SAML2 constructor.
     * @param \Wizkunde\WebSSO\Helper\Server $serverHelper
     * @param Signature $signature
     * @param \Wizkunde\SAMLBase\Metadata\IDPMetadata $idpMetadata
     * @param \Wizkunde\SAMLBase\Binding\Redirect $redirectBinding
     * @param \Wizkunde\SAMLBase\Binding\Post $postBinding
     * @param \Wizkunde\SAMLBase\Binding\Artifact $artifactBinding
     * @param \Wizkunde\SAMLBase\Metadata\ResolveService $resolveService
     * @param \Wizkunde\SAMLBase\Configuration\UniqueID $uniqueId
     * @param \Wizkunde\SAMLBase\Configuration\Timestamp $timestamp
     * @param \Wizkunde\SAMLBase\Configuration\Settings $settings
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\Resolver $localeResolver
     * @param \Magento\Backend\Helper\Data $backendHelper
     */
    public function __construct(
        \Wizkunde\WebSSO\Helper\Server $serverHelper,
        \Wizkunde\WebSSO\Connection\SAML2\Service\Signature $signature,
        \Wizkunde\SAMLBase\Metadata\IDPMetadata $idpMetadata,
        \Wizkunde\SAMLBase\Binding\Redirect $redirectBinding,
        \Wizkunde\SAMLBase\Binding\Post $postBinding,
        \Wizkunde\SAMLBase\Binding\Artifact $artifactBinding,
        \Wizkunde\SAMLBase\Metadata\ResolveService $resolveService,
        \Wizkunde\SAMLBase\Configuration\UniqueID $uniqueId,
        \Wizkunde\SAMLBase\Configuration\Timestamp $timestamp,
        \Wizkunde\SAMLBase\Configuration\Settings $settings,
        \Magento\Framework\App\State $appState,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\Resolver $localeResolver,
        \Magento\Backend\Helper\Data $backendHelper
    ) {
    
        $this->signature = $signature;
        $this->uniqueId = $uniqueId;

        $this->redirectBinding = $redirectBinding;
        $this->postBinding = $postBinding;
        $this->artifactBinding = $artifactBinding;

        $this->idpMetadata = $idpMetadata;
        $this->resolveService = $resolveService;

        $this->timestamp = $timestamp;
        $this->httpClient = new \GuzzleHttp\Client;
        $this->settings = $settings;
        $this->appState = $appState;
        $this->storeManager = $storeManager;
        $this->localeResolver = $localeResolver;
        $this->domDocument = new \DomDocument;

        $this->backendHelper = $backendHelper;

        $this->serverHelper = $serverHelper;

        if (count($serverHelper->getServerInfo()) > 0) {
            $this->serverData = $serverHelper->getServerInfo()['type_saml2'];
            $twigFilesystem = new \Twig_Loader_Filesystem;
            $twigFilesystem->addPath(BP . '/vendor/wizkunde/samlbase/src/Wizkunde/SAMLBase/Template/Twig');
            $twigEnvironment = new \Twig_Environment($twigFilesystem);
            $this->twig = $twigEnvironment;
        }
    }

    public function getSsoBinding()
    {
        return $this->setupBinding($this->serverData['sso_binding']);
    }

    public function getSloBinding()
    {
        return $this->setupBinding($this->serverData['slo_binding']);
    }

    public function getArtifactBinding()
    {
        return $this->setupBinding('artifact');
    }

    public function getMetadataXml($backend = false)
    {
        $certString = str_replace(
            [
                '-----BEGIN CERTIFICATE-----',
                '-----END CERTIFICATE-----'
            ],
            '',
            $this->serverData['crt_data']
        );

        if($backend) {
            $baseUrl = $this->backendHelper->getHomePageUrl();
        } else {
            $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        }

        $requestTemplate = $this->twig->render(
            'Metadata.xml.twig',
            [
                'BaseURL'                   => $baseUrl,
                'ACSURL'                   => $baseUrl. 'sso/account/login',
                'SLOURL'                   => $baseUrl . 'sso/account/logout',
                'EntityID'                  => $this->serverData['name_id'],
                'ServiceProviderPublicKey'  => $certString,
                'OrganizationName'          => '',
                'OrganizationDisplayName'   => '',
                'OrganizationURL'           => $this->storeManager->getStore()->getBaseUrl(),
                'ContactPersonSurName'      => 'SAML User',
                'ContactPersonEmailAddress' => 'SAML Email'
            ]
        );

        $this->domDocument->loadXML($requestTemplate);

        $this->signature->getSigningService()->signMetadata($this->domDocument);

        return $this->domDocument->saveXml();
    }

    /**
     * Setup the binding to handle a dataset
     *
     * @param $bindingType
     */
    private function setupBinding($bindingType)
    {
        if ($bindingType == 'post') {
            $binding = $this->postBinding;
        } elseif ($bindingType == 'artifact') {
            $binding = $this->artifactBinding;
        } else {
            $binding = $this->redirectBinding;
        }

        if ($this->serverData['metadata_url'] != '') {
            $binding->setMetadata($this->resolveService->resolve($this->idpMetadata, $this->serverData['metadata_url']));
            $binding->setTwigService($this->twig);
            $binding->setUniqueIdService($this->uniqueId);
            $binding->setTimestampService($this->timestamp);
            $binding->setSignatureService($this->signature->getSigningService());
            $binding->setHttpService($this->httpClient);
            $binding->setSettings($this->getIdpSettings());
        }


        return $binding;
    }

    /**
     * PRepare all the IDP settings
     *
     * @return mixed
     */
    private function getIdpSettings()
    {

        $returnUrl = ($this->appState->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML) ? '/admin/' : '/';

        $this->settings->setValues([
                'NameID' => $this->serverData['name_id'],
                'Issuer' => $this->serverData['name_id'],
                'MetadataExpirationTime' => $this->serverData['metadata_expiration'],
                'SPReturnUrl' => $this->storeManager->getStore()->getBaseUrl(). 'sso/account/login',
                'ForceAuthn' => (int)$this->serverData['forceauthn'],
                'IsPassive' => (int)$this->serverData['is_passive'],
                'NameIDFormat' => $this->serverData['name_id_format'],
                'ComparisonLevel' => 'exact',
                'OptionalURLParameters'   =>
                    [
                        'idp' => $this->serverHelper->getServerInfo()['identifier'],
                        'language' => substr($this->localeResolver->getLocale(), 0, 2)
                    ]
            ]);

        return $this->settings;
    }
}
