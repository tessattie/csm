<?php
session_start();
class home extends Controller{

	private $brdata;
	
	private $today;
	
	private $from;
	
	private $to;

	private $exportURL;

	private $queryTitles;

	private $classname;

	private $fileArianne;

	public function __construct()
	{
		parent::__construct();
		$this->today = date('Y-m-d', strtotime("-1 days"));
		if(!isset($_COOKIE["from"]))
		{
			setCookie("from", date('Y-m-d', strtotime("-1 week")));
			$_COOKIE["from"] = date('Y-m-d', strtotime("-1 week"));
		}
		else
		{
			$this->from = $_COOKIE["from"];
		}
		if(!isset($_COOKIE["to"]))
		{
			setCookie("to", date('Y-m-d'));
			$_COOKIE["to"] = date('Y-m-d');
		}
		else
		{
			$this->to = $_COOKIE["to"];
		}
		$this->classname = "thereport";
		if(empty($_SESSION['csm']['keyword'])){
			$_SESSION['csm']['keyword'] = '';
		}
		$this->exportURL = "javascript: void(0)";
		$this->brdata = $this->model('brdata');
		$this->fileArianne = "HOME";
	} 

	public function index()
	{
		if(!isset($_COOKIE["from"]))
		{
			setCookie("from", date('Y-m-d', strtotime("-1 week")));
			$_COOKIE["from"] = date('Y-m-d', strtotime("-1 week"));
		}
		else
		{
			$this->from = $_COOKIE["from"];
		}
		if(!isset($_COOKIE["to"]))
		{
			setCookie("to", date('Y-m-d'));
			$_COOKIE["to"] = date('Y-m-d');
		}
		else
		{
			$this->to = $_COOKIE["to"];
		}
		$data = array("exportURL" => $this->exportURL, "from" => $this->from, "to" => $this->to, "action" => "index", "menu" => $this->userRole, "title" => $this->fileArianne);
		$this->view('home', $data);
	}

	public function setKeyword(){
		if(!empty($_POST['key'])){
			$_SESSION['csm']['keyword'] = $_POST['key'];
		}else{
			$_SESSION['csm']['keyword'] = '';
		}
		echo $_SESSION['csm']['keyword']; die();
	}

