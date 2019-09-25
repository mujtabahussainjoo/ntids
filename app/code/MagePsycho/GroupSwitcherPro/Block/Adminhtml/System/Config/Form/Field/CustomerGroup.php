<?php

namespace MagePsycho\GroupSwitcherPro\Block\Adminhtml\System\Config\Form\Field;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * @category   MagePsycho
 * @package    MagePsycho_GroupSwitcherPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CustomerGroup extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * Customer groups cache
     *
     * @var array
     */
    private $_customerGroups;

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
        \Magento\Framework\View\Element\Context $context,
        GroupManagementInterface $groupManagement,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->groupManagement = $groupManagement;
        $this->groupRepository = $groupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;

        $this->_addGroupAllOption = false;
    }

    /**
     * Retrieve allowed customer groups
     *
     * @param int $groupId return name by customer group id
     * @return array|string
     */
    protected function _getCustomerGroups($groupId = null)
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

    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            if ($this->_addGroupAllOption) {
                $this->addOption(
                    $this->groupManagement->getAllCustomersGroup()->getId(),
                    __('ALL GROUPS')
                );
            }

            foreach ($this->_getCustomerGroups() as $groupId => $groupLabel) {
                $this->addOption($groupId, htmlentities($groupLabel));
            }
        }
        return parent::_toHtml();
    }
}