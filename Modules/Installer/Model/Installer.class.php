<?php
/**
* 
*/

namespace ARV\Modules\Installer\Model;

# Model base
use WPPFW\MVC\Model\PluginModel;

/**
* 
*/
class InstallerModel extends PluginModel {
	
	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	private $dbVersion;
	
	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected $discoverAWStatsScript;
	
	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected $discoverDomain;

	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected $discoverSystemUser;

	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected $installsParamsAWStatsPath;
	
	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected $installsParamsBuildStaticScript;

	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected $installsParamsConfigFile;
	
	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected $installsParamsIconsDirectory;

	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	private $installState;
	
	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected $installedVersion = '';
	
	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected $reset = true;
	
	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	private $pluginConfig;

	/**
	* put your comment there...
	* 
	* @return InstallerModel
	*/
	public function clearState() {
		# Clear state
		$this->discoverAWStatsScript = null;
		$this->discoverDomain = null;
		$this->discoverSystemUser = null;
		$this->installsParamsAWStatsPath = null;
		$this->installsParamsBuildStaticScript = null;
		$this->installsParamsConfigFile = null;
		$this->installsParamsIconsDirectory = null;
		# Set reset to false
		$this->reset = true;
		# Chain
		return $this;
	}

	/**
	* put your comment there...
	* 
	* @param Forms\InstallationParametersForm $form
	* @return {Forms\InstallationParametersForm|InstallerModel}
	*/
	public function discoverInstallationParameters(Forms\InstallationParametersForm & $form) {
		# Use awstats script path as it
		$installsParamsAWStatsPath = $this->getDiscoverAWStatsScriptPath();
		# Run AWStats, get versio number from meta tag located in the eight's line
		exec($installsParamsAWStatsPath, $awstatsResult);
		$generatorMetaLine = $awstatsResult[8];
		# Get version number from genearator string
		preg_match('/content\="AWStats\s{1}(\d+\.\d+)\s{1}/', $generatorMetaLine, $metaLineRegMatch);
		$awstatsVersionNumber = $metaLineRegMatch[1];
		# AWStats src path
		$awstatsSrcPath = "/usr/local/cpanel/src/3rdparty/gpl/awstats-{$awstatsVersionNumber}";
		# Config File path
		$currentFileFiles = explode(DIRECTORY_SEPARATOR, __FILE__);
		$homeDir = $currentFileFiles[1];
		$installsParamsConfigFile = "{$homeDir}/{$this->getDiscoverSystemUser()}/tmp/awstats/awstats.{$this->getDiscoverDomain()}.conf";
		# Build static script path
		$installsParamsBuildStaticScript = "{$awstatsSrcPath}/awstats_buildstaticpages.pl";
		# Icons Directory path
		$installsParamsIconsDirectory = "{$awstatsSrcPath}/wwwroot/icon";
		# Fill Form with discovered data
		$form->getAWStatsScriptPath()->setValue($installsParamsAWStatsPath);
		$form->getBuildStaticPath()->setValue($installsParamsBuildStaticScript);
		$form->getConfigFilePath()->setValue($installsParamsConfigFile);
		$form->getIconsDirPath()->setValue($installsParamsIconsDirectory);
		# Chain
		return $this;
	}

	/**
	* put your comment there...
	* 
	*/
	public function & done() {
		# Write database version / Mark as insalled
		$this->installedVersion = $this->dbVersion;
		# Chain
		return $this;
	}

	/**
	* put your comment there...
	* 
	*/
	public function enterReadyState() {
		# Unreset
		$this->reset = false;
		# Chain
		return $this;
	}

