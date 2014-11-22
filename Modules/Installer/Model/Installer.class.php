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
	* 
	*/
	const OPERATION_CREATE_REPORT = 2;
		
	/**
	* 
	*/
	const OPERATION_CREATE_REPORTS_DIRECTORY = 1;
	
	/**
	* 
	*/
	const OPERATION_NONE = 0;

	/**
	* 
	*/
	const OPERATION_WRITE_INSTALLATION_FLAGS = 3;
	
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
	protected $indexFileName = 'index.php';

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
	protected $installsParamsDomain;
	
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
	protected $lastOperation = self::OPERATION_NONE;
	
	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected $noListIndexFileRelPath;
	
	/**
	* put your comment there...
	* 
	* @var mixed
	*/
	protected $reportsDirectoryPath;
	
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
	*/
	public function & createReport() {
		# Initialize
		$reportModel =& $this->getReportViewerModel();
		# Pass installation-parameters to Report Viewer / required for building report
		$reportModel->setAWStatsParameters(
			$this->getInstallsParamsDomain(),
			$this->getInstallsParamsAWstatsScriptPath(),
			$this->getInstallsParamsBuildStaticScript(),
			$this->getInstallsParamsConfigFile(),
			$this->getInstallsParamsIconsDirectory()
		)
		# Pass installation parametes to report model / Required for creating report
		->setInstallationParameters(
			$this->getReportsDirectoryPath(),
			$this->getNoListIndexFileRelPath()
		);
		# Pipe Viewer Model errors to write to Installer Model
		$reportCreated = $reportModel->pipeErrors($this)
		# Generate new report unique id
		->generateRID()
		# Create/Build AWStats report for the first time
		->createReport();
		# Check weather if report created successful
		if ($reportCreated) {
			# Set as passed operation
			$this->lastOperation = self::OPERATION_CREATE_REPORT;
		}
		# Chain
		return $this;
	}

	/**
	* put your comment there...
	* 
	*/
	public function createReportsDirectory() {
		# Initialize
		$reportModel =& $this->getReportViewerModel();
		$plugin =& $this->factory()->get('ARV\Plugin');
		# Process only if no operation performed before
		if (!$this->lastOperation) {
			# Getting absolute path to reports directory
			$reportsDirectory = $reportModel->buildReportsDirectoryAbsolutePath($this->getReportsDirectoryPath());
			# Make sure we can create directory
			$reportsDirectoryParent = dirname($reportsDirectory);
			if (is_readable($reportsDirectoryParent) && is_writable($reportsDirectoryParent)) {
				# Try to create directory
				if (file_exists($reportsDirectory) || mkdir($reportsDirectory, 0755))	{
					# Copy index file to reorts directory if not already exists
					$desIndexFilePath = $reportsDirectory . DIRECTORY_SEPARATOR . $this->getIndexFileName();
					$srcIndexFilePath =  $plugin->getDirectory() . DIRECTORY_SEPARATOR . $this->getNoListIndexFileRelPath();
					# Creating directory index file
					if (file_exists($desIndexFilePath) || copy($srcIndexFilePath, $desIndexFilePath)) {
						# Set as last operation
						$this->lastOperation = self::OPERATION_CREATE_REPORTS_DIRECTORY;
					}
					else {
						# Report problem
						$this->addError("Could not create reports directory default index file: {$desIndexFilePath}");	
					}
				}
				else {
					# Report Problem
					$this->addError("Could not create reports directory: {$reportsDirectory}");
				}
			}
			else {
				# Report problem
				$this->addError("No enough permission to create reports holder directory: {$reportsDirectory}");
			}
		}
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
		# Initiaolize
		$installsParamsAWStatsPath = $this->getDiscoverAWStatsScriptPath();
		$discoverDomain = $this->getDiscoverDomain();
		$awstats = new \ARV\Modules\Report\Model\AWStats($installsParamsAWStatsPath);
		# AWStats src path
		$awstatsSrcPath = "/usr/local/cpanel/src/3rdparty/gpl/awstats-{$awstats->getVersion()}";
		# Config File path
		$currentFileFiles = explode(DIRECTORY_SEPARATOR, __FILE__);
		$homeDir = $currentFileFiles[1];
		$installsParamsConfigFile = "/{$homeDir}/{$this->getDiscoverSystemUser()}/tmp/awstats/awstats.{$this->getDiscoverDomain()}.conf";
		# Build static script path
		$installsParamsBuildStaticScript = "{$awstatsSrcPath}/tools/awstats_buildstaticpages.pl";
		# Icons Directory path
		$installsParamsIconsDirectory = "{$awstatsSrcPath}/wwwroot/icon";
		# Fill Form with discovered data
		$form->getAWStatsScriptPath()->setValue($installsParamsAWStatsPath);
		$form->getBuildStaticPath()->setValue($installsParamsBuildStaticScript);
		$form->getConfigFilePath()->setValue($installsParamsConfigFile);
		$form->getIconsDirPath()->setValue($installsParamsIconsDirectory);
		$form->getDomain()->setValue($discoverDomain);
		# Chain
		return $this;
	}

	/**
	* put your comment there...
	* 
	*/
	public function & done() {
		# Write database version / Mark as insalled
		# Do that only if passed CREATE REPORT OPERATION
		if ($this->lastOperation == self::OPERATION_CREATE_REPORT) {
			# Write version number
			$this->installedVersion = $this->dbVersion;	
			# Set as last operation
			$this->lastOperation = self::OPERATION_WRITE_INSTALLATION_FLAGS;
		}
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
	public function getCurrentOperation() {
		return $this->lastOperation;
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
	public function getIndexFileName() {
		return $this->indexFileName;
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
	public function getInstallsParamsDomain() {
		return $this->installsParamsDomain;
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
	public function getNoListIndexFileRelPath() {
		return $this->noListIndexFileRelPath;
	}
	
	/**
	* put your comment there...
	* 
	*/
	protected function & getReportViewerModel() {
		return $this->mvcServiceManager()->getModel('Viewer', 'Report');
	}

	/**
	* put your comment there...
	* 
	*/
	public function getReportsDirectoryPath() {
		return $this->reportsDirectoryPath;
	}

	/**
	* put your comment there...
	* 
	*/
	public function isAllProcessed() {
		return ($this->lastOperation == self::OPERATION_WRITE_INSTALLATION_FLAGS);
	}

	/**
	* put your comment there...
	* 
	* @param mixed $operation
	*/
	public function isCurrentOperation($operation) {
		return ($this->getCurrentOperation() == $operation);
	}

  /**
  * put your comment there...
  * 
  */
  protected function initialize() {
  	# Getting Plugin configuration
  	$factory =& $this->factory();
  	$plugin =& $factory->get('ARV\Plugin');
		$this->pluginConfig =& $plugin->getPluginConfig();
		$this->dbVersion = $this->pluginConfig['parameters']['dbVersion'];
		$this->installState = new InstallState($plugin);
		$this->reportsDirectoryPath 	= 'wp-content' . DIRECTORY_SEPARATOR . 'arv-reports';
		$this->noListIndexFileRelPath = 'Modules' . DIRECTORY_SEPARATOR . 
																		'Installer' . DIRECTORY_SEPARATOR . 
																		'Model' . DIRECTORY_SEPARATOR . 
																		'Installer' . DIRECTORY_SEPARATOR . $this->getIndexFileName();
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
		$form->getDomain()->setValue($this->getInstallsParamsDomain());
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
	* @param mixed $domain
	* @param mixed $scriptPath
	* @param mixed $buildStaticPath
	* @param mixed $configFilePath
	* @param mixed $iconsDir
	* @return InstallerModel
	*/
	public function setInstallationParameters($domain, $scriptPath, $buildStaticPath, $configFilePath, $iconsDir) {
		# Set
		$this->installsParamsDomain =& $domain;
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
		  $form->getDomain()->getValue(),	
			$form->getAWStatsScriptPath()->getValue(),
			$form->getBuildStaticPath()->getValue(),
			$form->getConfigFilePath()->getValue(),
			$form->getIconsDirPath()->getValue()
		);
	}

}
