<?php

namespace Nwdthemes\Revslider\Helper;

use \Nwdthemes\Revslider\Model\Revslider\Framework\PclZip;

class Filesystem extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $_context;

	/**
	 *	Constructor
	 */

	public function __construct(
        \Magento\Framework\App\Helper\Context $context)
    {
        $this->_context = $context;

        parent::__construct($this->_context);
	}

    /**
	 *	Init filesystem class
	 */

	public function WP_Filesystem() {
		return $this;
	}

	/**
	 *	Unzip file
	 *
	 *	@param	string	Zip file
	 *	@param	string	Destination path
	 *	@return boolean
	 */

	public function unzip_file($file, $path) {

        // make sure it have trailing slash
        $path = rtrim($path, '/\\') . DIRECTORY_SEPARATOR;

		if ( ! $this->wp_mkdir_p($path)) return false;

		if (class_exists('\ZipArchive', false)) {
			$zip = new \ZipArchive;
			$zipResult = $zip->open($file, \ZipArchive::CREATE);
			if ($zipResult === true) {
				for($i = 0; $i < $zip->numFiles; $i++) {
					$fileName = $zip->getNameIndex($i);
					$fileInfo = pathinfo($fileName);
					if (strpos($fileName, '_') !== 0 && strpos($fileName, '.') !== 0 && strpos($fileInfo['basename'], '_') !== 0 && strpos($fileInfo['basename'], '.') !== 0) {
						if ($fileInfo['dirname'] !== '.' && ! file_exists($path.$fileInfo['dirname'])) {
							$parts = explode('/', $fileInfo['dirname']);
							$dirPath = $path;
							foreach ($parts as $part) {
								$dirPath .= $part . DIRECTORY_SEPARATOR;
								$this->wp_mkdir_p($dirPath);
							}
						}
						if (substr($fileName, -1) !== '/' && substr($fileName, -1) !== '\\') {
							copy("zip://".$file."#".$fileName, $path.str_replace('//', DIRECTORY_SEPARATOR, $fileName));
						}
					}
				}
				$zip->close();
			}
		} else {
			$pclZip = new PclZip($file);
			$list = $pclZip->listContent();
			if ($list) {
				for ($i=0; $i<sizeof($list); $i++) {
					$fileInfo = $list[$i];
					$fileName = $fileInfo['filename'];
					if (strpos($fileName, '_') !== 0 && strpos($fileName, '.') !== 0 && strpos($fileName, '/_') === FALSE && strpos($fileName, '/.') === FALSE) {
						if ($fileInfo['folder']) {
                            if ( ! file_exists($path.$fileName)) {
                                $this->wp_mkdir_p($path.$fileName);
                            }
						} elseif ( ! file_exists($path . dirname($fileName))) {
							$parts = explode('/', dirname($fileName));
							$dirPath = $path;
							foreach ($parts as $part) {
								$dirPath .= $part . DIRECTORY_SEPARATOR;
								$this->wp_mkdir_p($dirPath);
							}
                        }
						copy("zip://".$file."#".$fileName, $path.$fileName);
					}
				}
			}
			$zipResult = count($list) !== 0;
		}
		return $zipResult;
	}

	public function recurse_move($src, $dst) {
		$src = rtrim($src,'/\\');
		$dst = rtrim($dst,'/\\');
		$dir = opendir($src);
		$this->wp_mkdir_p($dst);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					$this->recurse_move($src . '/' . $file,$dst . '/' . $file);
				} else {
					$this->rename($src . '/' . $file,$dst . '/' . $file);
				}
			}
		}
		closedir($dir);
		rmdir($src);
	}

	/**
	 *	Make writable directory
	 *
	 *	@param	string	$path
	 *	@return	bool
	 */

	public function wp_mkdir_p($dir) {
		if (file_exists($dir) && is_dir($dir)) {
			return true;
		} else {
			return @mkdir($dir);
		}
	}

	/**
	 *	Check if file exists
	 *
	 *	@param	string	Path to file
	 *	@return boolean
	 */

	public function exists($path) {
		return file_exists($path);
	}

	/**
	 *	Read file
	 *
	 *	@param	string	Path to file
	 *	@return string
	 */

	public function get_contents($path) {
		return file_get_contents($path);
	}

	/**
	 *	Delete file
	 *
	 *	@param	string	Path to file
	 *	@param	boolean	Is recursive
	 *	@return string
	 */

	public function delete($path, $recursive = false) {
		if (is_dir($path)) {
			$dir = opendir($path);
			while(false !== ( $file = readdir($dir)) ) {
				if (( $file != '.' ) && ( $file != '..' )) {
					if ( is_dir($path . '/' . $file) ) {
						if ($recursive) {
							$this->delete($path . '/' . $file, $recursive);
						}
					} else {
						unlink($path . '/' . $file);
					}
				}
			}
			closedir($dir);
			rmdir($path);
		} else {
			unlink($path);
		}
	}
	
}