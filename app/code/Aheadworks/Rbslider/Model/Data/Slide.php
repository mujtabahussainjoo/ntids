<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Rbslider\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use Aheadworks\Rbslider\Api\Data\SlideInterface;
use Aheadworks\Rbslider\Api\Data\SlideExtensionInterface;

/**
 * Slide data model
 * @codeCoverageIgnore
 */
class Slide extends AbstractExtensibleObject implements SlideInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreIds()
    {
        return $this->_get(self::STORE_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreIds($storeIds)
    {
        return $this->setData(self::STORE_IDS, $storeIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerGroupIds()
    {
        return $this->_get(self::CUSTOMER_GROUP_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerGroupIds($customerGroupIds)
    {
        return $this->setData(self::CUSTOMER_GROUP_IDS, $customerGroupIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayFrom()
    {
        return $this->_get(self::DISPLAY_FROM);
    }

    /**
     * {@inheritdoc}
     */
    public function setDisplayFrom($displayFrom)
    {
        return $this->setData(self::DISPLAY_FROM, $displayFrom);
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayTo()
    {
        return $this->_get(self::DISPLAY_TO);
    }

    /**
     * {@inheritdoc}
     */
    public function setDisplayTo($displayTo)
    {
        return $this->setData(self::DISPLAY_TO, $displayTo);
    }

    /**
     * {@inheritdoc}
     */
    public function getImgType()
    {
        return $this->_get(self::IMG_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setImgType($imgType)
    {
        return $this->setData(self::IMG_TYPE, $imgType);
    }

    /**
     * {@inheritdoc}
     */
    public function getImgFile()
    {
        return $this->_get(self::IMG_FILE);
    }

    /**
     * {@inheritdoc}
     */
    public function setImgFile($imgFile)
    {
        return $this->setData(self::IMG_FILE, $imgFile);
    }

    /**
     * {@inheritdoc}
     */
    public function getImgUrl()
    {
        return $this->_get(self::IMG_URL);
    }

    /**
     * {@inheritdoc}
     */
    public function setImgUrl($imgUrl)
    {
        return $this->setData(self::IMG_URL, $imgUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function getImgTitle()
    {
        return $this->_get(self::IMG_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setImgTitle($imgTitle)
    {
        return $this->setData(self::IMG_TITLE, $imgTitle);
    }

    /**
     * {@inheritdoc}
     */
    public function getImgAlt()
    {
        return $this->_get(self::IMG_ALT);
    }

    /**
     * {@inheritdoc}
     */
    public function setImgAlt($imgAlt)
    {
        return $this->setData(self::IMG_ALT, $imgAlt);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->_get(self::URL);
    }

    /**
     * {@inheritdoc}
     */
    public function setUrl($url)
    {
        return $this->setData(self::URL, $url);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsOpenUrlInNewWindow()
    {
        return $this->_get(self::IS_OPEN_URL_IN_NEW_WINDOW);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsOpenUrlInNewWindow($isOpenUrlInNewWindow)
    {
        return $this->setData(self::IS_OPEN_URL_IN_NEW_WINDOW, $isOpenUrlInNewWindow);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsAddNofollowToUrl()
    {
        return $this->_get(self::IS_ADD_NOFOLLOW_TO_URL);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsAddNofollowToUrl($isAddNofollowToUrl)
    {
        return $this->setData(self::IS_ADD_NOFOLLOW_TO_URL, $isAddNofollowToUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function getBannerIds()
    {
        return $this->_get(self::BANNER_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setBannerIds($bannerIds)
    {
        return $this->setData(self::BANNER_IDS, $bannerIds);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(SlideExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
