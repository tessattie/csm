<?php
class login extends Controller{
	
	private $users;

	public function __construct()
	{
		$this->users = $this->model('users');
	} 
	
	public function index($errorMessage = '')
	{
		if(!empty($_SESSION['id']))
		{
			header('Location: /csm/public/home');
		}
		// csmreport51M
		if(isset($_POST['submit']))
		{
			$password = $this->users->isUsername($_POST['username']);
			if(empty($password))
			{
				$errorMessage = '<p class="bg-danger">This username does not exist</p>';
			}
			else
			{
				$user = $this->users->getUser($_POST['username'], sha1($_POST['password']));
				if(empty($user))
				{
					$errorMessage = '<p class="bg-danger">The username and password do not match</p>';
				}
				else
				{
					$this->startUserSession($user);
					$this->rememberUser($_POST);
					header('Location: /csm/public/home');
				}
			}
		}
		$this->view('login', array('error' => $errorMessage));
	}

	private function startUserSession($user)
	{
		session_start();
		$_SESSION["id"] = $user['id'];
		$_SESSION["username"] = $user['username'];
		$_SESSION["email"] = $user['email'];
		$_SESSION["firstname"] = $user['firstname'];
		$_SESSION["lastname"] = $user['lastname'];
		$_SESSION["role"] = $user['role'];
	}

	private function rememberUser($post)
	{
		if(isset($post['rememberMe']))
		{
            $month = time() + (60 * 60 * 24 * 30);
            setcookie('remember', $post['username'], $month);
            setcookie('username', $post['username'], $month);
            setcookie('password', $post['password'], $month);
        } 
        elseif (!isset($post['remember'])) 
        {
            $past = time() - 100;
            if (isset($_COOKIE['remember'])) 
            {
                setcookie('remember', '', $past);
            } 
            elseif (isset($_COOKIE['username'])) 
            {
                setcookie('username', '', $past);
            } 
            elseif (isset($_COOKIE['password'])) 
            {
                setcookie('password', '', $past);
            }
        }
	}

}