<?php

class log{

	private $db ;

	public function __construct()
	{
		$server_name = 'HOST-STORE';
		$this->db = new PDO( "sqlsrv:server=".$server_name." ; Database = reports", "sa", "BRd@t@123");
	}

	public function getLogs()
	{
		$SQL = "SELECT * FROM logs WHERE application = 1 ";
		$result = $this->db->query($SQL);
		return $result->fetch(PDO::FETCH_BOTH);
	}

	public function saveLog($date, $application, $action)
	{
		$insert = $this->db->prepare("INSERT INTO logs (date, application, action)
	    VALUES (:date, :application, :action)");

	    $insert->bindParam(':date', $date);
	    $insert->bindParam(':application', $application);
	    $insert->bindParam(':action', $action);

	    $insert->execute();
	}

	public function deleteLog($id)
	{
		$delete = "DELETE FROM logs WHERE id = '" . $id . "'";
		$this->db->query($delete);		
	}
}