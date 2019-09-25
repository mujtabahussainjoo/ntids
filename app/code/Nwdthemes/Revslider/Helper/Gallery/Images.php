<?php

namespace Nwdthemes\Revslider\Helper\Gallery;

use Magento\Framework\App\Filesystem\DirectoryList;

class Images extends \Magento\Cms\Helper\Wysiwyg\Images {

	protected $_currentPath;
	protected $_directory;

	/**
	 *	Constructor
	 */

	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper = null
    ) {
        $escaper = $escaper ?: \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\Escaper::class);
        parent::__construct($context, $backendData, $filesystem, $storeManager, $escaper);

        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_directory->create(\Nwdthemes\Revslider\Helper\Images::IMAGE_DIR);
    }

    public function getCurrentPath() {
        if (!$this->_currentPath) {
            $currentPath = $this->_directory->getAbsolutePath() . \Nwdthemes\Revslider\Helper\Images::IMAGE_DIR;
            $path = $this->_getRequest()->getParam($this->getTreeNodeName());
            if ($path) {
                $path = $this->convertIdToPath($path);
                if ($this->_directory->isDirectory($this->_directory->getRelativePath($path))) {
                    $currentPath = $path;
                }
            }
            try {
                $currentDir = $this->_directory->getRelativePath($currentPath);
                if (!$this->_directory->isExist($currentDir)) {
                    $this->_directory->create($currentDir);
                }
            } catch (\Magento\Framework\Exception\FileSystemException $e) {
                $message = __('The directory %1 is not writable by server.', $currentPath);
                throw new \Magento\Framework\Exception\LocalizedException($message);
            }
            $this->_currentPath = $currentPath;
        }
        return $this->_currentPath;
    }

    public function getStorageRoot() {
        return $this->_directory->getAbsolutePath(\Nwdthemes\Revslider\Helper\Images::IMAGE_DIR);
    }

    public function isUsingStaticUrlsAllowed() {
		return true;
    }

}
