<?php 
class App{

	protected $controller = 'home';

	protected $method = 'index';

	protected $params = [];

	protected $rights = [];

	protected $roles = [];

	public function __construct()
	{
		ini_set('memory_limit', '700M');
		ini_set('max_execution_time', 10000);

		$this->roles = array(0 => "SuperAdmin", 1 => "Admin", 2 => "Level 1", 3 => "Level 2", 4 => "Level 0", 10 => 'Level 5');

		$this->rights = array(0 => array("SuperAdmin" => array("controllers" => array("home", "export", "account", "login", "error"), 
														  "actions" => array("index", "vendor", "UPCRange", "section", "vendorSection", 
							  												 "department", "vendorDepartment", "itemDescription", "logout",
							  												 "UPCPriceCompare", "vendorItemCode", "vendorNames", "changeExportFolder", "sectionMovement",
							  												 "sectionNames", "departmentNames", "delete", "reset", "changePassword", "UPCPriceCompare_url"))),
							  1 => array("Admin" => array("controllers" => array("home", "export", "account", "login", "error"), 
														  "actions" => array("index", "vendor", "UPCRange", "section", "vendorSection", 
							  												 "department", "vendorDepartment", "itemDescription", "logout",
							  												 "UPCPriceCompare", "vendorItemCode", "vendorNames", "changeExportFolder", "sectionMovement",
							  												 "sectionNames", "departmentNames", "delete", "reset", "changePassword", "UPCPriceCompare_url"))), 
							  2 => array("Level 1" => array("controllers" => array("home", "export", "login", "error"), 
							  								"actions" => array("index", "vendor","itemDescription", "logout",
							  												   "UPCPriceCompare", "vendorItemCode", "vendorNames", "UPCPriceCompare_url", "sectionMovement",
							  												   "sectionNames", "departmentNames", "changePassword", "changeExportFolder"))), 
							  3 => array("Level 2" => array("controllers" => array("home", "export", "login", "error"),
							  								"actions" => array("index", "itemDescription", "UPCPriceCompare", "logout",
							  												   "vendorItemCode", "vendorNames", "UPCPriceCompare_url", "sectionMovement",
							  												   "sectionNames", "changeExportFolder", "departmentNames", "delete", "reset", "changePassword"))), 
							  4 => array("Level 0" => array("controllers" => array("home", "export", "login", "error"),
							  								"actions" => array("index", "itemDescription", "UPCPriceCompare", "logout", "vendor", "vendorSection",
							  												   "vendorItemCode", "vendorNames", "UPCPriceCompare_url", "sectionMovement",
							  												   "sectionNames", "changeExportFolder", "departmentNames", "delete", "reset", "changePassword"))),
							  10 => array("Level 5" => array("controllers" => array("home", "export", "login", "error"),
							  								"actions" => array("index", "itemDescription", "UPCPriceCompare", "logout", "vendor", "vendorSection",
							  												   "vendorItemCode", "vendorNames", "UPCPriceCompare_url", "changeExportFolder", "departmentNames", "delete", "reset", "changePassword"))));

		$url = $this->parseUrl();
		
		if(file_exists('../app/controllers/' . $url[0] . '.php'))
		{
			$this->controller = $url[0];
			unset($url[0]);
		}

		require_once '../app/controllers/' . $this->controller . '.php';

		$controllerName = $this->controller;
		$this->controller = new $this->controller;

		if($controllerName != 'login')
		{
			$this->controller->checkSession();
			if($_SESSION['csm']['role'] == 1 || $_SESSION['csm']['role'] == 2 || $_SESSION['csm']['role'] == 3 || $_SESSION['csm']['role'] == 4 || $_SESSION['csm']['role'] == 10)
			{
				
			}else{
				unset($_SESSION);
				header('Location: /csm/public/login');
			}
		}

		$methodName = $this->method;

		if(isset($url[1]))
		{
			$methodName = $url[1];
			if(method_exists($this->controller, $url[1]))
			{
				$this->method = $url[1];
				unset($url[1]);
			}
		}

		$this->params = $url ? array_values($url) : [] ;


		if($controllerName != "login")
		{
			if(!empty($this->rights[$_SESSION['csm']["role"]][$this->roles[$_SESSION['csm']["role"]]]["controllers"][array_search($controllerName, $this->rights[$_SESSION['csm']["role"]][$this->roles[$_SESSION['csm']["role"]]]["controllers"])]) 
			&& !empty($this->rights[$_SESSION['csm']["role"]][$this->roles[$_SESSION['csm']["role"]]]["actions"][array_search($methodName, $this->rights[$_SESSION['csm']["role"]][$this->roles[$_SESSION['csm']["role"]]]["actions"])]))
			{
				call_user_func_array([$this->controller, $this->method], $this->params);
			}
			else
			{
				$this->controller = "error";
				require_once '../app/controllers/' . $this->controller . '.php';
				$this->controller = new $this->controller;
				$this->method = "index";

				call_user_func_array([$this->controller, $this->method], $this->params);
			}
		}
		else
		{
			call_user_func_array([$this->controller, $this->method], $this->params);
		}
		

		
	}

	public function parseUrl()
	{
		if(isset($_GET['url']))
		{
			return $url = explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
		}
	}
}