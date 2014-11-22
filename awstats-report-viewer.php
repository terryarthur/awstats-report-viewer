<?php
/**
* Plugin Name: AWStats Report Viewer
* Plugin URI: http://wp-arv.xptrdev.com
* Author: AHMeD SAiD
* Author URI: http://xptrdev.com
* Version: 0.5
* Description: View CPanel's AWStats report via Wordpress Dashboard page.
* License: GPL2
*/

# ARV Namespace
namespace ARV;

# Constants
const NO_DIRECT_ACCESS_MESSAGE = 'Access Denied';

# Wordpres Plugin Framework
use WPPFW\Plugin\PluginBase;

# Installer Module that will get all the other modules loaded
use ARV\Services\InstallerModule;

# Class Autoloader
require 'vendor/autoload.php';

/**
* 
*/
class Plugin extends PluginBase {
	
	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected static $instance;
	
	/**
	* put your comment there...
	* 
	*/
	protected function __construct() {
		# Plugin base
		parent::__construct(__FILE__, new Config\Plugin());
		# Only admin side is used in this Plugin
		if (is_admin()) {
			# Dashboad Service Module
			$installerModule = new  Services\InstallerModule($this);
			$installerModule->start();
		}		
	}

	/**
	* put your comment there...
	* 
	*/
	public static function run() {
		# Create if not yet created
		if (!self::$instance) {
			self::$instance = new Plugin();
		}
		# Return instance
		return self::$instance;
	}

}

# Run
Plugin::run();