<?php

namespace Core\Controllers;

use Phalcon\Mvc\ModelInterface,
	Core\Acl,
	Core\Utils as _U;


class CrudController extends \Core\Controller
{
	protected $formData	= array();
	
	public function indexAction()
	{
	}

	public function listAction()
	{
		$object = $this -> getObj();
		$filters = $this -> getListFilters();
		$list = $object::find((array)$filters);
	
		if (count($list) != 0) {
    		   $this -> view -> setVar('object', $list);
		} 
	}

	public function editAction()
	{
		$object = $this -> getObj();
		$model = strtolower($this -> getModel());

		$this -> loadObject();
		$this -> obj -> member_id = $this -> memberId;

		if (isset($this -> queryGetVals[$model])) {
			$this -> obj = $object::findFirstById((int)$this -> queryGetVals[$model]);
			$this -> setDependencyProperty($this -> obj -> getDependency());			
		} 

		$form = $this -> loadForm();

		if ($this -> request -> isPost()) {
			if ($form -> isValid($this -> request -> getPost())) {
				$this -> processForm($form);
				$this -> loadRedirect();
			}
		}

		$this -> view -> $model = $this -> obj;
		$this -> view -> setVar('edit' . ucfirst($model), true);
		$this -> view -> form = $form;
	}


	public function deleteAction()
	{
		$object = $this -> getObj();
		$model = $this -> getModel();

		if (isset($this -> dp[strtolower($model)])) {
			$item = $object::findFirst((int)$this -> dp[strtolower($model)]);
			if (!$item -> delete()) {
				// Sad =/
			} 

			return $this -> response -> redirect(strtolower($model). '/list');
		}
	}


	public function loadObject()
	{
		$object = $this -> getObj();
		$this -> obj = new $object;

		return $this;
	}

	public function loadForm()
	{
		$model = $this -> getModel();

		$formClass = $this -> getFormPath() . $model . 'Form';
		$form = new $formClass($this -> obj);

		return $form;
	}


	public function loadRedirect()
	{
		$model = strtolower($this -> getModel());
		$this -> response -> redirect(strtolower($model). '/list');
	}
	
	public function processForm($form) 
	{
		$dependencies = $this -> obj -> getDependency();

		if ($form -> isValid($this -> request -> getPost())) {
			$this -> formData = $form -> getFormValues();
			$this -> processDependencyProperty($dependencies);

			$this -> obj -> assign($this -> formData);
			if (!$this -> obj -> save()) {
				foreach ($this -> obj -> getMessges() as $message) {
					$form -> addError($message);
				}
				$this -> view -> form = $form;
				return;
			}

			return;
		} 
	}


	public function setDependencyProperty($deps = false)
	{
		if ($deps) {
			foreach ($deps as $dep => $settings) {
				if (isset($settings['createOnChange'])) {
					$currentValName = 'current_' . $dep;
					$previousValName = 'prev_' . $dep;
					$compareField = $settings['createOnChangeField'];

					$this -> obj -> $dep ? $depValue = $this -> obj -> $dep -> $compareField : $depValue = '';
					$this -> obj -> $currentValName = $this -> obj -> $previousValName = $depValue;
				}
			}
		}

		return;
	}


	public function processDependencyProperty($deps = false)
	{
		if ($deps) {
			foreach ($deps as $dep => $settings) {
				if (isset($settings['createOnChange'])) {
					if ($this -> formData['prev_' . $dep] != $this -> formData['current_' . $dep]) {
						$depObjectModel = $this -> getModelPath() . ucfirst($dep);
						$depObject = new $depObjectModel;
						if (!isset($settings['createOnChangeRelation'])) {
							$settings['createOnChangeRelation'] = $dep . '_id';
						}

						$this -> formData[$settings['createOnChangeRelation']] =
							$depObject -> createOnChange($this -> formData['current_' . $dep]);
					}
				} 
			}
		}

		return;
	}


	public function getListFilters()
	{
	    $filter = 'member_id = ' . $this -> memberId;
	    return $filter;
	}
}
