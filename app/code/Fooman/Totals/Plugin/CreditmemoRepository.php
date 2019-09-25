<?php
/**
 * @author     Kristof Ringleff
 * @package    Fooman_Totals
 * @copyright  Copyright (c) 2016 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fooman\Totals\Plugin;

class CreditmemoRepository
{

    /**
     * @var \Fooman\Totals\Model\CreditmemoTotalManagement
     */
    private $creditmemoTotalManagement;

    /**
     * @var \Magento\Sales\Api\Data\CreditmemoExtensionFactory
     */
    private $creditmemoExtensionFactory;

    /**
     * @var \Fooman\Totals\Model\GroupFactory
     */
    private $creditmemoTotalGroupFactory;

    /**
     * @param \Fooman\Totals\Model\CreditmemoTotalManagement     $creditmemoTotalManagement
     * @param \Magento\Sales\Api\Data\CreditmemoExtensionFactory $creditmemoExtensionFactory
     * @param \Fooman\Totals\Model\GroupFactory               $creditmemoTotalGroupFactory
     */
    public function __construct(
        \Fooman\Totals\Model\CreditmemoTotalManagement $creditmemoTotalManagement,
        \Magento\Sales\Api\Data\CreditmemoExtensionFactory $creditmemoExtensionFactory,
        \Fooman\Totals\Model\GroupFactory $creditmemoTotalGroupFactory
    ) {
        $this->creditmemoTotalManagement = $creditmemoTotalManagement;
        $this->creditmemoExtensionFactory = $creditmemoExtensionFactory;
        $this->creditmemoTotalGroupFactory = $creditmemoTotalGroupFactory;
    }

    /**
     * @param  \Magento\Sales\Api\CreditmemoRepositoryInterface $subject
     * @param  \Magento\Sales\Api\Data\CreditmemoInterface      $creditmemo
     *
     * @return \Magento\Sales\Api\Data\CreditmemoInterface      $creditmemo
     */
    public function afterGet(
        \Magento\Sales\Api\CreditmemoRepositoryInterface $subject,
        \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo
    ) {

        $this->applyExtensionAttributes($creditmemo);
        return $creditmemo;
    }

    /**
     * @param  \Magento\Sales\Api\CreditmemoRepositoryInterface $subject
     * @param \Magento\Sales\Api\Data\CreditmemoSearchResultInterface $result
     *
     * @return \Magento\Sales\Api\Data\CreditmemoSearchResultInterface
     */
    public function afterGetList(
        \Magento\Sales\Api\CreditmemoRepositoryInterface $subject,
        \Magento\Sales\Api\Data\CreditmemoSearchResultInterface $result
    ) {

        $creditmemos = $result->getItems();
        if (!empty($creditmemos)) {
            foreach ($creditmemos as $creditmemo) {
                $this->applyExtensionAttributes($creditmemo);
            }
        }

        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo
     *
     * @return void
     */
    private function applyExtensionAttributes(\Magento\Sales\Api\Data\CreditmemoInterface $creditmemo)
    {

        $extensionAttributes = $creditmemo->getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->creditmemoExtensionFactory->create();
        }

        $foomanTotalGroup = $extensionAttributes->getFoomanTotalGroup();
        if (!$foomanTotalGroup) {
            $foomanTotalGroup = $this->creditmemoTotalGroupFactory->create();
        }

        $creditmemoTotals = $this->creditmemoTotalManagement->getByCreditmemoId(
            $creditmemo->getEntityId()
        );

        if (!empty($creditmemoTotals)) {
            foreach ($creditmemoTotals as $creditmemoTotal) {
                $foomanTotalGroup->addItem($creditmemoTotal);
            }
        }

        $extensionAttributes->setFoomanTotalGroup($foomanTotalGroup);
        $creditmemo->setExtensionAttributes($extensionAttributes);
    }
}
