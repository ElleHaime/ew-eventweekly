<?php

namespace Core\Controllers;

use Phalcon\Mvc\ModelInterface,
	Core\Acl,
	Core\Utils as _U;


class CrudController extends \Core\Controller
{
	protected $formData	= array();
	protected $editExtraRelations = false;
	
	public function indexAction()
	{
	}

	public function listAction()
	{
		$object = $this -> getObj();
		$filters = $this -> getListFilters();
		$list = $object::find((array)$filters);

		if ($list -> count()) {
    		   $this -> view -> setVar('object', $list);
    		   $this -> view -> setVar('list', $list);
		}
	}

	public function editAction($id = false)
	{
		$object = $this -> getObj();
		$model = strtolower($this -> getModel());
		$this -> setEditExtraRelations();

		$this -> loadObject();
		$this -> obj -> member_id = $this -> memberId;

		$param = $this -> dispatcher -> getParam('id');

		if ($param !== null) {
			$this -> obj = $object::findFirst($param);
			
			$this -> obj -> setExtraRelations($this -> getEditExtraRelations());
			$this -> obj -> getDependencyProperty();
			//$this -> setDependencyProperty($this -> obj -> getDependency());
		} 
		$form = $this -> loadForm();

		if ($this -> request -> isPost() && !$this -> dispatcher -> wasForwarded()) {
			if ($form -> isValid($this -> request -> getPost())) {
				$redirectOptions = $this -> processForm($form);
				if(is_array($redirectOptions)) {
					$this -> loadRedirect($redirectOptions);
				} else {
					$this -> loadRedirect();
				}
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

			return $this -> response -> redirect('/' . strtolower($model). '/list');
		}
	}

	
	public function loadObject()
	{
		$object = $this -> getObj();
		$this -> obj = new $object;

		return $this;
	}

	public function loadForm($obj = false)
	{
		!$obj ? $formObject = $this -> obj : $formObject  = $obj;
		$model = $this -> getModel();

		$formClass = $this -> getFormPath() . $model . 'Form';
		$form = new $formClass($formObject);

		return $form;
	}


	public function loadRedirect($params = [])
	{
		$model = strtolower($this -> getModel());
		$this -> response -> redirect('/' . strtolower($model). '/list');
	}
	
	
	public function processForm($form) 
	{
		$dependencies = $this -> obj -> getDependency();

		if ($form -> isValid($this -> request -> getPost())) {
			$this -> formData = $form -> getFormValues();
			//$this -> processDependencyProperty($dependencies);

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


	/*public function setDependencyProperty($deps = false)
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
	} */


	public function getListFilters()
	{
	    $filter = 'member_id = ' . $this -> memberId;
	    return $filter;
	}

	public function setEditExtraRelations()
	{
	}

	public function getEditExtraRelations()
	{
		return $this -> editExtraRelations;
	}
}
