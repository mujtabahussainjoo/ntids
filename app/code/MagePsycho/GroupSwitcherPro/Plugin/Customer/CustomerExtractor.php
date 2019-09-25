<?php

namespace MagePsycho\GroupSwitcherPro\Plugin\Customer;
use Magento\Framework\App\RequestInterface;

/**
 * @category   MagePsycho
 * @package    MagePsycho_GroupSwitcherPro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CustomerExtractor
{
    protected $request;

    /**
     * @var \MagePsycho\GroupSwitcherPro\Helper\Data
     */
    private $groupSwitcherProHelper;

    public function __construct(
        RequestInterface $request,
        \MagePsycho\GroupSwitcherPro\Helper\Data $groupSwitcherProHelper
    ) {
        $this->request                = $request;
        $this->groupSwitcherProHelper = $groupSwitcherProHelper;
    }

    public function afterExtract(
        \Magento\Customer\Model\CustomerExtractor $subject,
        $result
    ) {
        if ($this->groupSwitcherProHelper->isFxnSkipped()) {
            return $result;
        }

        if ($groupId = $this->request->getParam('group_id')) {
            $result->setGroupId($groupId);
        }

        return $result;
    }
}