	/**
	* put your comment there...
	* 
	*/
	public function getDBVersion() {
		return $this->dbVersion;
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function getDefaultDiscoverAWStatsScriptPath() {
		return '/usr/local/cpanel/3rdparty/bin/awstats.pl';
	}

	/**
	* put your comment there...
	* 
	*/
	public function getDefaultDiscoverDomain() {
		return $_SERVER['HTTP_HOST'];
	}

	/**
	* put your comment there...
	* 
	*/
	public function getDefaultDiscoverSystemUser() {
		return get_current_user();
	}

	/**
	* put your comment there...
	* 
	*/
	public function getDiscoverAWStatsScriptPath() {
		return $this->discoverAWStatsScript;
	}

	/**
	* put your comment there...
	* 
	*/
	public function getDiscoverDomain() {
		return $this->discoverDomain;
	}

	/**
	* put your comment there...
	* 
	*/
	public function getDiscoverSystemUser() {
		return $this->discoverSystemUser;
	}

	/**
	* put your comment there...
	* 
	*/
	public function getInstalledVersion() {
		return $this->installedVersion;
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function getInstallsParamsAWstatsScriptPath() {
		return $this->installsParamsAWStatsPath;
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function getInstallsParamsBuildStaticScript() {
		return $this->installsParamsBuildStaticScript;
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function getInstallsParamsIconsDirectory() {
		return $this->installsParamsIconsDirectory;
	}

	/**
	* put your comment there...
	* 
	*/
	public function getInstallsParamsConfigFile() {
		return $this->installsParamsConfigFile;
	}

  /**
  * put your comment there...
  * 
  */
  protected function initialize() {
  	# Getting Plugin configuration
  	$factory =& $this->factory();
  	$plugin =& $factory->get('WPPFW\Plugin\PluginBase');
		$this->pluginConfig =& $plugin->getPluginConfig();
		$this->dbVersion = $this->pluginConfig['parameters']['dbVersion'];
		$this->installState = new InstallState($plugin);
  }

	/**
	* put your comment there...
	* 	
	*/
	public function isInstalled() {
		return $this->installState->isInstalled();
	}

	/**
	* put your comment there...
	* 
	*/
	public function isReady() {
		return !$this->reset;
	}

	/**
	* put your comment there...
	* 
	* @param Forms\InstallationParametersForm $form
	* @return {Forms\InstallationParametersForm|InstallerModel}
	*/
	public function & readInstallationParameters(Forms\InstallationParametersForm & $form) {
		# Fill form from stored state
		$form->getAWStatsScriptPath()->setValue($this->getInstallsParamsAWstatsScriptPath());
		$form->getBuildStaticPath()->setValue($this->getInstallsParamsBuildStaticScript());
		$form->getConfigFilePath()->setValue($this->getInstallsParamsConfigFile());
		$form->getIconsDirPath()->setValue($this->getInstallsParamsIconsDirectory());
		# Chain
		return $this;
	}

	/**
	* put your comment there...
	* 
	* @param mixed $scriptPath
	* @param mixed $domainName
	* @param mixed $systemUser
	* @return InstallerModel
	*/
	public function setDiscoverParameters($scriptPath, $domainName, $systemUser) {
		# Set
		$this->discoverAWStatsScript =& $scriptPath;
		$this->discoverDomain =& $domainName;
		$this->discoverSystemUser =& $systemUser;
		# Chain
		return $this;
	}

	/**
	* put your comment there...
	* 
	* @param mixed $scriptPath
	* @param mixed $buildStaticPath
	* @param mixed $configFilePath
	* @param mixed $iconsDir
	* @return InstallerModel
	*/
	public function setInstallationParameters($scriptPath, $buildStaticPath, $configFilePath, $iconsDir) {
		# Set
		$this->installsParamsAWStatsPath =& $scriptPath;
		$this->installsParamsBuildStaticScript =& $buildStaticPath;
		$this->installsParamsConfigFile =& $configFilePath;
		$this->installsParamsIconsDirectory =& $iconsDir;
		# Chain
		return $this;
	}

	/**
	* put your comment there...
	* 
	* @param Forms\AWStatsDiscoverParametersForm $form
	* @return {Forms\AWStatsDiscoverParametersForm|InstallerModel}
	*/
	public function setInstallationParametersForm(Forms\InstallationParametersForm & $form) {
		# Set and Chain
		return $this->setInstallationParameters(
			$form->getAWStatsScriptPath()->getValue(),
			$form->getBuildStaticPath()->getValue(),
			$form->getConfigFilePath()->getValue(),
			$form->getIconsDirPath()->getValue()
		);
	}

}
