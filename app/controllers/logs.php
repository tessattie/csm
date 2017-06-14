<?php
class logs extends Controller{
	
	private $logs;

	public function __construct()
	{
		$this->logs = $this->model('log');
	} 


	public function saveLog(){
		$this->logs->saveLog($date, 1, $action);
	}

}