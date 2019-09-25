<?php
namespace Ibnab\OwlSlider\Model\ResourceModel;

class Sliders extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('ibnab_owlslider_sliders', 'slider_id');
    }
    /**
     * Assign slider to banners
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->saveBanners($object);
        $this->saveProducts($object);
        return parent::_afterSave($object);
    }
    protected function saveBanners(\Magento\Framework\Model\AbstractModel $object){
        $oldBanners = $this->lookupBannersIds($object->getId());
        $newBanners = (array)$object->getBannersId();
        if($newBanners != null)
        {
        $table = $this->getTable('ibnab_owlslider_sliders_banners');
        $insert = array_diff($newBanners, $oldBanners);
        $delete = array_diff($oldBanners, $newBanners);
        
        if ($delete) {
            $where = ['slider_id = ?' => (int)$object->getId(), 'banner_id IN (?)' => $delete];
            
            $this->getConnection()->delete($table, $where);
        }
        if(isset($insert[0]) && $insert[0] != 0)
        {
        if ($insert) {
            $data = [];

            foreach ($insert as $bannerId) {
                $data[] = ['slider_id' => (int)$object->getId(), 'banner_id' => (int)$bannerId];
            }

            $this->getConnection()->insertMultiple($table, $data);
        }
        }
        }       
    }
   protected function saveProducts(\Magento\Framework\Model\AbstractModel $object){
        $oldProducts = $this->lookupProductsIds($object->getId());
        $newProducts = (array)$object->getProductsId();
        if($newProducts != null)
        {
        $table = $this->getTable('ibnab_owlslider_sliders_products');
        $insert = array_diff($newProducts, $oldProducts);
        $delete = array_diff($oldProducts, $newProducts);
        
        if ($delete) {
            $where = ['slider_id = ?' => (int)$object->getId(), 'product_id IN (?)' => $delete];
            
            $this->getConnection()->delete($table, $where);
        }
        if(isset($insert[0]) && $insert[0] != 0)
        {
        if ($insert) {
            $data = [];

            foreach ($insert as $productId) {
                $data[] = ['slider_id' => (int)$object->getId(), 'product_id' => (int)$productId];
            }

            $this->getConnection()->insertMultiple($table, $data);
        }
        }
        }       
    }
    /**
     * Get banner ids to which specified item is assigned
     *
     * @param int $pageId
     * @return array
     */
    public function lookupProductsIds($sliderId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('ibnab_owlslider_sliders_products'),
            'product_id'
        )->where(
            'slider_id = ?',
            (int)$sliderId
        );

        return $connection->fetchCol($select);
    }
    /**
     * Get banner ids to which specified item is assigned
     *
     * @param int $pageId
     * @return array
     */
    public function lookupBannersIds($sliderId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('ibnab_owlslider_sliders_banners'),
            'banner_id'
        )->where(
            'slider_id = ?',
            (int)$sliderId
        );

        return $connection->fetchCol($select);
    }
    /**
     * Process slider data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['slider_id = ?' => (int)$object->getId()];

        $this->getConnection()->delete($this->getTable('ibnab_owlslider_sliders_banners'), $condition);

        return parent::_beforeDelete($object);
    }
}
