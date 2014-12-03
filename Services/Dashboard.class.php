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
use WPPFW\Services\Dashboard\Ajax\AjaxService;

/**
* 
*/
class DashboardModule extends ServiceModule {
	
	/**
	* 
	*/
	const VIEWER_AJAX_SERVICE_KEY = 1;

	/**
	* 
	*/
	const VIEWER_AJAX_SERVICE_OBJECT_KEY = 1;

	/**
	* 
	*/
	const VIEWER_SERVICE_KEY = 0;

	/**
	* 
	*/
	const VIEWER_SERVICE_OBJECT_KEY = 1;
	
	/**
	* put your comment there...
	* 
	* @param PluginBase $plugin
	* @param mixed $services
	*/
	protected function initializeServices(PluginBase & $plugin, & $services) {
		# Viewer page service
		$services[self::VIEWER_SERVICE_KEY] = new MenuService($plugin, array(
			self::VIEWER_SERVICE_OBJECT_KEY => new Dashboard\MenuPages\Viewer\Page()
		));
		# Ajax services
		$services[self::VIEWER_AJAX_SERVICE_KEY] = new AjaxService($plugin, array(
			self::VIEWER_AJAX_SERVICE_OBJECT_KEY => new Dashboard\Services\Ajax()
		));
	}
	
	/**
	* put your comment there...
	* 
	*/
	public function viewer() {
		return $this->getServiceObject(self::VIEWER_SERVICE_KEY, self::VIEWER_SERVICE_OBJECT_KEY);
	}

	/**
	* put your comment there...
	* 
	*/
	public function viewerService() {
		return $this->getServiceObject(self::VIEWER_AJAX_SERVICE_KEY, self::VIEWER_AJAX_SERVICE_OBJECT_KEY);
	}

}
