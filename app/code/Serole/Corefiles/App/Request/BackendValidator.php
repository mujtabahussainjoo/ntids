<?php

   namespace Serole\Corefiles\App\Request;

   use Magento\Backend\Model\Auth;
   use Magento\Backend\Model\UrlInterface as BackendUrl;
   use Magento\Framework\Controller\Result\RawFactory;
   use Magento\Framework\Controller\Result\RedirectFactory;
   use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
   use Magento\Backend\App\AbstractAction;
   use Magento\Framework\App\ActionInterface;
   use Magento\Framework\App\CsrfAwareActionInterface;
   use Magento\Framework\App\Request\InvalidRequestException;
   use Magento\Framework\App\Request\ValidatorInterface;
   use Magento\Framework\App\RequestInterface;
   use Magento\Framework\App\Request\Http as HttpRequest;
   use Magento\Framework\Controller\Result\Raw as RawResult;
   use Magento\Framework\Phrase;


   class BackendValidator extends \Magento\Backend\App\Request\BackendValidator{

       private $auth;

       private $formKeyValidator;

       private $backendUrl;

       private $redirectFactory;

       private $rawResultFactory;

        public function __construct(Auth $auth,
                                    FormKeyValidator $formKeyValidator,
                                    BackendUrl $backendUrl,
                                    RedirectFactory $redirectFactory,
                                    RawFactory $rawResultFactory){
            parent::__construct($auth, $formKeyValidator, $backendUrl, $redirectFactory, $rawResultFactory);
            $this->auth = $auth;
            $this->formKeyValidator = $formKeyValidator;
            $this->backendUrl = $backendUrl;
            $this->redirectFactory = $redirectFactory;
            $this->rawResultFactory = $rawResultFactory;
        }

       public function validate(
           RequestInterface $request,
           ActionInterface $action
       ): void {
           if ($action instanceof AbstractAction) {
               //Abstract Action has build-in validation.
               if (!$action->_processUrlKeys()) {
                   throw new InvalidRequestException($action->getResponse());
               }
           } else {
               //Fallback validation.
               $className = get_class($action);
               if (!$this->validateRequest($request, $action) && $className != 'PluginCompany\CouponImport\Controller\Adminhtml\Import\File\Interceptor') {
                   throw $this->createException($request, $action);
               }
           }
       }

       private function validateRequest(
           RequestInterface $request,
           ActionInterface $action
       ): bool {
           /** @var bool|null $valid */
           $valid = null;

           if ($action instanceof CsrfAwareActionInterface) {
               $valid = $action->validateForCsrf($request);
           }

           if ($valid === null) {
               $validFormKey = true;
               $validSecretKey = true;
               if ($request instanceof HttpRequest && $request->isPost()) {
                   $validFormKey = $this->formKeyValidator->validate($request);
               } elseif ($this->auth->isLoggedIn()
                   && $this->backendUrl->useSecretKey()
               ) {
                   $secretKeyValue = (string)$request->getParam(
                       BackendUrl::SECRET_KEY_PARAM_NAME,
                       null
                   );
                   $secretKey = $this->backendUrl->getSecretKey();
                   $validSecretKey = ($secretKeyValue === $secretKey);
               }
               $valid = $validFormKey && $validSecretKey;
           }

           return $valid;
       }

       private function createException(
           RequestInterface $request,
           ActionInterface $action
       ): InvalidRequestException {
           /** @var InvalidRequestException|null $exception */
           $exception = null;

           if ($action instanceof CsrfAwareActionInterface) {
               $exception = $action->createCsrfValidationException($request);
           }

           if ($exception === null) {
               if ($request instanceof HttpRequest && $request->isAjax()) {
                   //Sending empty response for AJAX request since we don't know
                   //the expected response format and it's pointless to redirect.
                   /** @var RawResult $response */
                   $response = $this->rawResultFactory->create();
                   $response->setHttpResponseCode(401);
                   $response->setContents('');
                   $exception = new InvalidRequestException($response);
               } else {
                   //For regular requests.
                   $response = $this->redirectFactory->create()
                       ->setUrl($this->backendUrl->getStartupPageUrl());
                   $exception = new InvalidRequestException(
                       $response,
                       [
                           new Phrase(
                               'Invalid security or form key. Please refresh the page.'
                           )
                       ]
                   );
               }
           }

           return $exception;
       }


   }