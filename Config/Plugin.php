<?php
/**
* 
*/

namespace ARV\Config;

# Imports
use WPPFW\Plugin\PluginConfig;

/**
* 
*/
class Plugin extends PluginConfig {
	
	/**
	* put your comment there...
	* 
	*/
	public function __construct() {
		# Load plugin.xml file relative to this class
		parent::__construct(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'plugin.xml'));
	}
	
}