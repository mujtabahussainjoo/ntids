<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\GeoIP\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Symfony\Component\Config\Definition\Exception\Exception;
use GeoIp2\Database\Reader as GeoIP2Reader;


/**
 * Geoip model.
 *
 * Work with customer location
 */
class Geoip extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Module registry
     *
     * @var \Magento\Framework\Component\ComponentRegistrar
     */
    protected $componentRegistrar;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    protected $directory;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $driver;

    /**
     * @var \Magento\Framework\Archive\Gz
     */
    protected $gz;

    /**
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    protected $zendClientFactory;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Country\Collection
     */
    protected $countryCollection;

    /**
     * @var \MageWorx\GeoIP\Helper\Info
     */
    protected $helperInfo;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Component\ComponentRegistrarInterface $componentRegistrar
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Driver\File $driver
     * @param \Magento\Framework\Archive\Gz $gz
     * @param \Magento\Framework\HTTP\ZendClientFactory $clientFactory
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Component\ComponentRegistrarInterface $componentRegistrar,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Driver\File $driver,
        \Magento\Framework\Archive\Gz $gz,
        \Magento\Framework\HTTP\ZendClientFactory $clientFactory,
        \Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection,
        \MageWorx\GeoIP\Helper\Info $helperInfo
    ) {
        $this->customerSession = $customerSession;
        $this->objectManager = $objectManager;
        $this->componentRegistrar = $componentRegistrar;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->driver = $driver;
        $this->gz = $gz;
        $this->zendClientFactory = $clientFactory;
        $this->countryCollection = $countryCollection;
        $this->helperInfo = $helperInfo;
    }

    /**
     * Get customer location data by IP
     *
     * @param string $ip
     * @return array
     */
    protected function getGeoIpLocation($ip)
    {
        /** @var \MageWorx\GeoIP\Helper\Database $helperDatabase */
        $helperDatabase = $this->objectManager->get('MageWorx\GeoIP\Helper\Database');
        $dbPath = $helperDatabase->getDatabasePath();
        $isCityDbType = $helperDatabase->isCityDbType();

        if (!$this->driver->isExists($dbPath)) {
            return array();
        }

        $data = ['ip' => $ip];
        $reader = new GeoIP2Reader($dbPath, ['en']);

        try {
            if ($isCityDbType) {
                $record = $reader->city($ip);
                if ($record) {
                    $data['code'] = $record->country->isoCode;
                    $data['country'] = $record->country->name;
                    $data['region'] = $record->mostSpecificSubdivision->name;
                    $data['region_code'] = $record->mostSpecificSubdivision->isoCode;
                    $data['city'] = $record->city->name;
                    $data['postal_code'] = $record->postal->code;
                }
            } else {
                $record = $reader->country($ip);
                $data['code'] = $record->country->isoCode;
                $data['country'] = $record->country->name;
            }
        } catch(\Exception $e) {
            $data['code']    = null;
            $data['country'] = null;
        }

        return $data;
    }

    /**
     * Loads location data by ip and puts it in object
     *
     * @param string $ip
     * @return \MageWorx\GeoIP\Model\Geoip
     */
    public function getLocation($ip)
    {
        $helperCountry = $this->objectManager->get('MageWorx\GeoIP\Helper\Country');
        $data = $this->getGeoIpLocation($ip);

        if (isset($data['code'])) {
            $data['flag'] = $helperCountry->getFlagPath($data['code']);
        }

        $obj = new \Magento\Framework\DataObject($data);

        return $obj;
    }

    /**
     * Return current customer loaction
     *
     * @return mixed
     */
    public function getCurrentLocation()
    {
        $session = $this->customerSession;

        if (!$session->getCustomerLocation() || !$session->getCustomerLocation()->getCode()) {
            $ip = $this->objectManager->get('MageWorx\GeoIP\Helper\Customer')->getCustomerIp();
            $data = $this->getLocation($ip);
            $session->setCustomerLocation($data);
        }

        return $session->getCustomerLocation();
    }

    /**
     * Set current customer location
     *
     * @param string $countryCode
     * @return boolean
     */
    public function changeCurrentLocation($countryCode)
    {
        $session = $this->customerSession;
        if ($location = $session->getCustomerLocation()) {
            $location->setCode($countryCode);
            $session->setCustomerLocation($location);
        }

        return true;
    }

    /**
     * Return all available countries and regions
     *
     * @return array
     */
    public function getAvailableCountriesAndRegions()
    {
        $maxmindData = $this->helperInfo->getMaxmindData();
        $countries = $this->countryCollection->loadByStore();
        $availableCountries = [];

        foreach ($countries as $country) {
            if (!isset($maxmindData[$country->getId()])) {
                continue;
            }
            $availableCountries[$country->getId()] = $maxmindData[$country->getId()];
        }

        return $availableCountries;
    }

    /**
     * Downloads file from remote server
     *
     * @param string $source
     * @param string $destination
     * @return Geoip
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Http_Client_Exception
     */
    public function downloadFile($source, $destination, $createBackupFlag = null)
    {
        $dir = \MageWorx\GeoIP\Helper\Database::DB_PATH;
        if (!$this->directory->isExist($dir)) {
            $this->directory->create($dir);
        }

        $newFile = $this->directory->getDriver()->fileOpen($destination, "wb");

        if (!$newFile) {
            throw new \Magento\Framework\Exception\LocalizedException(__("Can't create new file. Check that folder %s has write permissions", $dir));
        }

        /** @var \Magento\Framework\HTTP\ZendClient $client */
        $client = $this->zendClientFactory->create();
        $client->setUri($source);
        $client->setConfig(['maxredirects' => 0, 'timeout' => 120]);

        try {
            $result = $client->request(\Zend_Http_Client::GET)->getBody();
            $this->directory->getDriver()->fileWrite($newFile, $result);
        } catch (Exception $e) {
            throw $e;
        }

        if (!$this->directory->getDriver()->isExists($destination)) {
            throw new \Magento\Framework\Exception\LocalizedException(__('DataBase source is temporary unavailable'));
        }

        $this->directory->getDriver()->fileClose($newFile);

        /** @var \MageWorx\GeoIP\Helper\Database $helperDatabase */
        $helperDatabase =  $this->objectManager->get('MageWorx\GeoIP\Helper\Database');
        if ($createBackupFlag) {
            $backupDestination = $destination . \MageWorx\GeoIP\Helper\Database::ARCHIVE_SUFFIX . time();
            $this->directory->getDriver()->copy($destination, $backupDestination);
        }

        $this->unCompressFile($helperDatabase->getTempUpdateFile(), $helperDatabase->getDatabasePath());
        $this->directory->getDriver()->deleteFile($helperDatabase->getTempUpdateFile());

        return $this;
    }

    /**
     * Unpack .gz archive
     *
     * @param string $source
     * @param string $destination
     * @return bool
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function unCompressFile($source, $destination)
    {
        try {
            $this->gz->unpack($source, $destination);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            throw $e;
        }

        return $this;
    }
}
