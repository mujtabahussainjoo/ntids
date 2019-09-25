<?php

namespace Nwdthemes\Revslider\Helper;

use \Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\UrlInterface;

class Images extends \Magento\Framework\App\Helper\AbstractHelper {

	const IMAGE_DIR = 'revslider';
	const IMAGE_DIR_EXCLUDES = '/plugins,/templates,/thumbs,/rstemp';
	const IMAGE_THUMB_DIR = 'revslider/thumbs';
	const RS_IMAGE_PATH = 'revslider';

    protected $_directory;
    protected $_imageFactory;
    protected $_storeManager;
    protected $_imageBuilder;
    protected $_catalogImageHelper;
    protected $_catalogProductHelper;
    protected $_productFactory;
    protected $_productMediaConfig;
    protected $_galleryImagesHelper;

    public static $imageSizes = array(
        'gallery' => array('width' => 195, 'height' => 130),
        'thumbnail' => array('width' => 150, 'height' => 150),
        'medium' => array('width' => 300, 'height' => 200),
        'large' => array('width' => 1024, 'height' => 682),
        'post-thumbnail' => array('width' => 825, 'height' => 510)
    );

    /**
	 *	Constructor
	 */

	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
        \Magento\Catalog\Helper\Image $catalogImageHelper,
        \Magento\Catalog\Helper\Product $catalogProductHelper,
		\Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Product\Media\Config $productMediaConfig,
        \Nwdthemes\Revslider\Helper\Gallery\Images $galleryImagesHelper
    ) {
        $this->_imageFactory = $imageFactory;
        $this->_storeManager = $storeManager;
        $this->_imageBuilder = $imageBuilder;
        $this->_catalogImageHelper = $catalogImageHelper;
        $this->_catalogProductHelper = $catalogProductHelper;
        $this->_productFactory = $productFactory;
        $this->_productMediaConfig = $productMediaConfig;
        $this->_galleryImagesHelper = $galleryImagesHelper;

        parent::__construct($context);

        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_directory->create(self::IMAGE_DIR);
    }

	/**
	 * Get images directory
	 *
	 * @return string
	 */
	
	public function getImageDir() {
		return self::IMAGE_DIR;
	}

	/**
	 * Get image thumbs directory
	 *
	 * @return string
	 */
	
	public function getImageThumbDir() {
		return self::IMAGE_THUMB_DIR;
	}

	/**
	 * Resize image
	 *
	 * @param string $fileName
	 * @param int $width
	 * @param int $height
	 * @return string Resized image url
	 */

	public function resizeImg($fileName, $width, $height = '') {

        $fileName = $this->imageClean($fileName);
		if (strpos($fileName, '//') !== false && strpos($fileName, $this->imageBaseUrl()) === false) {
			return $fileName;
		}

		if ( ! $height) {
			$height = $width;
		}

		$thumbDir = self::IMAGE_THUMB_DIR;
		$resizeDir = $thumbDir . "/resized_{$width}x{$height}";

		$this->_directory->create($resizeDir);

		$baseURL = str_replace(array('https://', 'http://'), '//', $this->getBaseUrl());
		$fileName = str_replace(array('https://', 'http://'), '//', $fileName);
		$fileName = str_replace($baseURL, '', $fileName);

		$imageFile = str_replace(array('/', '\\'), '_', str_replace('revslider/', '', $fileName));

		$folderURL = $this->getBaseUrl();
		$imageURL = $folderURL . $fileName;

		$basePath = $this->getBaseDir() . DIRECTORY_SEPARATOR . $fileName;
		$newPath = $this->getBaseDir() . DIRECTORY_SEPARATOR . $resizeDir . DIRECTORY_SEPARATOR . $imageFile;

		if ($width != '') {
			if (file_exists($basePath) && is_file($basePath) && ! file_exists($newPath)) {
				$imageObj = $this->_imageFactory->create($basePath);
				$imageObj->constrainOnly(TRUE);
				$imageObj->keepAspectRatio(TRUE);
				$imageObj->keepFrame(FALSE);
				$imageObj->keepTransparency(TRUE);
				//$imageObj->backgroundColor(array(255,255,255));
				$imageObj->resize($width, $height);
				$res = $imageObj->save($newPath);
			}
			$resizedURL = $this->getBaseUrl() . $resizeDir . '/' . $imageFile;
		} else {
			$resizedURL = $imageURL;
		}
		return $resizedURL;
	}

	/**
	 *	Get image id by url
	 *
	 *	@param	string	$url
	 *	@return	int
	 */
	
	public function attachment_url_to_postid($url) {
		return $this->get_image_id_by_url($url);
	}

	/**
	 *	Get image id by url
	 *
	 *	@param	string	$url
	 *	@return	int
	 */

	public function get_image_id_by_url($url) {
		$id = false;
		$imagePath = $this->imageFile($url);
		if ($imagePath && file_exists($this->imageBaseDir() . $imagePath)) {
			$id = $this->idEncode($imagePath);
		}
		return $id;
	}

	/**
	 *	Get image url by id and size
	 *
	 *	@param	int		Image id
	 *	@param	string	Size type
	 *	@return string
	 */

	public function wp_get_attachment_image_src($attachment_id, $size='thumbnail') {
		return $this->image_downsize($attachment_id, $size);
	}
	
	/**
	 *	Get attached file
	 *
	 *	@param	string
	 *	@return string
	 */

	public function get_attached_file($attachment_id) {
		if ($attachment_id) {
			$image = $this->imageBaseDir() . DIRECTORY_SEPARATOR . $this->imageFile($this->_galleryImagesHelper->idDecode($attachment_id));
			if (file_exists($image)) {
				return $image;
			}
		}
	}
	
	/**
	 *	Resize image by id and preset size
	 *
	 *	@param	int		Image id
	 *	@param	string	Size type
	 *	@return string
	 */

	public function image_downsize($id, $size = 'medium') {

        $downsizedImage = false;

		if ((string)(int)$id === (string)$id && $product = $this->_productFactory->create()->load($id)) {

            switch ($size) {
                case 'thumbnail' :
                    $image = $this->_catalogImageHelper
                        ->init($product, 'product_thumbnail_image')
                        ->setImageFile($product->getFile());
                    $downsizedImage = [
                        $image->getUrl(),
                        $image->getWidth(),
                        $image->getHeight()
                    ];
                    break;
                case 'small' :
                    $imageUrl = $this->_catalogProductHelper->getSmallImageUrl($product);
                    $downsizedImage = [
                        $imageUrl,
                        250,
                        250
                    ];
                    break;
                case 'medium' :
                case 'large' :
                case 'base' :
                case 'full' :
                default :
                    $imageUrl = $this->_catalogProductHelper->getImageUrl($product);
                    $downsizedImage = [
                        $imageUrl,
                        1000,
                        1000
                    ];
                    break;
            }

		} elseif ($id) {

            $image = $this->imageFile($this->get_attached_file($id));

            switch ($size) {
                case 'base' :
                case 'large' :
                case 'full' :

                    if ($imageSize = getimagesize($this->imagePath($image))) {
                        $width = $imageSize[0];
                        $height = $imageSize[1];
                        $imageUrl = $this->imageUrl($image);
                        $downsizedImage = array($imageUrl, $width, $height);
                    }

                    break;
                default :

                    $targetSize = isset(self::$imageSizes[$size]) ? self::$imageSizes[$size] : reset(self::$imageSizes);
                    $width = $targetSize['width'];
                    $height = $targetSize['height'];
                    $imageUrl = $this->image_resize($this->imageUrl($image), $width, $height);
                    $downsizedImage = array($imageUrl, $width, $height);

                    break;
            }

		}

        return $downsizedImage;
	}

	/**
	 *	Resize image
	 *
	 *	@param	string	Image url
	 *	@param	int		Width
	 *	@param	int		Height
	 *	@param	boolean	Is crop
	 *	@param	boolean	Is single
	 *	@param	boolean	Is upscale
	 *	@return string
	 */

	public function image_resize($url, $width = null, $height = null, $crop = null, $single = true, $upscale = false) {
		return $this->resizeImg($url, $width, $height);
	}

	/**
	 *	Alias for Resize Image
	 */

	public function rev_aq_resize($url, $width = null, $height = null, $crop = null, $single = true, $upscale = false) {
		return $this->image_resize($url, $width, $height, $crop, $single, $upscale);
	}

	/**
	 *	Convert image name to url
	 *
	 *	@param	string
	 *	@return	string
	 */

	public function image_to_url($image) {
		$image = $this->imageFile($image);
		if (empty($image) || strpos($image, '//') !== false) {
			$url = $image;
		} else {
			$url = $this->imageBaseUrl() . $image;
		}
        $urlImageData = explode('media/', $url);
        if (isset($urlImageData['1'])) {
            $url = $this->getBaseUrl() . ltrim($urlImageData['1'], '/');
        }
		return $url;
	}

	/**
	 *	Convert image url to path
	 *
	 *	@param	string
	 *	@return	string
	 */

	public function image_url_to_path($url) {
		if (strpos($url, $this->imageBaseUrl()) === false && strpos($url, $this->getBaseUrl()) !== false) {
			$image = str_replace($this->getBaseUrl(), '', $url);
			$path = $this->getBaseDir() . DIRECTORY_SEPARATOR . $image;
		} else {
			$image = str_replace($this->imageBaseUrl(), '', $url);
			$path = $this->imageBaseDir() . $image;
		}

		return $path;
	}

	/**
	 *	Get image url
	 *
	 *	@param	string
	 *	@return	string
	 */

	public function imageUrl($image) {
		if ($image && strpos($image, '//') === false) {
		    $url = $this->imageFile($image);
		    if (strpos($url, self::IMAGE_DIR . '/') === 0) {
		        $url = substr($url, strlen(self::IMAGE_DIR . '/'));
            }
            $url = $this->imageBaseUrl() . $url;
        } else {
            $url = $this->imageClean($image);
        }
        if ($this->_storeManager->getStore()->isCurrentlySecure()) {
            $url = str_replace('http://', 'https://', $url);
        }
		return $url;
	}

	/**
	 *	Get image path
	 *
	 *	@param	string
	 *	@return	string
	 */

	public function imagePath($image) {
		if (strpos($image, $this->imageBaseUrl()) === false && strpos($image, $this->getBaseUrl()) !== false) {
			$image = str_replace($this->getBaseUrl(), '', $image);
            if ($image) {
                $path = $this->getBaseDir() . DIRECTORY_SEPARATOR . str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $image);
            }
		} else {
            $image = str_replace($this->imageBaseUrl(), '', $image);
            if ($image) {
                $path = $this->imageBaseDir() . str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $image);
            }
		}
		return $path;
	}

	/**
	 *	Get image file from url or path
	 *
	 *	@param	string
	 *	@return	string
	 */

	public function imageFile($image) {
        $replace = array(
            $this->imageBaseDir(),
            $this->imageBaseUrl(),
            $this->getBaseUrl(),
            $this->getBaseDir()
        );
		$file = str_replace($replace, '', $this->imageClean($image));
		$file = ltrim($file, DIRECTORY_SEPARATOR . '/');
		return $file;
	}

	/**
	 *	Clean image from artifacts
	 *
	 *	@param	string
	 *	@return	string
	 */

	public function imageClean($image) {
		$image = str_replace(array('//', ':/'), array('/', '://'), $image);
		return $image;
	}

   /**
     * Get media base dir
     *
     * @return string
     */
   
    public function getBaseDir() {
        return $this->_directory->getAbsolutePath();
    }
    
	/**
	 *	Get images base path
	 *
	 *	@return	string
	 */

	public function imageBaseDir() {
        return $this->_directory->getAbsolutePath(self::IMAGE_DIR);
	}

   /**
     * Get media base url
     *
     * @return string
     */

    public function getBaseUrl() {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

	/**
	 *	Get images base url
	 *
	 *	@return	string
	 */

	public function imageBaseUrl() {
        return $this->getBaseUrl() . self::IMAGE_DIR . '/';
	}

    /**
     *  Remove admin url from images
     *
     *  @param  mixed
     *  @return mixed
     */

    public function relativeImagesUrl($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->relativeImagesUrl($value);
            }
        } elseif (is_object($data)) {
            $arr = (array) $data;
            foreach ($arr as $key => $value) {
                $arr[$key] = $this->relativeImagesUrl($value);
            }
            $data = (object) $arr;
        } elseif (is_string($data)) {
            $arr = json_decode($data);
            if (is_array($arr) || is_object($arr)) {
                $arr = $this->relativeImagesUrl((array) $arr);
                $data = json_encode($arr);
            } elseif (strpos($data, $this->imageBaseUrl()) !== false) {
                $data = $this->imageFile($data);
            }
        }
        return $data;
    }

}
