<?php
/**
* 
*/

namespace ARV\Factory;

# Imports
use WPPFW\Obj;

# Original object
use WPPFW\Database\Wordpress;

/**
* 
*/
class WordpressOptions {

	/**
	* put your comment there...
	* 
	* @param Obj\Factory $factory
	* @return Obj\Factory
	*/
	public function getInstance(Obj\Factory & $factory) {
		# Getting Plugin instance.
		$plugin =& $factory->get('WPPFW\Plugin\PluginBase');
		# Return Wordpress options object instance
		return new Wordpress\WordpressOptions(strtolower($plugin->getNamespace()->getNamespace() . '-'));
	}

}
