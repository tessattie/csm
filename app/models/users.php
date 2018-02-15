<?php

class users{

	private $db ;

	public function __construct()
	{
		$server_name = 'HOST-STORE';
		$this->db = new PDO( "sqlsrv:server=".$server_name." ; Database = users", "sa", "BRd@t@123");
	}

	public function getUsers()
	{
		$SQL = "SELECT * FROM users WHERE (role=1 OR role=2 OR role=3 OR role=4 OR role=10) AND visible = 1 ORDER BY lastname";
		$result = $this->db->query($SQL);
		return $result->fetchall(PDO::FETCH_BOTH);
	}

	public function isUsername($username)
	{
		$SQL = "SELECT password FROM users WHERE username ='" . $username . "'";
		$result = $this->db->query($SQL);
		return $result->fetch(PDO::FETCH_BOTH)['password'];
	}

	public function getUser($username, $password)
	{
		$SQL = "SELECT * FROM users WHERE username ='" . $username . "' AND password = '" . $password . "'";
		$result = $this->db->query($SQL);
		return $result->fetch(PDO::FETCH_BOTH);
	}

	public function getUserByUsername($username)
	{
		$SQL = "SELECT * FROM users WHERE username ='" . $username . "'";
		$result = $this->db->query($SQL);
		return $result->fetch(PDO::FETCH_BOTH);
	}

	public function setUser($user)
	{
		$insert = $this->db->prepare("INSERT INTO users (firstname, lastname, username, password, email, role, vendors)
	    VALUES (:firstname, :lastname, :username, :password, :email, :role, :vendors)");

	    $insert->bindParam(':firstname', $user['firstname']);
	    $insert->bindParam(':lastname', $user['lastname']);
	    $insert->bindParam(':username', $user['username']);
	    $insert->bindParam(':password', $user['password']);
	    $insert->bindParam(':email', $user['email']);
	    $insert->bindParam(':role', $user['role']);
	    $insert->bindParam(':vendors', $user['vendors']);

	    $insert->execute();
	}

	public function getUserById($id)
	{
		$SQL = "SELECT * FROM users WHERE id =" . $id ;
		$result = $this->db->query($SQL);
		return $result->fetch(PDO::FETCH_BOTH);
	}

	public function deleteUser($id)
	{
		$delete = "DELETE FROM users WHERE id = '" . $id . "'";
		$this->db->query($delete);		
	}

	public function updateUser($firstname, $lastname, $username, $email, $role, $id)
	{
		$update = "UPDATE users SET firstname ='" . $firstname . "', lastname = '".$lastname."', username = '".$username."', 
		email = '".$email."', role = '".$role."' WHERE id =" . $id;
		$this->db->query($update);	
	}

	public function setPassword($id, $password)
	{
		$update = "UPDATE users SET password ='" . $password . "' WHERE id =" . $id;
		$this->db->query($update);	
	}

}