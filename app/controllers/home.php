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

	public function vendor()
	{
		$data = array();
		$title = "";
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST RECEIVING", "LAST RECEIVING DATE", "SALES", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", "sales", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['vendorNumber']))
		{
			$this->setDefaultDates($_POST['fromvendor'], $_POST['tovendor']);
			$this->exportURL = "/csm/public/phpExcelExport/vendor/" .$_POST['vendorNumber']. "/" . $this->from . "/" . $this->to;
			$vendorReport = $this->brdata->get_vendorReport($_POST['vendorNumber'], $this->today, $_POST['fromvendor'], $_POST['tovendor']);
			if(!empty($vendorReport[0]))
			{
				$title = '[VDR' . $_POST["vendorNumber"] . ' - '. $vendorReport[0]["VdrName"] . '] - [' . $this->from . ' to ' . $this->to . '] - [' . count($vendorReport) . ' ITEMS]';				
			}
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles , "title" => $title, "tableID" => "report_result", "action" => "vendor", "reportType" => 'templateWithSectionOrder', "from" => $this->from, "to" => $this->to, "report" => $vendorReport, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function UPCReceivingHistory()
	{
		$data = array();
		$title = "";
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PACK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST RECEIVING", "LAST RECEIVING DATE", 
			"SALES", "TPR PRICE", "TPR START DATE", "TPR END DATE", "VDR NO", "VDR NAME");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", 
			"sales", "tpr", "tprStart", "tprEnd", "VdrNo", "VdrName");
		if(!empty($_POST['upcReceivingNumber']))
		{
			$this->setDefaultDates($_POST['fromReceivingupc'], $_POST['toReceivingupc']);
			$this->exportURL = "/csm/public/phpExcelExport/UPCReceivingHistory/".$_POST['upcReceivingNumber'] . "/" . $this->from . "/" . $this->to;
			$receivingHistory = $this->brdata->get_upcReceivingHistory($_POST['upcReceivingNumber'], $this->today, $_POST['toReceivingupc'], $_POST['fromReceivingupc']);
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
			"CASE COST", "RETAIL", "ON-HAND", "LAST RECEIVING", "LAST RECEIVING DATE", "SALES", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", "sales", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['vendorNegNumber']))
		{
			$this->setDefaultDates($_POST['fromNegvendor'], $_POST['toNegvendor']);
			$this->exportURL = "/csm/public/phpExcelExport/vendorNegative/".$_POST['vendorNegNumber'] . "/" . $this->from . "/" . $this->to;
			$vendorReport = $this->brdata->get_vendorReport($_POST['vendorNegNumber'], $this->today, $_POST['fromNegvendor'], $_POST['toNegvendor']);
			$j=0;
			$i=0;
			foreach($vendorReport as $key => $value)
			{
				if($value['onhand'] >= 0 ||  $value['SctNo'] == 184)
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
				$title = '[VDR' . $_POST["vendorNegNumber"] . ' - '. $report[0]["VdrName"] . '] - [' . $this->from . ' to ' . $this->to . '] - [' . count($report) . ' ITEMS]';				
			}
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles , "title" => $title, "tableID" => "report_result", "action" => "vendor", "reportType" => 'templateWithSectionOrder', "from" => $this->from, "to" => $this->to, "report" => $report, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function sectionMovement()
	{
		$data = array();
		$title = "";
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PACK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST RECEIVING", "LAST RECEIVING DATE", 
			"SALES", "VDR #", "VDR NAME", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", 
			"sales", "VdrNo", "VdrName", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['sectionMvtNumber']))
		{
			$this->setDefaultDates($_POST['fromMvtsection'], $_POST['toMvtsection']);
			$this->exportURL = "/csm/public/phpExcelExport/sectionMovement/".$_POST['sectionMvtNumber'] . "/" . $this->from . "/" . $this->to;
			$sectionReport = $this->brdata->get_sectionReport($_POST['sectionMvtNumber'], $this->today, $_POST['fromMvtsection'], $_POST['toMvtsection']);
			
			$j=0;
			$i=0;
			foreach($sectionReport as $key => $value)
			{
				if($value['sales'] != NULL)
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
			"CASE COST", "UNIT PRICE", "RETAIL", "ON-HAND", "LAST RECEIVING", "LAST RECEIVING DATE", "SALES", "TPR PRICE", "TPR START DATE", "TPR END DATE", "VDR NO", "VDR NAME");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "unitPrice", "onhand", "lastReceiving", "lastReceivingDate", "sales", "tpr", "tprStart", "tprEnd", "VdrNo", "VdrName");
		$this->exportURL = "/csm/public/phpExcelExport/specials/" . $this->from . "/" . $this->to;
		$specialReport = $this->brdata->get_specialReport($this->today, $this->from, $this->to);
		$this->memcache->set("report", $specialReport);
		$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles , "title" => $title, "tableID" => "report_result", "action" => "special", "reportType" => 'templateWithSectionOrder', "from" => $this->from, "to" => $this->to, "report" => $specialReport, "menu" => $this->userRole);
		$this->renderView($data);
	}

	public function vendorMovement()
	{
		$data = array();
		$title = "";
		$report = null;
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST RECEIVING", "LAST RECEIVING DATE", "SALES", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", "sales", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['vendorMvtNumber']))
		{
			$this->setDefaultDates($_POST['fromMvtvendor'], $_POST['toMvtvendor']);
			$this->exportURL = "/csm/public/phpExcelExport/vendorMovement/".$_POST['vendorMvtNumber'] . "/" . $this->from . "/" . $this->to;
			$vendorReport = $this->brdata->get_vendorReport($_POST['vendorMvtNumber'], $this->today, $_POST['fromMvtvendor'], $_POST['toMvtvendor']);
			$j=0;
			$i=0;
			foreach($vendorReport as $key => $value)
			{
				if($value['sales'] != NULL)
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
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles , "title" => $title, "tableID" => "report_result", "action" => "vendor", "reportType" => 'templateWithSectionOrder', "from" => $this->from, "to" => $this->to, "report" => $report, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function vendorSectionMovement()
	{
		$data = array();
		$title = "";
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PACK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST RECEIVING", "LAST RECEIVING DATE", 
			"SALES", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", 
			"sales", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['svendorMvtNumber']) && !empty($_POST['sctvendorMvtNumber']))
		{
			$this->setDefaultDates($_POST['fromvendorMvtSection'], $_POST['tovendorMvtSection']);
			$this->exportURL = "/csm/public/phpExcelExport/vendorSectionMovement/".$_POST['svendorMvtNumber'] . "/" . $_POST['sctvendorMvtNumber'] . "/" . $this->from . "/" . $this->to;
			$vdrSctReport = $this->brdata->get_vendorSectionReport($_POST['svendorMvtNumber'], $_POST['sctvendorMvtNumber'], $this->today, $_POST['tovendorMvtSection'], $_POST['fromvendorMvtSection']);
			$j=0;
			$i=0;
			foreach($vdrSctReport as $key => $value)
			{
				if($value['sales'] != NULL)
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
			"CASE COST", "RETAIL", "ON-HAND", "LAST RECEIVING", "LAST RECEIVING DATE", "SALES", "TPR PRICE", "TPR START DATE", "TPR END DATE");
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
			"TPR END DATE", "PACK", "SIZE", "CASE COST", "UNIT PRICE", "RETAIL", "ITEM #", "LAST RECEIVING", "LAST RECEIVING DATE", "VDR NO", "VDR NAME");
		$queryTitles = array("UPC", "Brand", "ItemDescription", "onhand", "sales", "tpr", "tprStart", "tprEnd", 
			array("PackOne", "SizeAlphaOne", "CaseCostOne", "unitPriceOne", "RetailOne", "CertCodeOne", "lastReceivingOne", "lastReceivingDateOne", "VdrNoOne", "VdrNameOne"), 
			array("PackTwo", "SizeAlphaTwo", "CaseCostTwo","unitPriceTwo", "RetailTwo", "CertCodeTwo", "lastReceivingTwo", "lastReceivingDateTwo", "VdrNoTwo", "VdrNameTwo"));
		if(!empty($_POST['vendor1']) && !empty($_POST['vendor2']))
		{
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
			"TPR END DATE", "PACK", "SIZE", "CASE COST", "UNIT PRICE", "RETAIL", "ITEM #", "LAST RECEIVING", "LAST RECEIVING DATE", "VDR NO", "VDR NAME");
		$queryTitles = array("UPC", "Brand", "ItemDescription", "onhand", "sales", "tpr", "tprStart", "tprEnd", 
			array("PackOne", "SizeAlphaOne", "CaseCostOne", "unitPriceOne", "RetailOne", "CertCodeOne", "lastReceivingOne", "lastReceivingDateOne", "VdrNoOne", "VdrNameOne"), 
			array("PackTwo", "SizeAlphaTwo", "CaseCostTwo", "unitPriceTwo", "RetailTwo", "CertCodeTwo", "lastReceivingTwo", "lastReceivingDateTwo", "VdrNoTwo", "VdrNameTwo"));
		if(!empty($_POST['vendor1Section']) && !empty($_POST['vendor2Section']) && !empty($_POST['sectionCompare']))
		{
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
			"CASE COST", "RETAIL", "ON-HAND", "LAST RECEIVING", "LAST RECEIVING DATE", "SALES", 
			"VDR #", "VDR NAME", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", 
			"sales", "VdrNo", "VdrName", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['upcRangeNo1']) && !empty($_POST['upcRangeNo2']))
		{
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
			"CASE COST", "RETAIL", "ON-HAND", "LAST RECEIVING", "LAST RECEIVING DATE", 
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
			"CASE COST", "RETAIL", "ON-HAND", "LAST RECEIVING", "LAST RECEIVING DATE", 
			"SALES", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", 
			"sales", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['svendorNumber']) && !empty($_POST['sctvendorNumber']))
		{
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
			"CASE COST", "RETAIL", "ON-HAND", "LAST RECEIVING", "LAST RECEIVING DATE", 
			"SALES", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", 
			"sales", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['svendorNegNumber']) && !empty($_POST['sctvendorNegNumber']))
		{
			$this->setDefaultDates($_POST['fromvendorNegSection'], $_POST['tovendorNegSection']);
			$this->exportURL = "/csm/public/phpExcelExport/vendorSectionNegative/".$_POST['svendorNegNumber'] . "/" . $_POST['sctvendorNegNumber'] . "/" . $this->from . "/" . $this->to;
			$vdrSctReport = $this->brdata->get_vendorSectionReport($_POST['svendorNegNumber'], $_POST['sctvendorNegNumber'], $this->today, $_POST['tovendorNegSection'], $_POST['fromvendorNegSection']);
			$j=0;
			$i=0;
			foreach($vdrSctReport as $key => $value)
			{
				if($value['onhand'] >= 0 ||  $value['SctNo'] == 184)
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
				$title = '[DPT'.$report[0]['DptNo'].' - '.$report[0]['DptName'].'] - [VDR'.$_POST['svendorNegNumber'].' - '.$report[0]['VdrName'].'] - 
				[SCT'.$_POST['sctvendorNegNumber'].' - '.$report[0]['SctName'].'] - ['.$this->from.' to '.$this->to.']  - ['.count($report).' ITEMS]';
			}
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles, 
				"title" => $title, "tableID" => "report_result", "action" => "vendorSection", "reportType" => 'defaultTemplate', 
				"from" => $this->from, "to" => $this->to, "report" => $report, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function section()
	{
		$data = array();
		$title = "";
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PACK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST RECEIVING", "LAST RECEIVING DATE", 
			"SALES", "VDR #", "VDR NAME", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", 
			"sales", "VdrNo", "VdrName", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['sectionNumber']))
		{
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

	public function department()
	{
		$data = array();
		$title = "";
		$theadTitles = array("UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PACK", "SIZE",
			"CASE COST", "RETAIL", "ON-HAND", "LAST RECEIVING", "LAST RECEIVING DATE", "SALES", 
			"VDR #", "VDR NAME", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", "sales", 
			"VdrNo", "VdrName", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['departmentNumber']))
		{
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
			"CASE COST", "RETAIL", "ON-HAND", "LAST RECEIVING", "LAST RECEIVING DATE", 
			"SALES", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", 
			"sales", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['dvendorNumber']) && !empty($_POST['dptvendorNumber']))
		{
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
			"CASE COST", "RETAIL", "ON-HAND", "LAST RECEIVING", "LAST RECEIVING DATE", 
			"SALES", "TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "Retail", "onhand", "lastReceiving", "lastReceivingDate", 
			"sales", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['dvendorNumberNeg']) && !empty($_POST['dptvendorNumberNeg']))
		{
			$this->setDefaultDates($_POST['fromvendorDptNeg'], $_POST['tovendorDptNeg']);
			$this->exportURL = "/csm/public/phpExcelExport/vendorDepartmentNegative/".$_POST['dvendorNumberNeg'] . "/" . $_POST['dptvendorNumberNeg'] . "/" . $this->from . "/" . $this->to;
			$vdrDptReport = $this->brdata->get_vendorDepartmentReport($_POST['dvendorNumberNeg'], $_POST['dptvendorNumberNeg'], $this->today, $_POST['fromvendorDptNeg'], $_POST['tovendorDptNeg']);
			$j=0;
			$i=0;
			foreach($vdrDptReport as $key => $value)
			{
				if($value['onhand'] >= 0)
				{
					unset($vdrDptReport[$i]);
				}
				$i = $i + 1;
			}
			foreach($vdrDptReport as $key => $value)
			{
				$report[$j] = $value;
				$j = $j + 1;
			}
			if(!empty($report[0]))
			{
				$title = '[VDR'.$_POST['dvendorNumberNeg'].' - '.$report[0]['VdrName'].'] - [DPT'.$_POST['dptvendorNumberNeg'].' - '.$report[0]['DptName'].'] - ['.$this->from.' to '.$this->to.'] - ['.count($report).' ITEMS]';				
			}
			$data = array("class" => $this->classname, "exportURL" => $this->exportURL, "qt" => $queryTitles, "thead" => $theadTitles, 
				"title" => $title, "tableID" => "report_result", "action" => "vendorDepartment", "reportType" => 'templateWithSectionOrder', 
				"from" => $this->from, "to" => $this->to, "report" => $report, "menu" => $this->userRole);
		}
		$this->renderView($data);
	}

	public function UPCPriceCompare()
	{
		$data = array();
		$theadTitles = array("VDR #", "VDR NAME", "UPC", "ITEM #", "BRAND", "ITEM DESCRIPTION", "PACK", "SIZE",
			"CASE COST", "UNIT PRICE", "RETAIL", "ON-HAND", "LAST RECEIVING", "LAST RECEIVING DATE", "SALES", 
			"TPR PRICE", "TPR START DATE", "TPR END DATE");
		$queryTitles = array("VdrNo", "VdrName", "UPC", "CertCode", "Brand", "ItemDescription", "Pack", "SizeAlpha",
			"CaseCost", "unitPrice", "Retail", "onhand", "lastReceiving", "lastReceivingDate", "sales", "tpr", "tprStart", "tprEnd");
		if(!empty($_POST['upcNumber']))
		{
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
			"CASE COST", "UNIT PRICE", "RETAIL", "ON-HAND", "LAST RECEIVING", "LAST RECEIVING DATE", "SALES", "TPR PRICE",
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
			"CASE COST", "RETAIL", "ON-HAND", "LAST RECEIVING", "LAST RECEIVING DATE", "SALES", 
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
			"CASE COST", "RETAIL", "ON-HAND", "LAST RECEIVING", "LAST RECEIVING DATE", "SALES", 
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
		$theadTitles = array("SCT NO", "SCT NAME", "DPT NO", "DPT NAME");
		$queryTitles = array("SctNo", "SctName", "DptNo", "DptName");
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