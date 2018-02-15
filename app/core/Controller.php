<?php
class Controller{

	private $roles;

	protected $userRole;

	protected $log;

	public function __construct()
	{
		$this->roles = array(0 => "menuAdm", 1 => "menuAdmin", 2 => "menuOne", 3 => "menuTwo", 4 => "menuZero", 10 => "menuFive");
		$this->userRole = $this->setRole();

		$this->logs = $this->model('log');
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
		if(!isset($_SESSION['csm']['id']))
		{
			header('Location: /csm/public/login');
		}
	}

	public function setRole()
	{
		$role = "";
		if(isset($_SESSION['csm']['role']))
		{
			$role = $this->roles[$_SESSION['csm']['role']];
		}
		else
		{
			if(!isset($_SESSION['csm']['id']))
			{
				header('Location: /csm/public/login');
			}
		}
		return $role;
	}

	public function saveLog($action)
	{
		$this->logs->saveLog($date, 1, $action);
	}
}