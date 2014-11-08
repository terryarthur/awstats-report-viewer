<?php
/**
* 
*/

namespace ARV;

# Imports
use WPPFW\Plugin\PluginFactory;

/**
* 
*/
class Factory extends PluginFactory {
	
	/**
	* put your comment there...
	* 
	*/
	protected function createMap() {
		# Create Map.
		$this->addClassMap('WPPFW\Database\Wordpress\WordpressOptions', 'WordpressOptions');
	}

}