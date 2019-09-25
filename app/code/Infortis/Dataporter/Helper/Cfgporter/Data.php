<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Regular License.
 * You may not use any part of the code in whole or part in any other software
 * or product or website.
 *
 * @author      Infortis
 * @copyright   Copyright (c) 2014 Infortis
 * @license     Regular License http://themeforest.net/licenses/regular 
 */

namespace Infortis\Dataporter\Helper\Cfgporter;

use Infortis\Dataporter\Helper\Data as HelperData;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem;
use Magento\Framework\Module\Dir\Reader;
class Data extends AbstractHelper
{
    /**
     * @var Filesystem
     */
    protected $_frameworkFilesystem;

    /**
     * @var Reader
     */
    protected $_dirReader;

    const PRESET_FILE_MAIN_DIR      = 'config';

    /**
     * Modules associated with package
     *
     * @var array
     */
    protected $_packageModules = [
        'Infortis_Base' =>
            ['Infortis_Base', 'Infortis_Brands', 'Infortis_UltraMegamenu', 'Infortis_UltraSlideshow'],
        'Infortis_Ultimo' =>
            ['Infortis_Base', 'Infortis_Ultimo', 'Infortis_Brands', 'Infortis_UltraMegamenu', 'Infortis_UltraSlideshow'],
        'Infortis_Fortis' =>
            ['Infortis_Base', 'Infortis_Fortis', 'Infortis_Brands', 'Infortis_UltraMegamenu', 'Infortis_UltraSlideshow'],
    ];

    /**
     * Human-readable names of modules
     *
     * @var array
     */
    protected $_moduleNames = [
        'Infortis_Base'             => 'Infortis Themes - Configuration',
        'Infortis_Ultimo'           => 'Ultimo',
        'Infortis_Fortis'           => 'Fortis',
        'Infortis_Brands'           => 'Brands',
        'Infortis_CloudZoom'        => 'Zoom',
        'Infortis_UltraMegamenu'    => 'Menu',
        'Infortis_UltraSlideshow'   => 'Slideshow',
    ];

    /**
     * File path elements
     *
     * @var string
     */
    protected $_presetFileExt = 'xml';
    protected $_presetFileModuleDirType = 'etc';
    protected $_presetFileTmpBaseDir;

    /**
     * Resource initialization
     */
    public function __construct(Context $context, 
        Filesystem $frameworkFilesystem, 
        Reader $dirReader)
    {
        $this->_frameworkFilesystem = $frameworkFilesystem;
        $this->_dirReader = $dirReader;

        //converted mage 1 code -- but there's no export in mage2
        $this->_presetFileTmpBaseDir = $this->_frameworkFilesystem
            ->getDirectoryWrite('var')->getAbsolutePath() . 
            DIRECTORY_SEPARATOR . 'export';
        
        if(!is_dir($this->_presetFileTmpBaseDir))
        {
            mkdir($this->_presetFileTmpBaseDir, 0755);
        }
            
    }

    /**
     * Get modules associated with package
     *
     * @param string
     * @return array
     */
    public function getPackageModules($package)
    {
        if (isset($this->_packageModules[$package]))
        {
            return $this->_packageModules[$package];
        }
    }

    /**
     * Get human-readable names of modules
     *
     * @return array
     */
    public function getModuleNames()
    {
        return $this->_moduleNames;
    }

    /**
     * Get human-readable name of a module
     *
     * @param string
     * @return string
     */
    public function getModuleName($module)
    {
        if (isset($this->_moduleNames[$module]))
        {
            return $this->_moduleNames[$module];
        }
    }

    /**
     * Determines and returns file path of the config preset file
     *
     * @param string
     * @param string
     * @return string
     */
    public function getPresetFilepath($filename, $modulename)
    {
        $baseDir = $this->getPresetDir($modulename);
        return $baseDir . DIRECTORY_SEPARATOR . $filename . '.' . $this->_presetFileExt;
    }

    /**
     * Determines and returns path of the directory with config preset files
     *
     * @param string
     * @return string
     */
    public function getPresetDir($modulename)
    {
        if ($modulename)
        {
            $baseDir = $this->_dirReader->getModuleDir($this->_presetFileModuleDirType, $modulename);
        }
        else
        {
            $baseDir = $this->_presetFileTmpBaseDir . DIRECTORY_SEPARATOR . HelperData::FILE_TOP_LEVEL_DIR;
        }

        return $baseDir . DIRECTORY_SEPARATOR . HelperData::FILE_MAIN_DIR . DIRECTORY_SEPARATOR . self::PRESET_FILE_MAIN_DIR;
    }
}
