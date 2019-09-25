<?php
   namespace Serole\Corefiles\Model\Import;


   use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;

   class Customer extends \Magento\CustomerImportExport\Model\Import\Customer{

       protected $accountManagement;

       public function __construct(\Magento\Framework\Stdlib\StringUtils $string,
                                   \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
                                   \Magento\ImportExport\Model\ImportFactory $importFactory,
                                   \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
                                   \Magento\Framework\App\ResourceConnection $resource,
                                   ProcessingErrorAggregatorInterface $errorAggregator,
                                   \Magento\Store\Model\StoreManagerInterface $storeManager,
                                   \Magento\ImportExport\Model\Export\Factory $collectionFactory,
                                   \Magento\Eav\Model\Config $eavConfig,
                                   \Magento\CustomerImportExport\Model\ResourceModel\Import\Customer\StorageFactory $storageFactory,
                                   \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory $attrCollectionFactory,
                                   \Magento\Customer\Model\CustomerFactory $customerFactory,
                                   \Magento\Customer\Model\AccountManagement $accountManagement,
                                   array $data = [])
       {
           parent::__construct($string, $scopeConfig, $importFactory, $resourceHelper, $resource, $errorAggregator, $storeManager, $collectionFactory, $eavConfig, $storageFactory, $attrCollectionFactory, $customerFactory, $data);
           $this->accountManagement = $accountManagement;
       }

       protected function _importData()
       {
           $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/child-customer-importData.log');
           $logger = new \Zend\Log\Logger();
           $logger->addWriter($writer);


           while ($bunch = $this->_dataSourceModel->getNextBunch()) {
               $this->prepareCustomerData($bunch);
               $entitiesToCreate = [];
               $entitiesToUpdate = [];
               $entitiesToDelete = [];
               $attributesToSave = [];

               foreach ($bunch as $rowNumber => $rowData) {
                   $logger->info($rowData);
                   $logger->info("step-1");
                   if (!$this->validateRow($rowData, $rowNumber)) {
                       continue;
                   }
                   if ($this->getErrorAggregator()->hasToBeTerminated()) {
                       $this->getErrorAggregator()->addRowToSkip($rowNumber);
                       continue;
                   }

                   if ($this->getBehavior($rowData) == \Magento\ImportExport\Model\Import::BEHAVIOR_DELETE) {
                       $logger->info("step-2");
                       $entitiesToDelete[] = $this->_getCustomerId(
                           $rowData[self::COLUMN_EMAIL],
                           $rowData[self::COLUMN_WEBSITE]
                       );
                   } elseif ($this->getBehavior($rowData) == \Magento\ImportExport\Model\Import::BEHAVIOR_ADD_UPDATE) {
                       $logger->info("step-3");
                       $processedData = $this->_prepareDataForUpdate($rowData);
                       $entitiesToCreate = array_merge($entitiesToCreate, $processedData[self::ENTITIES_TO_CREATE_KEY]);
                       $logger->info($entitiesToCreate);
                       $entitiesToUpdate = array_merge($entitiesToUpdate, $processedData[self::ENTITIES_TO_UPDATE_KEY]);
                       $logger->info($entitiesToUpdate);
                       foreach ($processedData[self::ATTRIBUTES_TO_SAVE_KEY] as $tableName => $customerAttributes) {
                           if (!isset($attributesToSave[$tableName])) {
                               $attributesToSave[$tableName] = [];
                           }
                           $attributesToSave[$tableName] = array_diff_key(
                                   $attributesToSave[$tableName],
                                   $customerAttributes
                               ) + $customerAttributes;
                       }
                   }
               }
               $logger->info("step-4");
               $this->updateItemsCounterStats($entitiesToCreate, $entitiesToUpdate, $entitiesToDelete);
               /**
                * Save prepared data
                */
               if ($entitiesToCreate || $entitiesToUpdate) {
                   $logger->info("step 5");
                   $logger->info($entitiesToCreate);
                   $logger->info($entitiesToUpdate);
                   $this->_saveCustomerEntities($entitiesToCreate, $entitiesToUpdate);

                   foreach ($entitiesToCreate as $entityCustomer){
                      $customerObj = $this->_customerModel;
                       $logger->info($entityCustomer['website_id']);
                       $customerObj->setWebsiteId($entityCustomer['website_id']);
                       $logger->info("inside the condition");
                       $logger->info($entityCustomer['email']);
                       $customerObjData = $customerObj->loadByEmail($entityCustomer['email']);
                       //$logger->info($customerObjData->getId());
                       if($customerObjData->getId()){
                           $this->accountManagement->initiatePasswordReset($entityCustomer['email'],
                                                                   \Magento\Customer\Model\AccountManagement::EMAIL_RESET,
                                                                      $entityCustomer['website_id']);
                       }
                   }

               }
               if ($attributesToSave) {
                   $logger->info("step-6");
                   $logger->info($attributesToSave);
                   $this->_saveCustomerAttributes($attributesToSave);
               }
               if ($entitiesToDelete) {
                   $logger->info("step-7");
                   $this->_deleteCustomerEntities($entitiesToDelete);
               }
           }

           return true;
       }
   }