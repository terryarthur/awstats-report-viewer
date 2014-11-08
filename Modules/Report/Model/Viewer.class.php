<?php
/**
* 
*/

namespace ARV\Modules\Report\Model;

# Models Framework
use WPPFW\MVC\Model\PluginModel;

/**
* 
*/
class ViewerModel extends PluginModel {
	
	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected $awstatsScript;
	
	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected $buildStaticScript;
	
	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected $configFile;
	
	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected $iconsDirectory;
	
	/**
	* put your comment there...
	* 
	* @param mixed $awstatsScript
	* @param mixed $buildStaticScript
	* @param mixed $configFile
	* @param mixed $iconsDirectory
	*/
	public function & setAWStatsParameters($awstatsScript, $buildStaticScript, $configFile, $iconsDirectory) {
		# Initialize
		$this->awstatsScript =& $awstatsScript;
		$this->buildStaticScript =& $buildStaticScript;
		$this->configFile =& $configFile;
		$this->iconsDirectory =& $iconsDirectory;
		# Chain
		return $this;
	}

	/**
	* put your comment there...
	* 
	*/
	public function createReport() {
		
	}

}
