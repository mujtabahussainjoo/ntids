<?php

namespace Infortis\Base\Model\Import;

use Infortis\Base\Helper\Data as HelperData;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Simplexml\Config;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
class Cms extends AbstractModel
{
    const ITEM_TITLE_PREFIX = 'Ultimo ';
    
    /**
     * @var ManagerInterface
     */
    protected $_messageManagerInterface;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var LoggerInterface
     */
    protected $_logLoggerInterface;

    /**
     * @var \Magento\Framework\Module\Dir
     */
    protected $moduleDirHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Path to directory with import files
     *
     * @var string
     */
    protected $_basePath;
    
    protected $_pageFactory;
    protected $_blockFactory;
    
    /**
     * Create path
     */
    public function __construct(
        Context $context, 
        Registry $registry, 
        ManagerInterface $messageManagerInterface,
        HelperData $helperData,
        \Magento\Framework\Module\Dir $moduleDirHelper,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        StoreManagerInterface $storeManager,
        AbstractResource $resource=null, 
        AbstractDb $resourceCollection=null,  
        array $data = []
    ) {
        $this->_messageManagerInterface = $messageManagerInterface;
        $this->_helperData = $helperData;
        $this->_logLoggerInterface = $context->getLogger();
        $this->moduleDirHelper = $moduleDirHelper;
        $this->_pageFactory = $pageFactory;
        $this->_blockFactory = $blockFactory;
        $this->_storeManager = $storeManager;
        
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    
    /**
    * Using dynamic strings as model aliases doesn't work in Magento 2
    * Since Magento 2 needs/wants to generate all classes before deploying
    * to production. This method allows us to pick which factory our code
    * will use based on the old model string. Since we inject both objects,
    * this should be safe.
    */  
    protected function getFactoryFromModel($modelString)
    {
        switch($modelString)
        {
            case 'cms/block':
                return $this->_blockFactory;
            case 'cms/page':
                return $this->_pageFactory;             
            default:
                throw new \Exception("I don't have a $modelString factory");
        }
        
        throw new \Exception("I don't have a $modelString factory");       
    }

    /**
     * Import CMS items
     *
     * @param string model string
     * @param string name of the main XML node (and name of the XML file)
     * @param int demo number
     * @param bool overwrite existing items
     * @param string package name
     */
    public function importCmsItems($modelString, $entityName, $demoNumber, $overwrite = false, $package = 'Infortis_Base')
    {
        $this->_basePath = $this->moduleDirHelper->getDir($package) . '/etc/importexport/cms/';

        // XML node name for collection of items (e.g. for static blocks $entityName is "block")
        $containerNodeString = $entityName . 's';

        // Determine name and path of the import file
        $xmlFileName = 'demo' . $demoNumber . '.xml';
        $importPath = $this->_basePath . $containerNodeString . '/';
        $xmlFilePath = $importPath . $xmlFileName;

        try
        {
            if (!is_readable($xmlFilePath))
            {
                throw new \Exception(
                    __("Can't read data file: %1", $xmlFilePath)
                    );
            }
            $xmlObj = new Config($xmlFilePath);

            // Get a list (hashtable) of items which already exist in the database
            $oldItems = $this->getExistingItemsIds($modelString);

            // Create a list of items which were already imported (during this execution)
            $alreadyImportedItems = [];
            
            $conflictingOldItems = [];
            $i = 0;
            foreach ($xmlObj->getNode($containerNodeString)->children() as $b)
            {
                $newId = (string) $b->identifier;

                // Check if items with the same ID already exists in the database
                if (isset($oldItems[$newId]))
                {
                    // Remember this ID
                    $conflictingOldItems[] = $newId;

                    // If old items can be overwritten
                    if ($overwrite)
                    {
                        // Delete the old items with this ID
                        $oldBlocks = $this->getFactoryFromModel($modelString)->create()->getCollection()
                            ->addFieldToFilter('identifier', $b->identifier) //array('eq' => $b->identifier)
                            ->load();
                        foreach ($oldBlocks as $old)
                        {
                            $old->delete();
                        }

                        // Remove the deleted item from the list
                        unset($oldItems[$newId]);
                    }
                    else
                    {
                        // Skip this item and don't import it
                        continue;
                    }
                }

                $newItem = $this->getFactoryFromModel($modelString)->create()
                    ->setIdentifier($b->identifier)
                    ->setTitle($b->title)
                    ->setIsActive($b->is_active)
                    ->setPageLayout($b->page_layout)
                    ->setContent($b->content);

                // Check if items with the same ID was already imported
                if (isset($alreadyImportedItems[$newId]))
                {
                    // If yes, don't assign to any store and deactivate
                    $newItem->setIsActive(false);

                    // Add suffix (version number of the item) to the title
                    $newItem->setTitle($b->title . ' (ver ' . ($alreadyImportedItems[$newId] + 1) . ')');
                }
                else
                {
                    // If not, assign to all stores
                    $newItem->setStores([0]);
                }

                $newItem->save();

                // Mark the item as already imported. Count how many times item was imported.
                if (!isset($alreadyImportedItems[$newId]))
                {
                    $alreadyImportedItems[$newId] = 1;
                }
                else
                {
                    $alreadyImportedItems[$newId]++;
                }

                $i++;
            }
            
            // Final info
            if ($i)
            {
                $this->_messageManagerInterface->addSuccess(
                    __('Number of imported items: <strong>%1</strong>. Items with the following identifiers were imported:<br />%2', $i, implode(', ', array_keys($alreadyImportedItems)))
                );
            }
            else
            {
                $this->_messageManagerInterface->addNotice(
                    __('No items were imported')
                );
            }
            
            if ($overwrite)
            {
                if ($conflictingOldItems)
                    $this->_messageManagerInterface->addSuccess(
                        __('Items (<strong>%1</strong>) with the following identifiers were overwritten:<br />%2', count($conflictingOldItems), implode(', ', $conflictingOldItems))
                    );
            }
            else
            {
                if ($conflictingOldItems)
                    $this->_messageManagerInterface->addNotice(
                        __('Unable to import items (%1) with the following identifiers (they already exist in the database):<br />%2', count($conflictingOldItems), implode(', ', $conflictingOldItems))
                    );
            }
        }
        catch (\Exception $e)
        {
            $this->_messageManagerInterface->addError($e->getMessage());
            $this->_logger->error($e);
        }
    }

    /**
     * Get identifiers of items which already exist in the database
     *
     * @param string model string
     * @return array
     */
    protected function getExistingItemsIds($modelString)
    {
        $list = [];

        $itemsCollection = $this->getFactoryFromModel($modelString)->create()->getCollection()
            ->load();

        foreach ($itemsCollection as $item)
        {
            $id = $item->getIdentifier();
            if (!isset($list[$id]))
            {
                $list[$id] = 1;
            }
            else
            {
                $list[$id]++;
            }
        }

        return $list;
    }

    /**
     * Export
     *
     * @param string
     * @param string
     * @param int
     * @param bool
     * @param string
     */
    public function exportCmsItems($modelString, $entityName, $storeId = null, $withDefaultStore = true, $package = 'Infortis_Base')
    {
    }

}
