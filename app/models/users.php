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
		$SQL = "SELECT * FROM users ORDER BY lastname";
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

	public function setUser($user)
	{
		$insert = $this->db->prepare("INSERT INTO users (firstname, lastname, username, password, email, role)
	    VALUES (:firstname, :lastname, :username, :password, :email, :role)");

	    $insert->bindParam(':firstname', $user['firstname']);
	    $insert->bindParam(':lastname', $user['lastname']);
	    $insert->bindParam(':username', $user['username']);
	    $insert->bindParam(':password', $user['password']);
	    $insert->bindParam(':email', $user['email']);
	    $insert->bindParam(':role', $user['role']);

	    $insert->execute();
	}

	public function deleteUser($id)
	{
		$delete = "DELETE FROM users WHERE id = '" . $id . "'";
		$this->db->query($delete);		
	}

	public function updateUser($field, $value, $id)
	{
		
	}

	public function setPassword($id, $password)
	{
		$update = "UPDATE users SET password ='" . $password . "' WHERE id =" . $id;
		$this->db->query($update);	
	}

}