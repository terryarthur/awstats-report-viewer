<?php
/**
* 
*/

namespace ARV\Modules\Installer\Model\Forms;

# Forms Framework
use WPPFW\Forms;

/**
* 
*/
class InstallationParametersForm extends Forms\Form {
	
	/**
	* 
	* 
	*/
	public function __construct() {
		# Form name
		parent::__construct('awstatsParams');
		# Form fields
		# AWStats script path
		$this->addChain(new Forms\Fields\FormStringField('scriptPath'))
		# Build static script path
		->addChain(new Forms\Fields\FormStringField('buildStaticPath'))
		# Config file path
		->addChain(new Forms\Fields\FormStringField('configFile'))
		# Icons directory
		->addChain(new Forms\Fields\FormStringField('iconsDir'));
	}

	/**
	* put your comment there...
	* 
	*/
	public function & getAWStatsScriptPath() {
		return $this->get('scriptPath');
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function & getBuildStaticPath() {
		return $this->get('buildStaticPath');
	}

	/**
	* put your comment there...
	* 
	*/
	public function & getConfigFilePath() {
		return $this->get('configFile');
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function & getIconsDirPath() {
		return $this->get('iconsDir');
	}

}
