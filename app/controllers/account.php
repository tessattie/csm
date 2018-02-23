<?php 
session_start();
class account extends Controller{

	private $users;

	private $exportURL;

	private $roles = [];

	private $from;
	
	private $to;


	public function __construct()
	{
		parent::__construct();
		$this->exportURL = "#";
		$this->roles = array(1 => "Admin", 2 => "Level 1", 3 => "Level 2", 4 => "Level 0", 10 => "Level 5");
		$this->users = $this->model('users');
		$this->from = date('Y-m-01');
		$this->to = date('Y-m-d');
	} 

	public function index($errormessage = '')
	{
		// Crypted default password
		$this->checkSession();
		$password = "af2c173028114539479ac8e71208f42d921dbafd";

		if(isset($_POST['submit']))
		{
			foreach($_POST AS $key => $value)
			{
				if(empty($value))
				{
					$users = $this->users->getUsers();
					$errormessage = "<p class='bg-danger'>You must complete all the fields</p>";
					$this->view('account', array('users' => $users, 'error' => $errormessage, "menu" => $this->userRole, "exportURL" => $this->exportURL, 'from' => $this->from, 'to' => $this->to));
				
				}
			}
			if(empty($this->roles[$_POST["role"]]))
			{
				$_POST["role"] = 3;
			}
			$_POST['password'] = $password;
			$this->users->setUser($_POST);
		}

		$users = $this->users->getUsers();
		$count = count($users);

		for($i=0;$i<$count;$i++)
		{
			$users[$i]['role'] = $this->roles[$users[$i]['role']];
		}

		$this->view('account', array('users' => $users, 'error' => $errormessage, "menu" => $this->userRole, "exportURL" => $this->exportURL, "from" => $this->from, "to" => $this->to));
	}

	public function delete($userId)
	{
		$this->users->deleteUser($userId);
		if($_SESSION['csm']['id'] == $userId)
		{
			header('Location: /csm/public/login');
		}
		else
		{
			header('Location: /csm/public/account');
		}
	}

	public function edit($id = false)
	{
		$errormessage = "";
		if(isset($_POST['submit']))
		{
			// print_r($_POST);die();
			$this->users->updateUser($_POST['firstname'], $_POST['lastname'], $_POST['username'], $_POST['email'], $_POST['role'], $_POST['id'], $_POST['vendors']);
		}
		$users = $this->users->getUsers();
		$user = $this->users->getUserById($id);
		$this->view('account/edit', array("user" => $user, "users" => $users, "error" => $errormessage, "menu" => $this->userRole, "exportURL" => $this->exportURL, "from" => $this->from, "to" => $this->to));
	}

	public function reset($userId)
	{
		$password = "01b307acba4f54f55aafc33bb06bbbf6ca803e9a";
		$this->users->setPassword($userId, $password);
		if($_SESSION['csm']['id'] == $userId)
		{
			header('Location: /csm/public/login');
		}
		else
		{
			header('Location: /csm/public/account');
		}
	}

	public function changePassword()
	{
		if(isset($_POST['oldpass']))
		{
			$oldpass = sha1($_POST['oldpass']);
			$user = $this->users->getUser($_SESSION['csm']['username'], sha1($_POST['oldpass']));
			if(!empty($user))
			{
				if(isset($_POST['newpass']) && isset($_POST['newpass2']) && $_POST['newpass2'] == $_POST['newpass'])
				{
					$this->users->setPassword($_SESSION['csm']['id'], sha1($_POST['newpass']));
					session_unset();
					session_destroy();
					header('Location: /csm/public/login');
				}
				else
				{
					header('Location: /csm/public/account');
				}
			}
			else
			{
				header('Location: /csm/public/account/');
			}
		}
	}
}