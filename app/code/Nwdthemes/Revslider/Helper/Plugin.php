<?php

namespace Nwdthemes\Revslider\Helper;

use \Magento\Framework\UrlInterface;
use \Magento\Framework\App\Filesystem\DirectoryList;

class Plugin extends \Magento\Framework\App\Helper\AbstractHelper {

	const WP_PLUGIN_DIR = 'revslider/plugins/';
	const MAX_FAILS = 5;

    protected $_optionsHelper;
    protected $_storeManager;
    protected $_queryHelper;
    protected $_curlHelper;
    protected $_filesystemHelper;
    protected $_imagesHelper;
    protected $_resource;
    protected $_googleFonts;
    protected $_revSliderLoadBalancer;

    protected static $directory;
    protected static $storeManager;

    private $_plugins = null;
    private $_pluginsLoaded = false;
    private $_activePlugins = null;

	/**
	 *	Constructor
	 */

	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Nwdthemes\Revslider\Helper\Data $dataHelper,
        \Nwdthemes\Revslider\Helper\Options $optionsHelper,
        \Nwdthemes\Revslider\Helper\Register $registerHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Nwdthemes\Revslider\Helper\Query $queryHelper,
        \Nwdthemes\Revslider\Helper\Curl $curlHelper,
        \Nwdthemes\Revslider\Helper\Filesystem $filesystemHelper,
        \Nwdthemes\Revslider\Helper\Images $imagesHelper,
        \Magento\Framework\App\ResourceConnection $resource,
        \Nwdthemes\Revslider\Model\Revslider\GoogleFonts $googleFonts
    ) {
        $this->_optionsHelper = $optionsHelper;
        $this->_registerHelper = $registerHelper;
        $this->_storeManager = $storeManager;
        $this->_queryHelper = $queryHelper;
        $this->_curlHelper = $curlHelper;
        $this->_filesystemHelper = $filesystemHelper;
        $this->_imagesHelper = $imagesHelper;
        $this->_resource = $resource;
        $this->_googleFonts = $googleFonts;

        self::$directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        self::$storeManager = $storeManager;

        parent::__construct($context);
	}

    /**
     *  Get plugin dir
     */

    public static function getPluginDir() {
        return self::$directory->getAbsolutePath() . self::WP_PLUGIN_DIR;
    }

    /**
     *	Get plugins url
     *
     *  @param  string  $file
     *  @param  string  $plugin
     *	@return	string
     */

    public function getPluginUrl($file, $plugin) {
        return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
        . self::WP_PLUGIN_DIR
        . basename($plugin, '.php')
        . '/'
        . $file;
    }

    /**
     *  Get installed plugins list
     *
     *  @return array
     */

    public function getPlugins() {
        if (is_null($this->_plugins)) {
            $this->_plugins = $this->_scanPlugins();
        }
        return $this->_plugins;
    }

    /**
     *  Check if plugin is active
     *
     *  @param  string  $plugin
     *  @return boolean
     */

    public function isPluginActive($plugin) {
        return in_array($plugin, $this->getActivePlugins());
    }

    /**
     *  Get list of active plugins
     *
     *  @return array
     */

    public function getActivePlugins() {
        if (is_null($this->_activePlugins)) {
            $activePlugins = $this->_optionsHelper->getOption('active_plugins');
            $this->_activePlugins = $activePlugins ? $activePlugins : array();
        }
        return $this->_activePlugins;
    }

    /**
     *  Activate plugin
     *
     *  @param  string  $plugin
     *  @return boolean
     */

    public function activatePlugin($plugin) {
        $activePlugins = $this->getActivePlugins();
        if ( ! in_array($plugin, $activePlugins)) {
            $activePlugins[] = $plugin;
            $this->_updateActivePlugins($activePlugins);
        }
        return true;
    }

    /**
     *  Deactivate plugin
     *
     *  @param  string  $plugin
     *  @return boolean
     */

    public function deactivatePlugin($plugin) {
        $activePlugins = $this->getActivePlugins();
        foreach ($activePlugins as $key => $_plugin) {
            if ($plugin == $_plugin) {
                unset($activePlugins[$key]);
            }
        }
        $this->_updateActivePlugins($activePlugins);
        return true;
    }

    /**
     *  Update plugin
     *
     *  @param  string  $updateUrl
     *  @param  string  $plugin
     *  @return boolean
     */

    public function updatePlugin($updateUrl, $plugin) {

        $url = "$updateUrl/magento2/addons/{$plugin}/{$plugin}.zip";
        $file = self::getPluginDir() . $plugin . '.zip';

        if ( ! $response = $this->_curlHelper->request($url, array('timeout' => 45))) {
            $result = false;
        }else{
            $this->_filesystemHelper->wp_mkdir_p(dirname($file));
            if ( ! @file_put_contents($file, $response['body'])) {
                $result = false;
            } else {
                if ($this->_filesystemHelper->unzip_file($file, self::getPluginDir())) {
                    $result = true;
                }
                @unlink($file);
            }
        }

        return $result;
    }

    /**
     * Load active plugins
     *
     * @param \Nwdthemes\Revslider\Helper\Framework $frameworkHelper
     */

    public function loadPlugins(\Nwdthemes\Revslider\Helper\Framework $frameworkHelper) {
        if ( ! $this->_pluginsLoaded) {

            if ($failed_plugin = $this->_optionsHelper->getOption('try_load_plugin')) {
                $fails_count = $this->_optionsHelper->getOption('fails_count', 0);
                if ($fails_count >= self::MAX_FAILS) {
                    $this->deactivatePlugin($failed_plugin);
                    $this->_optionsHelper->updateOption('fails_count', 0);
                } else {
                    $this->_optionsHelper->updateOption('fails_count', $fails_count + 1);
                }
                $this->_optionsHelper->updateOption('try_load_plugin', false);
            }

            foreach ($this->getActivePlugins() as $plugin) {
                if (file_exists(self::getPluginDir() . $plugin)) {
                    $this->_optionsHelper->updateOption('try_load_plugin', $plugin);
                    $frameworkHelper->includeFile(self::getPluginDir() . $plugin);
                    if ($failed_plugin == $plugin) {
                        $this->_optionsHelper->updateOption('fails_count', 0);
                    }
                    $this->_optionsHelper->updateOption('try_load_plugin', false);
                }
            }

            $this->_registerHelper->doAction('plugins_loaded', [
                $frameworkHelper,
                $this->_queryHelper,
                $this->_curlHelper,
                $this->_filesystemHelper,
                $this->_imagesHelper,
                $this->_resource,
                $this->_googleFonts,
                $this->_registerHelper,
                $this
            ]);

            $this->_pluginsLoaded = true;
        }
    }

    /**
     *  Find installed plugins
     *
     *  @return array
     */

    private function _scanPlugins() {
        $path = self::getPluginDir();
        $plugins = array();
        foreach (glob($path . '*' , GLOB_ONLYDIR) as $dir) {
            $dirName = basename($dir);
            $fileName = $dirName . '.php';
            $filePath = $dir . '/' . $fileName;
            if (file_exists($filePath)) {
                $plugin = array();
                $fileContent = file_get_contents($filePath);
                $fileContent = strstr($fileContent, '*/', true);
                foreach (explode("\n", $fileContent) as $line) {
                    $parts = explode(': ', $line);
                    if (count($parts) == 2) {
                        switch (trim(strtolower($parts[0]))) {
                            case 'plugin name' : $key = 'Name'; break;
                            case 'plugin uri' : $key = 'PluginURI'; break;
                            case 'description' : $key = 'Description'; break;
                            case 'author' : $key = 'Author'; break;
                            case 'version' : $key = 'Version'; break;
                            case 'author uri' : $key = 'AuthorURI'; break;
                            default: $key = str_replace(' ', '', trim($parts[0])); break;
                        }
                        $plugin[$key] = trim($parts[1]);
                    }
                }
                if (isset($plugin['Name']) && isset($plugin['Version'])) {
                    $plugin['Network'] = false;
                    $plugin['Title'] = $plugin['Name'];
                    $plugin['AuthorName'] = $plugin['Author'];
                    $plugins[$dirName . '/' . $fileName] = $plugin;
                }
            }
        }
		return $plugins;
    }

    /**
     *  Update active plugins
     *
     *  @param  array   $plugins
     */

    private function _updateActivePlugins($plugins) {
        $this->_activePlugins = $plugins;
        $this->_optionsHelper->updateOption('active_plugins', $plugins);
    }

}