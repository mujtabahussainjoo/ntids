<?php

namespace MagePsycho\StoreRestrictionPro\Model\System\Config\Source;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * @category   MagePsycho
 * @package    MagePsycho_StoreRestrictionPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CustomerGroup implements  \Magento\Framework\Option\ArrayInterface
{
    protected $_options;

    protected $_customerGroups;

    /**
     * Flag whether to add group all option or no
     *
     * @var bool
     */
    protected $_addGroupAllOption = true;

    /**
     *  Group Management Interface
     *
     * @var GroupManagementInterface
     */
    protected $groupManagement;

    /**
     * Call repository
     *
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     *  Seach Builder
     *
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    
    public function __construct(
        GroupManagementInterface $groupManagement,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->groupManagement       = $groupManagement;
        $this->groupRepository       = $groupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_addGroupAllOption    = false;
    }
    
    public function getAllOptions($withEmpty = false)
    {
         
        if (is_null($this->_options)) {
            foreach ($this->getCustomerGroups() as $groupId => $groupLabel) {
                $this->_options[] = [
                    'label' => htmlentities($groupLabel),
                    'value' => $groupId
                ];
            }
        }
        $options = $this->_options;
        if ($withEmpty) {
            array_unshift($options, ['value' => '', 'label' => '']);
        }
        return $options;
    }

    /**
     * Retrieve allowed customer groups
     *
     * @param int $groupId return name by customer group id
     * @return array|string
     */
    public function getCustomerGroups($groupId = null)
    {
        if ($this->_customerGroups === null) {
            $this->_customerGroups = [];
            foreach ($this->groupRepository->getList($this->searchCriteriaBuilder->create())->getItems() as $item) {
                if ($item->getId() == 0) {
                    continue;
                }
                $this->_customerGroups[$item->getId()] = $item->getCode();
            }
            /*$notLoggedInGroup = $this->groupManagement->getNotLoggedInGroup();
            $this->_customerGroups[$notLoggedInGroup->getId()] = $notLoggedInGroup->getCode();*/
        }
        if ($groupId !== null) {
            return isset($this->_customerGroups[$groupId]) ? $this->_customerGroups[$groupId] : null;
        }
        return $this->_customerGroups;
    }

    public function getOptionsArray($withEmpty = true)
    {
        $options = array();
        foreach ($this->getAllOptions($withEmpty) as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }

    public function getOptionText($value)
    {
        $options = $this->getAllOptions(false);
        foreach ($options as $item) {
            if ($item['value'] == $value) {
                return $item['label'];
            }
        }
        return false;
    }

    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

    public function toOptionHash($withEmpty = true)
    {
        return $this->getOptionsArray($withEmpty);
    }
}