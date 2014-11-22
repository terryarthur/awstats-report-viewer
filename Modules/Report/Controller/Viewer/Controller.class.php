<?php
/**
* 
*/

namespace ARV\Modules\Report\Controller\Viewer;

# Imoprts
use WPPFW\MVC\Controller\Controller;

/**
* 
*/
class ViewerController extends Controller {

	/**
	* put your comment there...
	* 
	*/
	public function createAction() {
		# Initialize
		$model =& $this->getModel();
		$router =& $this->router();
		# Generate new report Id
		$model->generateRID()
		# Create new report
		->createReport();
		# Redirect to index, display report
		$this->redirect($router->routeAction());
	}

	/**
	* put your comment there...
	* 
	*/
	public function deleteAction() {
		# Initialize
		$model =& $this->getModel();
		$router =& $this->router();
		# Delete report
		$model->deleteReport();
		# Go to index
		$this->redirect($router->routeAction());
	}
	
	/**
	* put your comment there...
	* 
	*/
	protected function indexAction() {
		# Initialize
		$model =& $this->getModel();
		$input =& $this->input()->get();
		# Point to NoReport Template if no report yet defined
		if (!$model->hasReport()) {
			$this->mvcTarget()->setLayout('NoReport');	
		}
		else {
			# If not report file specifiyied use model state
			if ($file = $input->get('file')) {
				# Display report file as requested
				$model->setReportFile($file);	
			}
		}
		# Return Model
		return $model;
	}

	/**
	* put your comment there...
	* 
	*/
	public function regenerateAction() {
		# Initialize
		$model =& $this->getModel();
		$router =& $this->router();
		# Delete Reoprt
		$model->deleteReport();
		# Generate new reoprt ID
		$model->generateRID()
		# Create Report
		->createReport();
		# Redirect to index
		$this->redirect($router->routeAction());
	}

	/**
	* put your comment there...
	* 
	*/
	public function updateAction() {
		# INitialize
		$router =& $this->router();
		$model =& $this->getModel();
		# Update Report
		$model->updateReport();
		# Go to index
		$this->redirect($router->routeAction());
	}
	
} # End class