	public function vendor()
	{
		$data = array();
		$title = "";
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST REC", "LAST REC DATE", "SALES", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", "sales", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['vendorNumber']))
		{
			// print_r($_POST); print_r($_SESSION); die();
			if($_SESSION['csm']['role'] == 10){
				$condition = false;
				for($i=0;$i<count($_SESSION['csm']['vendors']);$i++){
					if($_SESSION['csm']['vendors'][$i] == $_POST['vendorNumber']){
						$condition = true;
					}
				}
				if($condition == false){
					header('Location:/csm/public/home');
				}
			}
			$_POST['vendorNumber'] = $this->completeValue($_POST['vendorNumber'], 6);
			$this->setDefaultDates($_POST['fromvendor'], $_POST['tovendor']);
			$this->exportURL = "/csm/public/phpExcelExport/vendor/" .$_POST['vendorNumber']. "/" . $this->from . "/" . $this->to;
			$vendorReport = $this->brdata->get_vendorReport($_POST['vendorNumber'], $this->today, $_POST['fromvendor'], $_POST['tovendor']);
			// var_dump($vendorReport);die();
			if(!empty($vendorReport[0]))
			{
				$title = '[VDR' . $_POST["vendorNumber"] . ' - '. $vendorReport[0]["VdrName"] . '] - [' . $this->from . ' to ' . $this->to . '] - [' . count($vendorReport) . ' ITEMS]';				
			}
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles , "title" => $title, "tableID" => "report_result", "action" => "vendor", "reportType" => 'vendorTemplate', "from" => $this->from, "to" => $this->to, "report" => $vendorReport, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function limitedVendor(){
		$data = array();
		$title = "";
		$theadTitles = array("UPC", "ITEM #", "ITEM DESCRIPTION", "PK", "SIZE",
			"RETAIL", "ON-HAND");
		$queryTitles = array("UPC", "CertCode", "ItemDescription", "Pack", "SizeAlpha",
			"Retail", "onhand");
		if(!empty($_POST['limitedVendorNo']))
		{
			$_POST['limitedVendorNo'] = $this->completeValue($_POST['limitedVendorNo'], 6);
			$this->setDefaultDates($_POST['fromlimitedVendorNo'], $_POST['tolimitedVendorNo']);
			$this->exportURL = "/csm/public/phpExcelExport/limitedVendor/" .$_POST['limitedVendorNo']. "/" . $this->from . "/" . $this->to;
			$vendorReport = $this->brdata->get_vendorReport($_POST['limitedVendorNo'], $this->today, $_POST['fromlimitedVendorNo'], $_POST['tolimitedVendorNo']);
			// var_dump($vendorReport);die();
			if(!empty($vendorReport[0]))
			{
				$title = '[VDR' . $_POST["limitedVendorNo"] . ' - '. $vendorReport[0]["VdrName"] . '] - [' . $this->from . ' to ' . $this->to . '] - [' . count($vendorReport) . ' ITEMS]';				
			}
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles , "title" => $title, "tableID" => "report_result", "action" => "vendor", "reportType" => 'defaultTemplate', "from" => $this->from, "to" => $this->to, "report" => $vendorReport, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function multipleSections()
	{
		$data = array();
		$title = "";
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PACK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST REC", "LAST REC DATE", 
			"SALES", "VDR #", "VDR NAME", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", 
			"sales", "VdrNo", "VdrName", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['mulsectionNumber'])){
			$exporturl = "";
			$sections = explode(",",$_POST['mulsectionNumber']);
			for($i=0;$i<count($sections);$i++){
				$sections[$i] = $this->completeValue($sections[$i], 4);
				if($exporturl == ""){
					$exporturl .=  $sections[$i];
				}else{
					$exporturl .= "_" . $sections[$i];
				}
				
			}
			$this->setDefaultDates($_POST['mulfromsection'], $_POST['multosection']);
			$this->exportURL = "/csm/public/phpExcelExport/multipleSections/".$exporturl . "/" . $this->from . "/" . $this->to;
			$sectionReport = $this->brdata->get_multipleSectionReport($sections, $this->today, $_POST['mulfromsection'], $_POST['multosection']);
			if(!empty($sectionReport[0]))
			{
				$title = '[ DPT'.$sectionReport[0]['DptNo'].' - '.$sectionReport[0]['DptName'].' ] - [ '.$exporturl.' ] - [ '.$this->from.' to '.$this->to.' ] - 
				[ '.count($sectionReport).' ITEMS ]';
			}
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles, 
				"title" => $title, "tableID" => "report_result", "action" => "section", "reportType" => 'templateWithSectionOrder', 
				"from" => $this->from, "to" => $this->to, "report" => $sectionReport, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function multipleSectionsNeg()
	{
		$data = array();
		$title = "";
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PACK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST REC", "LAST REC DATE", 
			"SALES", "VDR #", "VDR NAME", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", 
			"sales", "VdrNo", "VdrName", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['mulsectionNumberneg'])){
			$exporturl = "";
			$sections = explode(",",$_POST['mulsectionNumberneg']);
			for($i=0;$i<count($sections);$i++){
				$sections[$i] = $this->completeValue($sections[$i], 4);
				if($exporturl == ""){
					$exporturl .=  $sections[$i];
				}else{
					$exporturl .= "_" . $sections[$i];
				}
				
			}
			$this->setDefaultDates($_POST['mulfromsectionneg'], $_POST['multosectionneg']);
			$this->exportURL = "/csm/public/phpExcelExport/multipleSectionsNeg/".$exporturl . "/" . $this->from . "/" . $this->to;
			$sectionReport = $this->brdata->get_multipleSectionNegReport($sections, $this->today, $_POST['mulfromsectionneg'], $_POST['multosectionneg']);
			if(!empty($sectionReport[0]))
			{
				$title = '[ DPT'.$sectionReport[0]['DptNo'].' - '.$sectionReport[0]['DptName'].'] - ['.$this->from.' to '.$this->to.'] - 
				['.count($sectionReport).' ITEMS ]';
			}
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles, 
						  "title" => $title, "tableID" => "report_result", "action" => "section", "reportType" => 'templateWithSectionOrderNegativeRepeat', 
						  "from" => $this->from, "to" => $this->to, "report" => $sectionReport, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function completeValue($val, $length){
		$total = $length;
		$value = '';
		$amount = strlen($val);
		$toadd = $total - (int)$amount;
		for($i=0;$i<$toadd;$i++){
			$value .= "0";
		}
		return $value.$val;
	}


	public function UPCReceivingHistory()
	{
		$data = array();
		$title = "";
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PACK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST REC", "LAST REC DATE", 
			"SALES", "TPR PRICE", "TPR START DATE", "TPR END DATE", "VDR NO", "VDR NAME");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", 
			"sales", "tpr", "tprStart", "tprEnd", "VdrNo", "VdrName");
		if(!empty($_POST['upcReceivingNumber']))
		{
			$_POST['upcReceivingNumber'] = $this->completeValue($_POST['upcReceivingNumber'], 15);
			$this->setDefaultDates($_POST['fromReceivingupc'], $_POST['toReceivingupc']);
			$this->exportURL = "/csm/public/phpExcelExport/UPCReceivingHistory/".$_POST['upcReceivingNumber'] . "/" . $this->from . "/" . $this->to;
			$receivingHistory = $this->brdata->get_upcReceivingHistory($_POST['upcReceivingNumber'], $this->today, $_POST['toReceivingupc'], $_POST['fromReceivingupc']);
			// var_dump($receivingHistory);
			if(!empty($receivingHistory[0]))
			{
				$title = '[UPC : '.$_POST['upcReceivingNumber'].'] - ['.$this->from.' to '.$this->to.'] - ['.count($receivingHistory).' ITEMS]';
			}
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles, 
				"title" => $title, "tableID" => "report_result", "action" => "vendorSection", "reportType" => 'defaultTemplate', 
				"from" => $this->from, "to" => $this->to, "report" => $receivingHistory, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function vendorNegative()
	{
		$data = array();
		$title = "";
		$report = null;
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST REC", "LAST REC DATE", "SALES", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", "sales", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['vendorNegNumber']))
		{
			$_POST['vendorNegNumber'] = $this->completeValue($_POST['vendorNegNumber'], 6);
			$this->setDefaultDates($_POST['fromNegvendor'], $_POST['toNegvendor']);
			$this->exportURL = "/csm/public/phpExcelExport/vendorNegative/".$_POST['vendorNegNumber'] . "/" . $this->from . "/" . $this->to;
			$report = $this->brdata->get_vendorReport($_POST['vendorNegNumber'], $this->today, $_POST['fromNegvendor'], $_POST['toNegvendor']);
			if(!empty($report[0]))
			{
				$title = '[VDR' . $_POST["vendorNegNumber"] . ' - '. $report[0]["VdrName"] . '] - [' . $this->from . ' to ' . $this->to . ']';				
			}
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles , "title" => $title, "tableID" => "report_result", "action" => "vendor", "reportType" => 'templateWithSectionOrderNegative', "from" => $this->from, "to" => $this->to, "report" => $report, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function sectionMovement()
	{
		$data = array();
		$title = "";
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PACK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST REC", "LAST REC DATE", 
			"SALES", "VDR #", "VDR NAME", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", 
			"sales", "VdrNo", "VdrName", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['sectionMvtNumber']))
		{
			$_POST['sectionMvtNumber'] = $this->completeValue($_POST['sectionMvtNumber'], 4);
			$this->setDefaultDates($_POST['fromMvtsection'], $_POST['toMvtsection']);
			$this->exportURL = "/csm/public/phpExcelExport/sectionMovement/".$_POST['sectionMvtNumber'] . "/" . $this->from . "/" . $this->to;
			$sectionReport = $this->brdata->get_sectionReport($_POST['sectionMvtNumber'], $this->today, $_POST['fromMvtsection'], $_POST['toMvtsection']);
			$report = array();
			$j=0;
			$i=0;
			// var_dump($sectionReport);
			foreach($sectionReport as $key => $value)
			{
				if(!empty($value['sales']) || $value['onhand'] == ".0000")
				{
					unset($sectionReport[$i]);
				}
				$i = $i + 1;
			}
			foreach($sectionReport as $key => $value)
			{
				$report[$j] = $value;
				$j = $j + 1;
			}
			if(!empty($report[0]))
			{
				$title = '[DPT'.$report[0]['DptNo'].' - '.$report[0]['DptName'].'] - [SCT'.$_POST['sectionMvtNumber'].' - '.$report[0]['SctName'].'] - ['.$this->from.' to '.$this->to.'] - 
				['.count($report).' ITEMS]';
			}
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles, 
				"title" => $title, "tableID" => "report_result", "action" => "section", "reportType" => 'defaultTemplate', 
				"from" => $this->from, "to" => $this->to, "report" => $report, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function specials()
	{
		$data = array();
		$title = "TPR REPORT";
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PK", "SIZE",
			"CASE COST", "UNIT PRICE", "RETAIL", "ON-HAND", "LAST REC", "LAST REC DATE", "SALES", "TPR PRICE", "TPR START DATE", "TPR END DATE", "VDR NO", "VDR NAME");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "unitPrice", "onhand", "lastReceiving", "lastReceivingDate", "sales", "tpr", "tprStart", "tprEnd", "VdrNo", "VdrName");
		$this->exportURL = "/csm/public/phpExcelExport/specials/" . $this->from . "/" . $this->to;
		$specialReport = $this->brdata->get_specialReport($this->today, $this->from, $this->to);
		$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles , "title" => $title, "tableID" => "report_result", "action" => "special", "reportType" => 'templateWithSectionOrder', "from" => $this->from, "to" => $this->to, "report" => $specialReport, "menu" => $this->userRole);
		$this->renderView($data);
	}

	public function vendorMovement()
	{
		$data = array();
		$title = "";
		$report = null;
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST REC", "LAST REC DATE", "SALES", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", "sales", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['vendorMvtNumber']))
		{
			$_POST['vendorMvtNumber'] = $this->completeValue($_POST['vendorMvtNumber'], 6);
			$this->setDefaultDates($_POST['fromMvtvendor'], $_POST['toMvtvendor']);
			$this->exportURL = "/csm/public/phpExcelExport/vendorMovement/".$_POST['vendorMvtNumber'] . "/" . $this->from . "/" . $this->to;
			$vendorReport = $this->brdata->get_vendorReport($_POST['vendorMvtNumber'], $this->today, $_POST['fromMvtvendor'], $_POST['toMvtvendor']);
			$j=0;
			$i=0;
			// var_dump($vendorReport); die();
			foreach($vendorReport as $key => $value)
			{
				if(!empty($value['sales']) || $value['onhand'] == ".0000")
				{
					unset($vendorReport[$i]);
				}
				$i = $i + 1;
			}
			foreach($vendorReport as $key => $value)
			{
				$report[$j] = $value;
				$j = $j + 1;
			}
			if(!empty($report[0]))
			{
				$title = '[VDR' . $_POST["vendorMvtNumber"] . ' - '. $report[0]["VdrName"] . '] - [' . $this->from . ' to ' . $this->to . '] - [' . count($report) . ' ITEMS]';				
			}
			// var_dump($report); die();
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles , "title" => $title, "tableID" => "report_result", "action" => "vendor", "reportType" => 'templateWithSectionOrder', "from" => $this->from, "to" => $this->to, "report" => $report, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function departmentMovement()
	{
		$data = array();
		$title = "";
		$report = null;
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST REC", "LAST REC DATE", "SALES", "TPR PRICE", "TPR START DATE", "TPR END DATE", "VDR NO", "VDR NAME");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", "sales", "tpr", "tprStart", "tprEnd", 'VdrNo', 'VdrName');
		if(!empty($_POST['dptMvtNumber']))
		{
			$_POST['vendorMvtNumber'] = $this->completeValue($_POST['dptMvtNumber'], 6);
			$this->setDefaultDates($_POST['fromDptvendor'], $_POST['toDptvendor']);
			$this->exportURL = "/csm/public/phpExcelExport/departmentMovement/".$_POST['dptMvtNumber'] . "/" . $this->from . "/" . $this->to;
			$dptReport = $this->brdata->get_departmentReport($_POST['dptMvtNumber'], $this->today, $_POST['fromDptvendor'], $_POST['toDptvendor']);
			$j=0;
			$i=0;
			// var_dump($dptReport); die();
			foreach($dptReport as $key => $value)
			{
				if(!empty($value['lastReceivingDate'])){
						unset($dptReport[$i]);
					}
				if(!empty($value['sales']) || ($value['onhand'] != ".0000" && $value['onhand'] != "99999"))
				{
					unset($dptReport[$i]);
				}
				$i = $i + 1;
			}
			foreach($dptReport as $key => $value)
			{
				$report[$j] = $value;
				$j = $j + 1;
			}
			if(!empty($report[0]))
			{
				$title = '[DPT' . $_POST["dptMvtNumber"] . ' - '. $report[0]["DptName"] . '] - [' . $this->from . ' to ' . $this->to . '] - [' . count($report) . ' ITEMS]';				
			}
			// var_dump($report); die();
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles , "title" => $title, "tableID" => "report_result", "action" => "vendor", "reportType" => 'templateWithSectionOrder', "from" => $this->from, "to" => $this->to, "report" => $report, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function vendorSectionMovement()
	{
		$data = array();
		$title = "";
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PACK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST REC", "LAST REC DATE", 
			"SALES", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", 
			"sales", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['svendorMvtNumber']) && !empty($_POST['sctvendorMvtNumber']))
		{
			$report = array();
			$_POST['svendorMvtNumber'] = $this->completeValue($_POST['svendorMvtNumber'], 6);
			$_POST['sctvendorMvtNumber'] = $this->completeValue($_POST['sctvendorMvtNumber'], 4);
			$this->setDefaultDates($_POST['fromvendorMvtSection'], $_POST['tovendorMvtSection']);
			$this->exportURL = "/csm/public/phpExcelExport/vendorSectionMovement/".$_POST['svendorMvtNumber'] . "/" . $_POST['sctvendorMvtNumber'] . "/" . $this->from . "/" . $this->to;
			$vdrSctReport = $this->brdata->get_vendorSectionReport($_POST['svendorMvtNumber'], $_POST['sctvendorMvtNumber'], $this->today, $_POST['tovendorMvtSection'], $_POST['fromvendorMvtSection']);
			$j=0;
			$i=0;
			// var_dump($)
			foreach($vdrSctReport as $key => $value)
			{
				if(!empty($value['sales']) || $value['onhand'] == ".0000")
				{
					unset($vdrSctReport[$i]);
				}
				$i = $i + 1;
			}
			foreach($vdrSctReport as $key => $value)
			{
				$report[$j] = $value;
				$j = $j + 1;
			}
			if(!empty($report[0]))
			{
				$title = '[DPT'.$report[0]['DptNo'].' - '.$report[0]['DptName'].'] - [VDR'.$_POST['svendorMvtNumber'].' - '.$report[0]['VdrName'].'] - 
				[SCT'.$_POST['sctvendorMvtNumber'].' - '.$report[0]['SctName'].'] - ['.$this->from.' to '.$this->to.']  - ['.count($report).' ITEMS]';
			}
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles, 
				"title" => $title, "tableID" => "report_result", "action" => "vendorSection", "reportType" => 'defaultTemplate', 
				"from" => $this->from, "to" => $this->to, "report" => $report, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function vendor_url($vendor)
	{
		$data = array();
		$title = "";
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST REC", "LAST REC DATE", "SALES", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", "sales", "tpr", "tprStart", "tprEnd");
		if(!empty($vendor))
		{
			$this->exportURL = "/csm/public/phpExcelExport/vendor/".$vendor . "/" . $this->from . "/" . $this->to;
			$vendorReport = $this->brdata->get_vendorReport($vendor, $this->today, $this->from, $this->to);
			if(!empty($vendorReport[0]))
			{
				$title = '[VDR' . $vendor . ' - '. $vendorReport[0]["VdrName"] . '] - [' . $this->from . ' to ' . $this->to . '] - [' . count($vendorReport) . ' ITEMS]';				
			}
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles , "title" => $title, "tableID" => "report_result", "action" => "vendor", "reportType" => 'templateWithSectionOrder', "from" => $this->from, "to" => $this->to, "report" => $vendorReport, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function vendorPriceCompare()
	{
		$data = array();
		$title = "Vendor price compare";
		$theadTitles = array("UPC", "BRAND", "ITEM DESCRIPTION", "ON-HAND", "SALES", "TPR PRICE", "TPR START DATE", 
			"TPR END DATE", "PACK", "SIZE", "CASE COST", "UNIT PRICE", "RETAIL", "ITEM #", "LAST REC", "LAST REC DATE", "VDR NO", "VDR NAME");
		$queryTitles = array("UPC", "Brand", "ItemDescription", "onhand", "sales", "tpr", "tprStart", "tprEnd", 
			array("PackOne", "SizeAlphaOne", "CaseCostOne", "unitPriceOne", "RetailOne", "CertCodeOne", "lastReceivingOne", "lastReceivingDateOne", "VdrNoOne", "VdrNameOne"), 
			array("PackTwo", "SizeAlphaTwo", "CaseCostTwo","unitPriceTwo", "RetailTwo", "CertCodeTwo", "lastReceivingTwo", "lastReceivingDateTwo", "VdrNoTwo", "VdrNameTwo"));
		if(!empty($_POST['vendor1']) && !empty($_POST['vendor2']))
		{
			$_POST['vendor1'] = $this->completeValue($_POST['vendor1'], 6);
			$_POST['vendor2'] = $this->completeValue($_POST['vendor2'], 6);
			$this->exportURL = "/csm/public/phpExcelExport/vendorPriceCompare/".$_POST['vendor1'] . "/" . $_POST['vendor2'] . "/" . $this->from . "/" . $this->to;
			$this->setDefaultDates($_POST['fromPriceCompare'], $_POST['toPriceCompare']);
			$vendorReport = $this->brdata->vendorPriceCompare($_POST['vendor1'], $_POST['vendor2'], $this->today, $this->from, $this->to);
			if(!empty($vendorReport[0]))
			{
				$title = '[VENDOR PRICE COMPARE : '.$vendorReport[0]['VdrNameOne'].' - '.$vendorReport[0]['VdrNameTwo'].'] - ['.count($vendorReport).' ITEMS]';
			}
		}
		$this->view('home', array("qt" => $queryTitles, "thead" => $theadTitles , "title" => $title, 'active' => "vendorPriceCompare", 
			"class" => $this->classname, "exportURL" => $this->exportURL, "tableID" => "report_results", "action" => "priceCompare", 
			"reportType" => 'templateCompare',"from" => $this->from, "to" => $this->to, "report" => $vendorReport, "menu" => $this->userRole));
	}

	public function sectionPriceCompare()
	{
		$data = array();
		$title = "Section price compare";
		$theadTitles = array("UPC", "BRAND", "ITEM DESCRIPTION", "ON-HAND", "SALES", "TPR PRICE", "TPR START DATE", 
			"TPR END DATE", "PACK", "SIZE", "CASE COST", "UNIT PRICE", "RETAIL", "ITEM #", "LAST REC", "LAST REC DATE", "VDR NO", "VDR NAME");
		$queryTitles = array("UPC", "Brand", "ItemDescription", "onhand", "sales", "tpr", "tprStart", "tprEnd", 
			array("PackOne", "SizeAlphaOne", "CaseCostOne", "unitPriceOne", "RetailOne", "CertCodeOne", "lastReceivingOne", "lastReceivingDateOne", "VdrNoOne", "VdrNameOne"), 
			array("PackTwo", "SizeAlphaTwo", "CaseCostTwo", "unitPriceTwo", "RetailTwo", "CertCodeTwo", "lastReceivingTwo", "lastReceivingDateTwo", "VdrNoTwo", "VdrNameTwo"));
		if(!empty($_POST['vendor1Section']) && !empty($_POST['vendor2Section']) && !empty($_POST['sectionCompare']))
		{
			$_POST['vendor1Section'] = $this->completeValue($_POST['vendor1Section'], 6);
			$_POST['vendor2Section'] = $this->completeValue($_POST['vendor2Section'], 6);
			$_POST['sectionCompare'] = $this->completeValue($_POST['sectionCompare'], 4);
			$this->exportURL = "/csm/public/phpExcelExport/sectionPriceCompare/".$_POST['vendor1Section'] . "/" . $_POST['vendor2Section'] . "/"  . $_POST['sectionCompare'] . "/" . $this->from . "/" . $this->to;
			$this->setDefaultDates($_POST['fromSectionCompare'], $_POST['toSectionCompare']);
			$sectionReport = $this->brdata->sectionPriceCompare($_POST['vendor1Section'], $_POST['vendor2Section'], $_POST['sectionCompare'], $this->today, $this->from, $this->to);
			if(!empty($sectionReport[0]))
			{
				$title = '[VENDOR SECTION PRICE COMPARE : '.$sectionReport[0]['VdrNameOne'].' - '.$sectionReport[0]['VdrNameTwo'].'] - [SECTION ' . $sectionReport[0]['SctName'] . '] - ['.count($sectionReport).' ITEMS]';
			}
		}
		$this->view('home', array("qt" => $queryTitles, "thead" => $theadTitles , "title" => $title, 'active' => "sectionPriceCompare", 
			"class" => $this->classname, "exportURL" => $this->exportURL, "tableID" => "report_results", "action" => "sectionCompare", 
			"reportType" => 'templateCompare',"from" => $this->from, "to" => $this->to, "report" => $sectionReport, "menu" => $this->userRole));
	}

	public function UPCRange()
	{
		$data = array();
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PACK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST REC", "LAST REC DATE", "SALES", 
			"VDR #", "VDR NAME", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", 
			"sales", "VdrNo", "VdrName", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['upcRangeNo1']) && !empty($_POST['upcRangeNo2']))
		{
			$_POST['upcRangeNo1'] = $this->completeValue($_POST['upcRangeNo1'], 15);
			$_POST['upcRangeNo2'] = $this->completeValue($_POST['upcRangeNo2'], 15);
			$this->setDefaultDates($_POST['fromupcRange'], $_POST['toupcRange']);
			$this->exportURL = "/csm/public/phpExcelExport/UPCRange/".$_POST['upcRangeNo1'] . "/" . $_POST['upcRangeNo2'] . "/" . $this->from . "/" . $this->to;
			$upcRangeReport = $this->brdata->get_upcRangeReport($_POST['upcRangeNo1'], $_POST['upcRangeNo2'], $this->today, $_POST['toupcRange'], $_POST['fromupcRange']);
			$title = '[UPC RANGE : '.$_POST['upcRangeNo1'].' / '.$_POST['upcRangeNo2'].'] - ['.$this->from.' to '.$this->to.'] - ['.count($upcRangeReport).' ITEMS]';
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles , 
				"title" => $title, "tableID" => "report_result", "action" => "UPCRange", "reportType" => 'templateWithSectionOrder', 
				"from" => $this->from, "to" => $this->to, "report" => $upcRangeReport, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function itemDescription()
	{
		$data = array();
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PACK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST REC", "LAST REC DATE", 
			"SALES", "VDR #", "VDR NAME", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", 
			"sales", "VdrNo", "VdrName", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['itemDescription']))
		{
			$this->setDefaultDates($_POST['descriptionfrom'], $_POST['descriptionto']);
			$this->exportURL = "/csm/public/phpExcelExport/itemDescription/".str_replace(" ", "_", $_POST['itemDescription']) . "/" . $this->from . "/" . $this->to;
			$description = $this->brdata->get_itemDescription($_POST['itemDescription'], $this->today, $_POST['descriptionto'], $_POST['descriptionfrom']);
			$title = '[ITEM : '.$_POST['itemDescription'].'] - ['.$this->from.' to '.$this->to.'] - ['.count($description).' ITEMS]';
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles, 
				"title" => $title, "tableID" => "report_result", "action" => "itemDescription", "reportType" => 'templateWithSectionOrder', 
				"from" => $from2 = $this->from, "to" => $this->to, "report" => $description, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function vendorSection()
	{
		$data = array();
		$title = "";
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PACK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST REC", "LAST REC DATE", 
			"SALES", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", 
			"sales", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['svendorNumber']) && !empty($_POST['sctvendorNumber']))
		{
			$_POST['svendorNumber'] = $this->completeValue($_POST['svendorNumber'], 6);
			$_POST['sctvendorNumber'] = $this->completeValue($_POST['sctvendorNumber'], 4);
			$this->setDefaultDates($_POST['fromvendorSection'], $_POST['tovendorSection']);
			$this->exportURL = "/csm/public/phpExcelExport/vendorSection/".$_POST['svendorNumber'] . "/" . $_POST['sctvendorNumber'] . "/" . $this->from . "/" . $this->to;
			$vdrSctReport = $this->brdata->get_vendorSectionReport($_POST['svendorNumber'], $_POST['sctvendorNumber'], $this->today, $_POST['tovendorSection'], $_POST['fromvendorSection']);
			if(!empty($vdrSctReport[0]))
			{
				$title = '[DPT'.$vdrSctReport[0]['DptNo'].' - '.$vdrSctReport[0]['DptName'].'] - [VDR'.$_POST['svendorNumber'].' - '.$vdrSctReport[0]['VdrName'].'] - 
				[SCT'.$_POST['sctvendorNumber'].' - '.$vdrSctReport[0]['SctName'].'] - ['.$this->from.' to '.$this->to.']  - ['.count($vdrSctReport).' ITEMS]';
			}
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles, 
				"title" => $title, "tableID" => "report_result", "action" => "vendorSection", "reportType" => 'defaultTemplate', 
				"from" => $this->from, "to" => $this->to, "report" => $vdrSctReport, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function vendorSectionNegative()
	{
		$data = array();
		$report = array();
		$title = "";
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PACK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST REC", "LAST REC DATE", 
			"SALES", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", 
			"sales", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['svendorNegNumber']) && !empty($_POST['sctvendorNegNumber']))
		{
			$_POST['svendorNegNumber'] = $this->completeValue($_POST['svendorNegNumber'], 6);
			$_POST['sctvendorNegNumber'] = $this->completeValue($_POST['sctvendorNegNumber'], 4);
			$this->setDefaultDates($_POST['fromvendorNegSection'], $_POST['tovendorNegSection']);
			$this->exportURL = "/csm/public/phpExcelExport/vendorSectionNegative/".$_POST['svendorNegNumber'] . "/" . $_POST['sctvendorNegNumber'] . "/" . $this->from . "/" . $this->to;
			$report = $this->brdata->get_vendorSectionReport($_POST['svendorNegNumber'], $_POST['sctvendorNegNumber'], $this->today, $_POST['tovendorNegSection'], $_POST['fromvendorNegSection']);
			if(!empty($report[0]))
			{
				$title = '[DPT'.$report[0]['DptNo'].' - '.$report[0]['DptName'].'] - [VDR'.$_POST['svendorNegNumber'].' - '.$report[0]['VdrName'].'] - 
				[SCT'.$_POST['sctvendorNegNumber'].' - '.$report[0]['SctName'].'] - ['.$this->from.' to '.$this->to.']  - ['.count($report).' ITEMS]';
			}
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles, 
				"title" => $title, "tableID" => "report_result", "action" => "vendorSection", "reportType" => 'defaultTemplateNegative', 
				"from" => $this->from, "to" => $this->to, "report" => $report, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function section()
	{
		$data = array();
		$title = "";
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PACK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST REC", "LAST REC DATE", 
			"SALES", "VDR #", "VDR NAME", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", 
			"sales", "VdrNo", "VdrName", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['sectionNumber']))
		{
			$_POST['sectionNumber'] = $this->completeValue($_POST['sectionNumber'], 4);
			$this->setDefaultDates($_POST['fromsection'], $_POST['tosection']);
			$this->exportURL = "/csm/public/phpExcelExport/section/".$_POST['sectionNumber'] . "/" . $this->from . "/" . $this->to;
			$sectionReport = $this->brdata->get_sectionReport($_POST['sectionNumber'], $this->today, $_POST['fromsection'], $_POST['tosection']);
			if(!empty($sectionReport[0]))
			{
				$title = '[DPT'.$sectionReport[0]['DptNo'].' - '.$sectionReport[0]['DptName'].'] - [SCT'.$_POST['sectionNumber'].' - '.$sectionReport[0]['SctName'].'] - ['.$this->from.' to '.$this->to.'] - 
				['.count($sectionReport).' ITEMS]';
			}
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles, 
				"title" => $title, "tableID" => "report_result", "action" => "section", "reportType" => 'defaultTemplate', 
				"from" => $this->from, "to" => $this->to, "report" => $sectionReport, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function sectionNegative()
	{
		$data = array();
		$title = "";
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PACK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST REC", "LAST REC DATE", 
			"SALES", "VDR #", "VDR NAME", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", 
			"sales", "VdrNo", "VdrName", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['sectionNegNumber']))
		{
			$_POST['sectionNegNumber'] = $this->completeValue($_POST['sectionNegNumber'], 4);
			$this->setDefaultDates($_POST['fromNegsection'], $_POST['toNegsection']);
			$this->exportURL = "/csm/public/phpExcelExport/sectionNegative/".$_POST['sectionNegNumber'] . "/" . $this->from . "/" . $this->to;
			$sectionReport = $this->brdata->get_sectionNegReport($_POST['sectionNegNumber'], $this->today, $_POST['fromNegsection'], $_POST['toNegsection']);
			if(!empty($sectionReport[0]))
			{
				$title = '[DPT'.$sectionReport[0]['DptNo'].' - '.$sectionReport[0]['DptName'].'] - [SCT'.$_POST['sectionNegNumber'].' - '.$sectionReport[0]['SctName'].'] - ['.$this->from.' to '.$this->to.']
				';
			}
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles, 
				"title" => $title, "tableID" => "report_result", "action" => "sectionNegative", "reportType" => 'defaultTemplateNegative', 
				"from" => $this->from, "to" => $this->to, "report" => $sectionReport, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function department()
	{
		$data = array();
		$title = "";
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PACK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST REC", "LAST REC DATE", "SALES", 
			"VDR #", "VDR NAME", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", "sales", 
			"VdrNo", "VdrName", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['departmentNumber']))
		{
			$_POST['departmentNumber'] = $this->completeValue($_POST['departmentNumber'], 2);
			$this->setDefaultDates($_POST['fromdepartment'], $_POST['todepartment']);
			$this->exportURL = "/csm/public/phpExcelExport/department/".$_POST['departmentNumber'] . "/" . $this->from . "/" . $this->to;
			$departmentReport = $this->brdata->get_departmentReport($_POST['departmentNumber'], $this->today, $_POST['fromdepartment'], $_POST['todepartment']);
			if(!empty($departmentReport[0]))
			{
				$title = '[DPT'.$_POST['departmentNumber'].'] - ['.$departmentReport[0]['DptName'].'] - ['.$this->from.' to '.$this->to.'] - ['.count($departmentReport).' ITEMS]';				
			}
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles, 
				"title" => $title, "tableID" => "report_result", "action" => "department", "reportType" => 'templateWithSectionOrder', 
				"from" => $this->from, "to" => $this->to, "report" => $departmentReport, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function vendorDepartment()
	{
		$data = array();
		$title = "";
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PACK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST REC", "LAST REC DATE", 
			"SALES", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", 
			"sales", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['dvendorNumber']) && !empty($_POST['dptvendorNumber']))
		{
			$_POST['dvendorNumber'] = $this->completeValue($_POST['dvendorNumber'], 6);
			$_POST['dptvendorNumber'] = $this->completeValue($_POST['dptvendorNumber'], 2);
			$this->setDefaultDates($_POST['fromvendorDpt'], $_POST['tovendorDpt']);
			$this->exportURL = "/csm/public/phpExcelExport/vendorDepartment/".$_POST['dvendorNumber'] . "/" . $_POST['dptvendorNumber'] . "/" . $this->from . "/" . $this->to;
			$vdrDptReport = $this->brdata->get_vendorDepartmentReport($_POST['dvendorNumber'], $_POST['dptvendorNumber'], $this->today, $_POST['fromvendorDpt'], $_POST['tovendorDpt']);
			if(!empty($vdrDptReport[0]))
			{
				$title = '[VDR'.$_POST['dvendorNumber'].' - '.$vdrDptReport[0]['VdrName'].'] - [DPT'.$_POST['dptvendorNumber'].' - '.$vdrDptReport[0]['DptName'].'] - ['.$this->from.' to '.$this->to.'] - ['.count($vdrDptReport).' ITEMS]';				
			}
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles, 
				"title" => $title, "tableID" => "report_result", "action" => "vendorDepartment", "reportType" => 'templateWithSectionOrder', 
				"from" => $this->from, "to" => $this->to, "report" => $vdrDptReport, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function vendorDepartmentNegative()
	{
		$data = array();
		$title = "";
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PACK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST REC", "LAST REC DATE", 
			"SALES", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", 
			"sales", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['dvendorNumberNeg']) && !empty($_POST['dptvendorNumberNeg']))
		{
			$_POST['dvendorNumberNeg'] = $this->completeValue($_POST['dvendorNumberNeg'], 6);
			$_POST['dptvendorNumberNeg'] = $this->completeValue($_POST['dptvendorNumberNeg'], 2);
			$this->setDefaultDates($_POST['fromvendorDptNeg'], $_POST['tovendorDptNeg']);
			$this->exportURL = "/csm/public/phpExcelExport/vendorDepartmentNegative/".$_POST['dvendorNumberNeg'] . "/" . $_POST['dptvendorNumberNeg'] . "/" . $this->from . "/" . $this->to;
			$report = $this->brdata->get_vendorDepartmentReport($_POST['dvendorNumberNeg'], $_POST['dptvendorNumberNeg'], $this->today, $_POST['fromvendorDptNeg'], $_POST['tovendorDptNeg']);
			if(!empty($report[0]))
			{
				$title = '[VDR'.$_POST['dvendorNumberNeg'].' - '.$report[0]['VdrName'].'] - [DPT'.$_POST['dptvendorNumberNeg'].' - '.$report[0]['DptName'].'] - ['.$this->from.' to '.$this->to.'] - ['.count($report).' ITEMS]';				
			}
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles, 
				"title" => $title, "tableID" => "report_result", "action" => "vendorDepartment", "reportType" => 'templateWithSectionOrderNegative', 
				"from" => $this->from, "to" => $this->to, "report" => $report, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function UPCPriceCompare()
	{
		$data = array();
		$theadTitles = array("VDR #", "VDR NAME", "UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PACK", "SIZE",
			"CASE COST", "UNIT PRICE", "RETAIL", "ON-HAND", "LAST REC", "LAST REC DATE", "SALES", 
			"TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("VdrNo", "VdrName", "UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "unitPrice", "Retail", "onhand", "lastReceiving", "lastReceivingDate", "sales", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['upcNumber']))
		{
			$_POST['upcNumber'] = $this->completeValue($_POST['upcNumber'], 15);
			$this->setDefaultDates($_POST['fromupc'], $_POST['toupc']);
			$this->exportURL = "/csm/public/phpExcelExport/UPCPriceCompare/".$_POST['upcNumber'] . "/" . $this->from . "/" . $this->to;
			$upcReport = $this->brdata->get_upcReport($_POST['upcNumber'], $this->today, $_POST['toupc'], $_POST['fromupc']);
			$title = '[UPC : '.$_POST['upcNumber'].'] - ['.$this->from.' to '.$this->to.'] - ['.count($upcReport).' ITEMS]';
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles, 
				"title" => $title, "tableID" => "upcTable", "action" => "UPCPriceCompare", "reportType" => 'defaultTemplate', 
				"from" => $this->from, "to" => $this->to, "report" => $upcReport, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function UPCPriceCompare_url($upc)
	{
		$data = array();
		$theadTitles = array("VDR #", "VDR NAME", "UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PACK", "SIZE",
			"CASE COST", "UNIT PRICE", "RETAIL", "ON-HAND", "LAST REC", "LAST REC DATE", "SALES", "TPR PRICE",
		    "TPR START DATE", "TPR END DATE");
		$queryTitles = array("VdrNo", "VdrName", "UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "unitPrice", "Retail", "onhand", "lastReceiving", "lastReceivingDate", "sales", "tpr", "tprStart", "tprEnd");
		if(!empty($upc))
		{
			$this->exportURL = "/csm/public/phpExcelExport/UPCPriceCompare/". $upc . "/" . $this->from . "/" . $this->to;
			$upcReport = $this->brdata->get_upcReport($upc, $this->today, $this->to, $this->from);
			$title = '[UPC : '.$upc.'] - ['.$this->from.' to '.$this->to.'] - ['.count($upcReport).' ITEMS]';
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles, 
				"title" => $title, "tableID" => "upcTable", "action" => "UPCPriceCompare", "reportType" => 'defaultTemplate', 
				"from" => $this->from, "to" => $this->to, "report" => $upcReport, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function vendorItemCode()
	{
		$data = array();
		$theadTitles = array("VDR #", "VDR NAME", "UPC", "BRAND", "ITEM DESCRIPTION", "PACK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST REC", "LAST REC DATE", "SALES", 
			"TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("VdrNo", "VdrName", "UPC", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", "sales", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['itemcode']))
		{
			$this->setDefaultDates($_POST['fromcode'], $_POST['tocode']);
			$this->exportURL = "/csm/public/phpExcelExport/vendorItemCode/".$_POST['itemcode'] . "/" . $this->from . "/" . $this->to;
			$itemcodereport = $this->brdata->get_itemcodeReport($_POST['itemcode'], $this->today, $_POST['tocode'], $_POST['fromcode']);
			$title = '[ITEM CODE : '.$_POST['itemcode'].'] - ['.$this->from.' to '.$this->to.'] - ['.count($itemcodereport).' ITEMS]';
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles, 
				"title" => $title, "tableID" => "upcTable", "action" => "vendorItemCode", "reportType" => 'defaultTemplate', 
				"from" => $this->from, "to" => $this->to, "report" => $itemcodereport, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function vendorItemCode_url($itemCode)
	{
		$data = array();
		$theadTitles = array("VDR #", "VDR NAME", "UPC", "BRAND", "ITEM DESCRIPTION", "PACK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST REC", "LAST REC DATE", "SALES", 
			"TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("VdrNo", "VdrName", "UPC", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", "sales", "tpr", "tprStart", "tprEnd");
		if(!empty($itemCode))
		{
			$this->exportURL = "/csm/public/phpExcelExport/vendorItemCode/".$itemCode . "/" . $this->from . "/" . $this->to;
			$itemcodereport = $this->brdata->get_itemcodeReport($itemCode, $this->today, $this->to, $this->from);
			$title = '[ITEM CODE : '.$itemCode.'] - ['.$this->from.' to '.$this->to.'] - ['.count($itemcodereport).' ITEMS]';
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles, 
				"title" => $title, "tableID" => "upcTable", "action" => "vendorItemCode", "reportType" => 'defaultTemplate', 
				"from" => $this->from, "to" => $this->to, "report" => $itemcodereport, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function vendorNames()
	{
		$data = array();
		$this->classname = "liststable";
		$theadTitles = array("VDR #", "VDR NAME");
		$queryTitles = array("VdrNo", "VdrName");
		$this->exportURL = "/csm/public/phpExcelExport/vendorNames/";
		$vendorNames = $this->brdata->get_vendorNames();
		$title = "VENDOR NAMES REPORT";
		$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles, 
			"title" => $title, "tableID" => "report_result", "action" => "lists", "reportType" => 'defaultTemplate', 
			"from" => $this->from, "to" => $this->to, "report" => $vendorNames, "menu" => $this->userRole);
		$this->renderView($data);

	}

	public function sectionNames()
	{
		$data = array();
		$this->classname = "liststable";
		$theadTitles = array("DPT NO", "DPT NAME", "SCT NO", "SCT NAME");
		$queryTitles = array("DptNo", "DptName", "SctNo", "SctName");
		$this->exportURL = "/csm/public/phpExcelExport/sectionNames";
		$sectionNames = $this->brdata->get_sectionNames();
		$title = "SECTION NAMES REPORT";
		$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles, 
			"title" => $title, "tableID" => "report_result", "action" => "lists", "reportType" => 'defaultTemplate', 
			"from" => $this->from, "to" => $this->to, "report" => $sectionNames, "menu" => $this->userRole);
		$this->renderView($data);
	}

	public function departmentNames()
	{
		$data = array();
		$this->classname = "liststable";
		$theadTitles = array("DPT NO", "DPT NAME");
		$queryTitles = array("DptNo", "DptName");
		$this->exportURL = "/csm/public/phpExcelExport/departmentNames/";
		$departmentNames = $this->brdata->get_departmentNames();
		$title = "DEPARTMENT NAMES REPORT";
		$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles, 
			"title" => $title, "tableID" => "report_result", "action" => "lists", "reportType" => 'defaultTemplate', 
			"from" => $this->from, "to" => $this->to, "report" => $departmentNames, "menu" => $this->userRole);
		$this->renderView($data);
	}

	public function adjustments()
	{
		$data = array();
		$theadTitles = array("UPC", "ITEM DESCRIPTION", "SECTION NO", "SECTION NAME","LAST RECEIVING", "LAST RECEIVING DATE", "ADJUSTMENT", "ADJUSTMENT DATE");
		$queryTitles = array("UPC", "ItemDescription", "SctNo", "SctName", "lastReceiving", "lastReceivingDate", "adj", "Date");
		$this->exportURL = "/csm/public/phpExcelExport/adjustments";
		$adjustments = $this->brdata->get_adjReport();
		$title = "ADJUSTMENTS REPORT";
		$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles, 
			"title" => $title, "tableID" => "report_result", "action" => "lists", "reportType" => 'defaultTemplate', 
			"from" => $this->from, "to" => $this->to, "report" => $adjustments, "menu" => $this->userRole);
		$this->renderView($data);
	}

	public function logout()
	{
		session_unset();
		session_destroy();
		header('Location: /csm/public/login');
	}

	private function renderView($data)
	{
		if(!empty($data))
		{
			$this->view('home', $data);
		}
		else
		{
			$this->view('home');
		}
	}

	public function setDefaultDates($from, $to)
	{
		setCookie("from", $from);
		$_COOKIE["from"] = $from;
		setCookie("to", $to);
		$_COOKIE["to"] = $to;
		if(!empty($from))
		{
			$this->from = $from;
		}
		if(!empty($to))
		{
			$this->to = $to;
		}
	}
}