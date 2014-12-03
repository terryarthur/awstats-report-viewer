<?php
/**
* 
*/

namespace ARV\Services;

# Module Framework
use WPPFW\Services\ServiceModule;

# Wordpres Plugin Framework
use WPPFW\Plugin\PluginBase;

# Menu Page service Framework
use WPPFW\Services\Dashboard\Menu\MenuService;

# Installer model
use ARV\Modules\Installer\Model\InstallState;

# ARV Modules
use ARV\Services\DashboardModule;

/**
* 
*/
class InstallerModule extends ServiceModule {
	
	/**
	* 
	*/
	const MENU_PAGE_SERVICE_KEY = 0;
	
	/**
	* 
	*/
	const MENU_PAGE_SERVICE_OBJECT_KEY = 0;

	
	/**
	* put your comment there...
	* 
	* @param PluginBase $plugin
	* @param mixed $services
	*/
	protected function initializeServices(PluginBase & $plugin, & $services) {
		# Initialize
		$installationState = new InstallState($plugin);
		# Always run installer module
		# Run Dashboard module only if installed
		if ($installationState->isInstalled()) { # Run installed services
			# create Dashboard module
			$dashboardModule = new DashboardModule($plugin);
			# Add to factory
			$plugin->factory()->setInstance($dashboardModule);
			# Run by current module
			$services[] = $dashboardModule;
		}
		else {
			# Installer Menu page
			$services[self::MENU_PAGE_SERVICE_KEY] = new MenuService($plugin, array(
				self::MENU_PAGE_SERVICE_OBJECT_KEY => new Installer\MenuPages\Installer\Page()
			));
		}
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function & pageServiceObject() {
		return $this->getServiceObject(self::MENU_PAGE_SERVICE_KEY, self::MENU_PAGE_SERVICE_OBJECT_KEY);
	}
}
