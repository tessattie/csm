<?php
class Controller{

	private $roles;

	protected $userRole;

	public function __construct()
	{
		$this->roles = array(1 => "menuAdmin", 2 => "menuOne", 3 => "menuTwo", 4 => "menuZero");
		$this->userRole = $this->setRole();
	}

	public function model($model)
	{
		if(file_exists('../app/models/' . $model . '.php'))
		{
			require_once '../app/models/' . $model . '.php';
			$return = new $model();
		}
		else
		{
			$return = false;
		}
		return $return;
	}

	public function phpExcel()
	{
		if(file_exists('../app/vendors/PHPExcel/Classes/PHPExcel.php'))
		{
			require_once '../app/vendors/PHPExcel/Classes/PHPExcel.php';
		}
		else
		{
			require_once '../app/vendors/PHPExcel/Classes/PHPExcel.php';
		}
		return new PHPExcel();
	}

	public function view($view, $data = [])
	{
		if(file_exists('../app/views/'. $view . '/index.php'))
		{
			require_once '../app/views/'. $view . '/index.php';
		}
		else
		{
			require_once '../app/views/default.php';
		}
	}

	public function checkSession()
	{
		if(!isset($_SESSION['id']))
		{
			header('Location: /csm/public/login');
		}
	}

	public function setRole()
	{
		$role = "";
		if(isset($_SESSION['role']))
		{
			$role = $this->roles[$_SESSION['role']];
		}
		else
		{
			if(!isset($_SESSION['id']))
			{
				header('Location: /csm/public/login');
			}
		}
		return $role;
	}
}