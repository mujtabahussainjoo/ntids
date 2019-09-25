<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Regular License.
 * You may not use any part of the code in whole or part in any other software
 * or product or website.
 *
 * @author		Infortis
 * @copyright	Copyright (c) 2014 Infortis
 * @license		Regular License http://themeforest.net/licenses/regular 
 */

namespace Infortis\Dataporter\Helper;

// use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Backend\Helper\Data as BackendDataHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem;
class Data extends BackendDataHelper
{
    /**
     * @var Filesystem
     */
    protected $_frameworkFilesystem;

	const FILE_TOP_LEVEL_DIR	= 'dataporter';
	const FILE_MAIN_DIR			= 'importexport';

	/**
	 * File path elements
	 *
	 * @var string
	 */
	protected $_tmpFileBaseDir; //Desitnation directory for files uploaded via form

	/**
	 * Resource initialization
	 */
	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Route\Config $routeConfig,
        \Magento\Framework\Locale\ResolverInterface $locale,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Backend\Model\Auth $auth,
        \Magento\Backend\App\Area\FrontNameResolver $frontNameResolver,
        \Magento\Framework\Math\Random $mathRandom,        
        Filesystem $frameworkFilesystem)
	{
        $this->_frameworkFilesystem = $frameworkFilesystem;
		$this->_tmpFileBaseDir = $this->_frameworkFilesystem->getDirectoryWrite('media')->getAbsolutePath() . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . self::FILE_TOP_LEVEL_DIR . DIRECTORY_SEPARATOR;
		return parent::__construct($context, $routeConfig, $locale, $backendUrl,
		    $auth, $frontNameResolver, $mathRandom, $frameworkFilesystem);
	}

	/**
	 * Get desitnation directory for files uploaded via form
	 *
	 * @return string
	 */
	public function getTmpFileBaseDir()
	{
		return $this->_tmpFileBaseDir;
	}

}
