<?php
/**
 * Created by PhpStorm.
 * User: Linh
 * Date: 5/31/2016
 * Time: 12:15 PM
 */
namespace PL\Competition\Helper;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Filesystem\DirectoryList;

class Resize extends AbstractHelper{

    protected $_filesystem;
    protected $_storeManager;
    protected $_directory;
    protected $_imageFactory;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory

    ) {
        $this->_filesystem = $filesystem;
        $this->_storeManager = $storeManager;
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_imageFactory = $imageFactory;

    }


    public function imageResize($src,$width=200,$height=200,$dir='pl/competition/images/'){
        $absPath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath().$src;

        $imageResized = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($dir).'cache/'.$width.'x'.$height.'/'.$this->getNewDirectoryImage($src);
        $imageResize = $this->_imageFactory->create();

        $imageResize->open($absPath);
        $imageResize->backgroundColor([255, 255, 255]);
        $imageResize->constrainOnly(TRUE);
        $imageResize->keepTransparency(TRUE);
        $imageResize->keepFrame(true);
        $imageResize->keepAspectRatio(true);

        $imageResize->resize($width,$height);
        $dest = $imageResized ;
        $imageResize->save($dest);
        $resizedURL= $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$dir.'cache/'.$width.'x'.$height.'/'.$this->getNewDirectoryImage($src);
        return $resizedURL;

    }

    public function getNewDirectoryImage($src){
        $segments = array_reverse(explode('/',$src));
        $first_dir = substr($segments[0],0,1);
        $second_dir = substr($segments[0],1,1);
        return $first_dir.'/'.$second_dir.'/'.$segments[0];
    }
}