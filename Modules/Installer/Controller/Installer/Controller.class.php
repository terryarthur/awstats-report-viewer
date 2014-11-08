<?php
/**
* 
*/

namespace ARV\Modules\Installer\Controller\Installer;

# Controller Framework
use WPPFW\MVC\Controller\Controller;

# Installation form
use ARV\Modules\Installer\Model\Forms\AWStatsDiscoverParametersForm;
use ARV\Modules\Installer\Model\Forms\InstallationParametersForm;

# Models
use ARV\Modules\Installer\Model\InstallerModel;

/**
* 
*/
class InstallerController extends Controller {

	/**
	* put your comment there...
	* 
	*/
	protected function indexAction() {
		# Initialize
		$input =& $this->input();
		$discoverInstallationParams = true;
		$discoverForm = new AWStatsDiscoverParametersForm();
		$installationForm = new InstallationParametersForm();
		/**
		* put your comment there...
		* 
		* @var InstallerModel
		*/
		$installerModel =& $this->getModel();
		# Initial discover or user submission discover
		if (!$input->isPost()) {
			# Set Model Discover parameters if not yet set
			if (!$installerModel->isReady()) {
				# Set default data
				$installerModel->setDiscoverParameters(
					$installerModel->getDefaultDiscoverAWStatsScriptPath(),
					$installerModel->getDefaultDiscoverDomain(),
					$installerModel->getDefaultDiscoverSystemUser()
				)
				# Discover installation parameters
				# Fill Installation parameters form
				->discoverInstallationParameters($installationForm)
				# Save discovered parameters @ model state
				->setInstallationParametersForm($installationForm)
				# Enter ready state
				->enterReadyState();
			}
			else {
				# Fill installation parameters form from model state
				$installerModel->readInstallationParameters($installationForm);
				# Validate installation parameters form so that
				# view template could display invalid errors.
				# this is useful when returned back from install action
				# when the installation form is invalidated!
				$installationForm->validate();
			}
			# Fill discover form
			$discoverForm->getAWStatsScript()->setValue($installerModel->getDiscoverAWStatsScriptPath());
			$discoverForm->getDomain()->setValue($installerModel->getDiscoverDomain());
			$discoverForm->getSystemUser()->setValue($installerModel->getDiscoverSystemUser());
		}
		else {
			# Fill discover form
			$discoverForm->setValue($input->post()->getArray());
			# Validate discover form
			if ($discoverInstallationParams = $discoverForm->validate()) {
				# Set discover parameters through user form
				$installerModel->setDiscoverParameters(
					$discoverForm->getAWStatsScript()->getValue(),
					$discoverForm->getDomain()->getValue(),
					$discoverForm->getSystemUser()->getValue()
				)
				# Discover installation parameters
				->discoverInstallationParameters($installationForm)
				# Save discovered parameters @ model state
				->setInstallationParametersForm($installationForm);
			}			
		}
		# Pass Discover form to view
		return (object) array('discoverForm' => $discoverForm, 'installationForm' => $installationForm);
	}

	/**
	* put your comment there...
	* 
	*/
	protected function installAction() {
		# Initialize
		$installationForm = new InstallationParametersForm();
		$input =& $this->input();
		$route =& $this->router();
		/**
		* put your comment there...
		* 
		* @var ARV\Modules\Installer\Model\InstallerModel
		*/
		$installerModel =& $this->getModel();
		# Read installation parameters values
		$installationForm->setValue($input->post()->getArray());
		# Save installation parameters at model state
		$installerModel->setInstallationParametersForm($installationForm);
		# Validate install parameters
		if ($installationForm->validate()) {
			# Save AWStats installation parameters at report Viewer Model
			$reportModel =& $this->getModel('Viewer', 'Report');
			$reportModel->setAWStatsParameters(
				$installationForm->getAWStatsScriptPath()->getValue(),
				$installationForm->getBuildStaticPath()->getValue(),
				$installationForm->getConfigFilePath()->getValue(),
				$installationForm->getIconsDirPath()->getValue()
			);
			# Create/Build AWStats report for the first time
			
			# Mark as installed / Store Database version at installer model
			$installerModel->done();
		}
		else {
			# Go to index action, display form errors, allow
			# user to repeat.
			$this->redirect($route->routeAction());
		}
		# Display success page
		return $installationForm;
	}

	/**
	* put your comment there...
	* 
	*/
	protected function resetAction() {
		# Initialize
		$model =& $this->getModel();
		$router =& $this->router();
		# Reset model state.
		$model->clearState();
		# Discover installation parameters by using default disocoverig (recycle)
		$this->redirect($router->routeAction());
	}

} # End class