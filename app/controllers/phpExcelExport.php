<?php
session_start();
class phpExcelExport extends Controller{
	
	private $users;

	private $phpExcel;
	
	private $brdata;
	
	private $sheet;
		
	private $today;

	private $columns;

	private $columnWidths;

	private $cell_border;

	private $cacheMethod;


	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('America/Port-au-Prince');
		$this->today = date('Y-m-d', strtotime("-1 days"));
		// $this->cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory; 
		$this->phpExcel = $this->phpExcel();
		$this->phpExcel->createSheet();
		$this->sheet = $this->phpExcel->getActiveSheet();
		$this->users = $this->model('users');
		$this->brdata = $this->model('brdata');
		$this->today = date('Y-m-d', strtotime("-1 days"));
		$this->columnWidths = array("UPC" => 13, "VDR ITEM #" => 11, "BRAND" => 10, "ITEM DESCRIPTION" => 30, "PACK" => 6, "SIZE" => 8, 
			"CASE COST" => 10, "RETAIL" => 7, "ON-HAND" => 8, "LAST RECEIVING" => 12, "LAST RECEIVING DATE" => 15, "SALES" => 5, "VDR #" => 7, "VDR NAME" => 22, 
			"TPR PRICE" => 7, "TPR START" => 8, "TPR END" => 8, "SCT NO" => 8, "SCT NAME" => 30, "DPT NO" => 8, "DPT NAME" => 30, "UNIT PRICE" => 10);
		$this->columns = array("UPC" => "UPC", "VDR ITEM #" => "CertCode", "BRAND" => "Brand", "ITEM DESCRIPTION" => "ItemDescription", "PACK" => "Pack", "SIZE" => "SizeAlpha", "CASE COST" => "CaseCost", "RETAIL" => "Retail", 
			"ON-HAND" => "onhand", "LAST RECEIVING" => "lastReceiving", "LAST RECEIVING DATE" => "lastReceivingDate", "SALES" => "sales", "VDR #" => "VdrNo", "VDR NAME" => "VdrName", "TPR PRICE" => "tpr", "TPR START" => "tprStart", 
			"TPR END" => "tprEnd", "SCT NO" => "SctNo", "SCT NAME" => "SctName", "DPT NO" => "DptNo", "DPT NAME" => "DptName", "UNIT PRICE" => "unitPrice");
	} 

	public function vendor($vendor, $from, $to)
	{
		$header = array("A" => "UPC", 
						"B" => "VDR ITEM #", 
						"C" => "BRAND", 
						"D" => "ITEM DESCRIPTION", 
						"E" => "PACK", 
						"F" => "SIZE", 
						"G" => "CASE COST", 
						"H" => "RETAIL", 
						"I" => "ON-HAND", 
						"J" => "LAST RECEIVING", 
						"K" => "LAST RECEIVING DATE", 
						"L" => "SALES", 
						"M" => "TPR PRICE", 
						"N" => "TPR START", 
						"O" => "TPR END");
		$this->setSheetName("VENDOR REPORT");
		$report = $this->brdata->get_vendorReport($vendor, $this->today, $from, $to);
		$lastItem = count($report) + 4;
		$this->setHeader("VENDOR SECTION FINAL REPORT","[ VENDOR : " . $vendor . " - ".$report[0]['VdrName']." ] - [ 
			".$from." - ".$to." ]", $header, 'vdrReport', $lastItem);
		$this->setReportWithSection($header, $report);
		$this->saveReport('VendorSectionFinal_'.$vendor.'_'.$report[0]['VdrName'].'_'.$this->today);
	}

	public function vendorNegative($vendor, $from, $to)
	{
		$header = array("A" => "UPC", 
						"B" => "VDR ITEM #", 
						"C" => "BRAND", 
						"D" => "ITEM DESCRIPTION", 
						"E" => "PACK", 
						"F" => "SIZE", 
						"G" => "CASE COST", 
						"H" => "RETAIL", 
						"I" => "ON-HAND", 
						"J" => "LAST RECEIVING", 
						"K" => "LAST RECEIVING DATE", 
						"L" => "SALES", 
						"M" => "TPR PRICE", 
						"N" => "TPR START", 
						"O" => "TPR END");
		$this->setSheetName("VENDOR NEGATIVE ON-HAND REPORT");
		$vendorReport = $this->brdata->get_vendorReport($vendor, $this->today, $from, $to);
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
		$lastItem = count($report) + 4;
		$this->setHeader("VENDOR SECTION FINAL REPORT WITH NEGATIVE ON-HAND","[ VENDOR : " . $vendor . " - ".$report[0]['VdrName']." ] - [ 
			".$from." - ".$to." ]", $header, 'vdrReport', $lastItem);
		$this->setReportWithSection($header, $report);
		$this->saveReport('VendorSectionFinalNegative_'.$vendor.'_'.$report[0]['VdrName'].'_'.$this->today);
	}

	public function vendorSectionNegative($vendor, $section, $from, $to)
	{
		$header = array("A" => "UPC", 
						"B" => "VDR ITEM #", 
						"C" => "BRAND", 
						"D" => "ITEM DESCRIPTION", 
						"E" => "PACK", 
						"F" => "SIZE", 
						"G" => "CASE COST", 
						"H" => "RETAIL", 
						"I" => "ON-HAND", 
						"J" => "LAST RECEIVING", 
						"K" => "LAST RECEIVING DATE", 
						"L" => "SALES", 
						"M" => "TPR PRICE", 
						"N" => "TPR START", 
						"O" => "TPR END");
		$this->setSheetName("VENDOR SECTION NEGATIVE ON-HAND REPORT");
		$vendorReport = $this->brdata->get_vendorSectionReport($vendor, $section, $this->today, $from, $to);
		$j=0;
		$i=0;
		$vendorName = $vendorReport[0]['VdrName'];
		$sectionName = $vendorReport[0]['SctName'];
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
		$lastItem = count($report) + 4;
		$this->setHeader("VENDOR SECTION REPORT WITH NEGATIVE ON-HAND","[ VENDOR : " . $vendor . " - ".$vendorName." ] - [ SECTION : " . $section . " - " . $sectionName . "] - [ 
			" . $from . " - " . $to . " ]", $header, 'vdrSctNegativeReport', $lastItem);
		$this->setReportWithSection($header, $report);
		$this->saveReport('VendorSectionNegative_'.$vendor.'_'.$vendorName.'_'.$this->today);
	}

	public function sectionMovement($section, $from, $to)
	{
		$header = array("A" => "UPC", 
						"B" => "VDR ITEM #", 
						"C" => "BRAND", 
						"D" => "ITEM DESCRIPTION", 
						"E" => "PACK", 
						"F" => "SIZE", 
						"G" => "CASE COST", 
						"H"	 => "RETAIL", 
						"I" => "ON-HAND", 
						"J" => "LAST RECEIVING", 
						"K" => "LAST RECEIVING DATE", 
						"L" => "SALES", 
						"M" => "VDR #", 
						"N" => "VDR NAME", 
						"O" => "TPR PRICE", 
						"P" => "TPR START", 
						"Q" => "TPR END");
		$this->setSheetName("SECTION MOVEMENT REPORT");
		$sectionReport = $this->brdata->get_sectionReport($section, $this->today, $from, $to);
		$i =0;
		$j=0;
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
		$lastItem = count($report) + 4;
		$this->setHeader("SECTION MOVEMENT REPORT" ," [SCT : ".$section." - " . $report[0]['SctName'] . " ] [ ".$from." - ".$to." ]", $header, "sctReport", $lastItem);
		$this->setReport($header, $report);
		$this->saveReport('SectionMovement_'.$section.'_' . $report[0]['SctName'] . '_' . $this->today);
	}

	public function vendorMovement($vendor, $from, $to)
	{
		$header = array("A" => "UPC", 
						"B" => "VDR ITEM #", 
						"C" => "BRAND", 
						"D" => "ITEM DESCRIPTION", 
						"E" => "PACK", 
						"F" => "SIZE", 
						"G" => "CASE COST", 
						"H" => "RETAIL", 
						"I" => "ON-HAND", 
						"J" => "LAST RECEIVING", 
						"K" => "LAST RECEIVING DATE", 
						"L" => "SALES", 
						"M" => "TPR PRICE", 
						"N" => "TPR START", 
						"O" => "TPR END");
		$this->setSheetName("VENDOR REPORT");
		$vendorReport = $this->brdata->get_vendorReport($vendor, $this->today, $from, $to);
		$i =0;
		$j=0;
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
		$lastItem = count($report) + 4;
		$this->setHeader("VENDOR MOVEMENT REPORT","[ VENDOR : " . $vendor . " - ".$report[0]['VdrName']." ] - [ 
			".$from." - ".$to." ]", $header, 'vdrReport', $lastItem);
		$this->setReportWithSection($header, $report);
		$this->saveReport('VendorMovement_'.$vendor.'_'.$report[0]['VdrName'].'_'.$this->today);
	}

	public function vendorSectionMovement($vendor, $section, $from, $to)
	{
		$header = array("A" => "UPC", 
						"B" => "VDR ITEM #", 
						"C" => "BRAND", 
						"D" => "ITEM DESCRIPTION", 
						"E" => "PACK", 
						"F" => "SIZE", 
						"G" => "CASE COST", 
						"H" => "RETAIL", 
						"I" => "ON-HAND", 
						"J" => "LAST RECEIVING", 
						"K" => "LAST RECEIVING DATE", 
						"L" => "SALES", 
						"M" => "TPR PRICE", 
						"N" => "TPR START", 
						"O" => "TPR END");
		$this->setSheetName("VENDOR SECTION MOVEMENT REPORT");
		$vendorReport = $this->brdata->get_vendorSectionReport($vendor, $section, $this->today, $to, $from);
		$i =0;
		$j=0;
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
		$lastItem = count($report) + 4;
		$this->setHeader("VENDOR SECTION MOVEMENT REPORT" ,"[ VENDOR : " . $vendor . " - " . $report[0]['VdrName'] . 
			" ] - [ SECTION ".$section." - " . $report[0]['SctName'] . "] - [ ".$from." - ".$to." ]", 
			$header, 'vdrSctReport', $lastItem);
		$this->setReport($header, $report);
		$this->saveReport('VendorSectionMovement_'.$vendor.'_'.$report[0]['VdrName'].'_'.$section.'_'.$report[0]['SctName'].'_'.$this->today);
	}

	public function UPCReceivingHistory($upc, $from, $to)
	{
		$header = array("A" => "UPC", 
						"B" => "VDR ITEM #", 
						"C" => "BRAND", 
						"D" => "ITEM DESCRIPTION", 
						"E" => "PACK", 
						"F" => "SIZE", 
						"G" => "CASE COST", 
						"H" => "RETAIL", 
						"I" => "ON-HAND", 
						"J" => "LAST RECEIVING", 
						"K" => "LAST RECEIVING DATE", 
						"L" => "SALES", 
						"M" => "TPR PRICE", 
						"N" => "TPR START", 
						"O" => "TPR END",
						"P" => "VDR #", 
						"Q" => "VDR NAME");
		$this->setSheetName("UPC RECEIVING HISTORY");
		$report = $this->brdata->get_upcReceivingHistory($upc, $this->today, $to, $from);
		$lastItem = count($report) + 4;
		$this->setHeader("UPC RECEIVING HISTORY","[ UPC : ".$upc." ] - [ 
			".$from." - ".$to." ]", $header, 'upcReceivingReport', $lastItem);
		$this->setReport($header, $report);
		$this->saveReport('upcReceivingHistory_'.$upc.'_'.$this->today);
	}

	public function specials($from, $to)
	{
		$header = array("A" => "UPC", 
						"B" => "VDR ITEM #", 
						"C" => "BRAND", 
						"D" => "ITEM DESCRIPTION", 
						"E" => "PACK", 
						"F" => "SIZE", 
						"G" => "CASE COST", 
						"H" => "UNIT PRICE", 
						"I" => "RETAIL", 
						"J" => "ON-HAND", 
						"K" => "LAST RECEIVING", 
						"L" => "LAST RECEIVING DATE", 
						"M" => "SALES", 
						"N" => "TPR PRICE", 
						"O" => "TPR START", 
						"P" => "TPR END");
		$this->setSheetName("TPR REPORT");
		$report = $this->brdata->get_specialReport($this->today, $from, $to);
		$lastItem = count($report) + 4;
		$this->setHeader("TPR SPECIALS REPORT", " [  " . $from . " - " . $to . " ]", $header, 'specials_r', $lastItem, $lastItem);
		$this->setReportWithSection($header, $report);
		$this->saveReport('TPRReport_'.$this->today);
	}

	public function vendorPriceCompare($vendor1, $vendor2, $from, $to)
	{
		$header = array("A" => "UPC", 
						"B" => "BRAND", 
						"C" => "ITEM DESCRIPTION",
						"D" => "ON-HAND", 
						"E" => "SALES", 
						"F" => "TPR PRICE", 
						"G" => "TPR START", 
						"H" => "TPR END", 
						"I" => "PACK", 
						"J" => "SIZE", 
						"K" => "CASE COST", 
						"L" => "UNIT PRICE", 
						"M" => "RETAIL", 
						"N" => "VDR ITEM #", 
						"O" => "LAST RECEIVING", 
						"P" => "LAST RECEIVING DATE", 
						"Q" => "VDR #", 
						"R" => "VDR NAME");
		$this->setSheetName("VENDOR PRICE COMPARE REPORT");
		$report = $this->brdata->vendorPriceCompare($vendor1, $vendor2, $this->today, $from, $to);
		$lastItem = count($report) + 4;
		$this->setHeader("VENDOR PRICE COMPARE REPORT", " [ VENDORS : " . $vendor1 . " - ".$report[0]['VdrNameOne']." / " . $vendor2 . " - " .
			$report[0]['VdrNameTwo'] . " ] - [ ".$from." - ".$to." ]", $header, "vdrPriceCompare", $lastItem);
		$this->setCompareReport($header, $report);
		$this->saveReport('VendorPriceCompare_'.$report[0]['VdrNameOne'].'_'.$report[0]['VdrNameTwo'].'_'.$this->today);
	}

	public function sectionPriceCompare($vendor1, $vendor2, $section, $from, $to)
	{
		$header = array("A" => "UPC", 
						"B" => "BRAND", 
						"C" => "ITEM DESCRIPTION",
						"D" => "ON-HAND", 
						"E" => "SALES", 
						"F" => "TPR PRICE", 
						"G" => "TPR START", 
						"H" => "TPR END", 
						"I" => "PACK", 
						"J" => "SIZE", 
						"K" => "CASE COST", 
						"L" => "UNIT PRICE", 
						"M" => "RETAIL", 
						"N" => "VDR ITEM #", 
						"O" => "LAST RECEIVING", 
						"P" => "LAST RECEIVING DATE", 
						"Q" => "VDR #", 
						"R" => "VDR NAME");
		$this->setSheetName("SECTION PRICE COMPARE REPORT");
		$report = $this->brdata->sectionPriceCompare($vendor1, $vendor2, $section, $this->today, $from, $to);
		$lastItem = count($report) + 4;
		$this->setHeader("VENDOR PRICE COMPARE PER SECTION", " [ VENDORS : " . $vendor1 . " - ".$report[0]['VdrNameOne']." / " . $vendor2 . " - " .
			$report[0]['VdrNameTwo'] . " ] - [ SECTION : " . $section . " - " . $report[0]['SctName'] . " ] - [ ".$from." - ".$to." ]", $header, "vdrPriceCompare", $lastItem);
		$this->setCompareReport($header, $report);
		$this->saveReport('SectionPriceCompare_'.$report[0]['VdrNameOne'].'_'.$report[0]['VdrNameTwo'] .'_'.$section."_".$this->today);
	}

	public function department($department, $from, $to)
	{
		$header = array("A" => "UPC", 
						"B" => "VDR ITEM #", 
						"C" => "BRAND", 
						"D" => "ITEM DESCRIPTION", 
						"E" => "PACK", 
						"F" => "SIZE", 
						"G" => "CASE COST", 
						"H" => "RETAIL", 
						"I" => "ON-HAND", 
						"J" => "LAST RECEIVING", 
						"K" => "LAST RECEIVING DATE", 
						"L" => "SALES", 
						"M" => "VDR #", 
						"N" => "VDR NAME", 
						"O" => "TPR PRICE", 
						"P" => "TPR START", 
						"Q" => "TPR END");
		$this->setSheetName("DEPARTMENT REPORT");
		$report = $this->brdata->get_departmentReport($department, $this->today, $from, $to);
		$lastItem = count($report) + 4;
		$this->setHeader("DEPARTMENT REPORT", "[ DPT " . $department . " - " . $report[0]['DptName'] . " ] - 
			 [ ".$from." - ".$to. " ]", $header, "dptReport", $lastItem);
		$this->setReportWithSection($header, $report);
		$this->saveReport('DepartmentReport_'.$report[0]['DptName'].'_'.$this->today);
	}

	public function vendorDepartment($vendor, $department, $from, $to)
	{
		$header = array("A" => "UPC", 
						"B" => "VDR ITEM #", 
						"C" => "BRAND", 
						"D" => "ITEM DESCRIPTION", 
						"E" => "PACK", 
						"F" => "SIZE", 
						"G" => "CASE COST",
						"H" => "RETAIL", 
						"I" => "ON-HAND", 
						"J" => "LAST RECEIVING", 
						"K" => "LAST RECEIVING DATE", 
						"L" => "SALES", 
						"M" => "TPR PRICE", 
						"N" => "TPR START", 
						"O" => "TPR END");
		$this->setSheetName("VENDOR DEPARTMENT REPORT");
		$report = $this->brdata->get_vendorDepartmentReport($vendor, $department, $this->today, $to, $from);
		$lastItem = count($report) + 4;
		$this->setHeader("VENDOR DEPARTMENT REPORT" ,"[ VENDOR : " . $vendor . " -  ".$report[0]['VdrName']." ] - [ DPT : 
			" . $department . " - " . $report[0]['DptName'] . "] - [ ".$from." - ".$to."] ", $header, "vdrDpt", $lastItem);
		$this->setReportWithSection($header, $report);
		$this->saveReport('VendorDepartment'.$report[0]['VdrName'].'_'.$report[0]['DptName'].'_'.$this->today);
	}

	public function UPCRange($upc1, $upc2, $from, $to)
	{
		$header = array("A" => "UPC", 
						"B" => "VDR ITEM #", 
						"C" => "BRAND", 
						"D" => "ITEM DESCRIPTION", 
						"E" => "PACK", 
						"F" => "SIZE", 
						"G" => "CASE COST", 
						"H" => "RETAIL", 
						"I" => "ON-HAND", 
						"J" => "LAST RECEIVING", 
						"K" => "LAST RECEIVING DATE", 
						"L" => "SALES", 
						"M" => "VDR #", 
						"N" => "VDR NAME", 
						"O" => "TPR PRICE", 
						"P" => "TPR START", 
						"Q" => "TPR END");
		$this->setSheetName("UPC RANGE REPORT");
		$report = $this->brdata->get_upcRangeReport($upc1, $upc2, $this->today, $to, $from);
		$lastItem = count($report) + 4;
		$this->setHeader("UPC RANGE REPORT" ,"[ UPC 1 : " . $upc1 . " / UPC2 : 
			" . $upc2 . " ] - [ ".$from." - ".$to." ]", $header, "upcRange", $lastItem);
		$this->setReportWithSection($header, $report);
		$this->saveReport('UPCRange_'.$upc1.'_'.$upc2.'_'.$this->today);
	}

	public function itemDescription($description, $from, $to)
	{
		$header = array("A" => "UPC", 
						"B" => "VDR ITEM #", 
						"C" => "BRAND", 
						"D" => "ITEM DESCRIPTION", 
						"E" => "PACK", 
						"F" => "SIZE", 
						"G" => "CASE COST", 
						"H" => "RETAIL", 
						"I" => "ON-HAND", 
						"J" => "LAST RECEIVING", 
						"K" => "LAST RECEIVING DATE", 
						"L" => "SALES", 
						"M" => "VDR #", 
						"N" => "VDR NAME", 
						"O" => "TPR PRICE", 
						"P" => "TPR START", 
						"Q" => "TPR END");
		$this->setSheetName("ITEM DESCRIPTION REPORT");
		$description = str_replace("_", " ", $description);
		$report = $this->brdata->get_itemDescription($description, $this->today, $to, $from);
		$lastItem = count($report) + 4;
		$this->setReportWithSection($header, $report);
		$this->setHeader("ITEM DESCRIPTION REPORT", "[ ". $description." ] [ FROM ".$from." TO ".$to." ]", $header, "itemDescription", $lastItem);
		$description = str_replace(" ", "_", $description);
		$this->saveReport('ItemDescriptionReport_'.$description.'_'.$this->today);
	}

	public function section($section, $from, $to)
	{
		$header = array("A" => "UPC", 
						"B" => "VDR ITEM #", 
						"C" => "BRAND", 
						"D" => "ITEM DESCRIPTION", 
						"E" => "PACK", 
						"F" => "SIZE", 
						"G" => "CASE COST", 
						"H"	 => "RETAIL", 
						"I" => "ON-HAND", 
						"J" => "LAST RECEIVING", 
						"K" => "LAST RECEIVING DATE", 
						"L" => "SALES", 
						"M" => "VDR #", 
						"N" => "VDR NAME", 
						"O" => "TPR PRICE", 
						"P" => "TPR START", 
						"Q" => "TPR END");
		$this->setSheetName("SECTION REPORT");
		$report = $this->brdata->get_sectionReport($section, $this->today, $from, $to);
		$lastItem = count($report) + 4;
		$this->setHeader("SECTION REPORT" ," [SCT : ".$section." - " . $report[0]['SctName'] . " ] [ ".$from." - ".$to." ]", $header, "sctReport", $lastItem);
		$this->setReport($header, $report);
		$this->saveReport('Section_' . $report[0]['SctName'] . '_' . $this->today);
	}

	public function vendorSection($vendor, $section, $from, $to)
	{
		$header = array("A" => "UPC", 
						"B" => "VDR ITEM #", 
						"C" => "BRAND", 
						"D" => "ITEM DESCRIPTION", 
						"E" => "PACK", 
						"F" => "SIZE", 
						"G" => "CASE COST", 
						"H" => "RETAIL", 
						"I" => "ON-HAND", 
						"J" => "LAST RECEIVING", 
						"K" => "LAST RECEIVING DATE", 
						"L" => "SALES", 
						"M" => "TPR PRICE", 
						"N" => "TPR START", 
						"O" => "TPR END");
		$this->setSheetName("VENDOR SECTION REPORT");
		$report = $this->brdata->get_vendorSectionReport($vendor, $section, $this->today, $to, $from);
		$lastItem = count($report) + 4;
		$this->setHeader("VENDOR SECTION REPORT" ,"[ VENDOR : " . $vendor . " - " . $report[0]['VdrName'] . 
			" ] - [ SECTION ".$section." - " . $report[0]['SctName'] . "] - [ ".$from." - ".$to." ]", 
			$header, 'vdrSctReport', $lastItem);
		$this->setReport($header, $report);
		$this->saveReport('VendorSection_'.$report[0]['VdrName'].'_'.$report[0]['SctName'].'_'.$this->today);
	}

	public function UPCPriceCompare($upc, $from, $to)
	{
		$header = array("A" => "VDR #", 
						"B" => "VDR NAME", 
						"C" => "UPC", 
						"D" => "VDR ITEM #", 
						"E" => "BRAND", 
						"F" => "ITEM DESCRIPTION", 
						"G" => "PACK", 
						"H" => "SIZE", 
						"I" => "CASE COST", 
						"J" => "UNIT PRICE",
						"K" => "RETAIL", 
						"L" => "ON-HAND", 
						"M" => "LAST RECEIVING", 
						"N" => "LAST RECEIVING DATE", 
						"O" => "SALES", 
						"P" => "TPR PRICE", 
						"Q" => "TPR START", 
						"R" => "TPR END");
		$this->setSheetName("VENDOR UPC PRICE COMPARE REPORT");
		$report = $this->brdata->get_upcReport($upc, $this->today, $to, $from);
		$lastItem = count($report) + 4;
		$this->setHeader("UPC PRICE COMPARE REPORT" ,"[ UPC : " . $upc . " ] - [ ". $from . " - " . $to . " ]", $header, "upcPriceCompare", $lastItem);
		$this->setReport($header, $report);
		$this->saveReport('VendorUPCPriceCompare_' . $upc . '_' . $this->today);
	}

	public function vendorItemCode($code, $from, $to)
	{
		$header = array("A" => "VDR #", 
						"B" => "VDR NAME", 
						"C" => "UPC", 
						"D" => "VDR ITEM #", 
						"E" => "BRAND", 
						"F" => "ITEM DESCRIPTION", 
						"G" => "PACK", 
						"H" => "SIZE", 
						"I" => "CASE COST", 
						"J" => "RETAIL", 
						"K" => "ON-HAND", 
						"L" => "LAST RECEIVING", 
						"M" => "LAST RECEIVING DATE", 
						"N" => "SALES", 
						"O" => "TPR PRICE", 
						"P" => "TPR START", 
						"Q" => "TPR END");
		$this->setSheetName("VENDOR ITEM CODE REPORT");
		$report = $this->brdata->get_itemcodeReport($code, $this->today, $to, $from);
		$lastItem = count($report) + 4;
		$this->setHeader("VENDOR ITEM CODE REPORT" ,"[ ITEM CODE " . $code . " ] - [ ". $from . " - " . $to." ]", $header, 'itemCode', $lastItem);
		$this->setReport($header, $report);
		$this->saveReport('VendorItemCode_' . $code . '_' . $this->today);
	}

	public function sectionNames()
	{
		$header = array("A" => "SCT NO", "B" => "SCT NAME", "C" => "DPT NO", "D" => "DPT NAME");
		$this->setSheetName("SECTION NAMES REPORT");
		$report = $this->brdata->get_sectionNames();
		$lastItem = count($report) + 4;
		$this->setHeader("SECTION NAMES REPORT", "LIST OF STORE SECTIONS", $header, "sctList", $lastItem);
		$this->setReport($header, $report);
		$this->saveReport('SectionNames_' . $this->today);
	}

	public function departmentNames()
	{
		$header = array("A" => "DPT NO", "B" => "DPT NAME");
		$this->setSheetName("DEPARTMENT NAMES REPORT");
		$report = $this->brdata->get_departmentNames();
		$lastItem = count($report) + 4;
		$this->setHeader("DEPARTMENT NAMES REPORT", "LIST OF STORE DEPARTMENTS", $header, 'dptList', $lastItem);
		$this->setReport($header, $report);
		$this->saveReport('DepartmentNames_' . $this->today);
	}

	public function vendorNames()
	{
		$header = array("A" => "VDR #", "B" => "VDR NAME");
		$this->setSheetName("VENDOR NAMES REPORTS");
		$report = $this->brdata->get_vendorNames();
		$lastItem = count($report) + 4;
		$this->setHeader("VENDOR NAMES REPORT", "LIST OF VENDORS", $header, 'vdrList', $lastItem);
		$this->setReport($header, $report);
		$this->saveReport('VendorNames_' . $this->today);
	}

	private function getItemDescriptionColumn($header)
	{
		$returnValue = '';
		foreach($header as $key => $value)
		{
			$returnValue = $key;
			if($value == "ITEM DESCRIPTION")
			{
				break;
			}
		}
		return $returnValue;
	}


	private function setSheetName($sheetName)
	{
		$this->sheet->Name = $sheetName;
	}

	private function setHeader($title, $subtitle, $header, $reportType, $lastItem)
	{
		$myWorkSheet = new PHPExcel_Worksheet($this->phpExcel, $reportType); 
		// Attach the “My Data” worksheet as the first worksheet in the PHPExcel object 
		$lastKey = $this->getLastArrayKey($header);
		$this->phpExcel->addSheet($myWorkSheet, 0);
		// Set report to landscape 
		$this->phpExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

		$this->sheet->mergeCells('A1:' . $lastKey . '1');
		$this->sheet->mergeCells('A2:' . $lastKey . '2');
		$this->sheet->getRowDimension('1')->setRowHeight(35);
		$this->sheet->setCellValue('A1', $title);
		$this->sheet->setCellValue('A2', $subtitle);
		$this->sheet->getStyle('A1:' . $lastKey . '3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->sheet->getRowDimension('2')->setRowHeight(25);
		$this->sheet->getRowDimension('3')->setRowHeight(25);
		$this->sheet->getStyle('A1:' . $lastKey . '3')->getFont()->setBold(true);
		$this->sheet->getStyle('A1:' . $lastKey . '1')->getFont()->setSize(14);
		$this->sheet->getStyle('A1:' . $lastKey . '3') ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		$this->sheet->getPageMargins()->setRight(0); 
		$this->sheet->getPageMargins()->setLeft(0);
		$this->sheet->getPageMargins()->setTop(0); 
		$this->sheet->getPageMargins()->setBottom(0);
		$this->sheet->getPageSetup()->setFitToWidth(1);
		$this->sheet->getPageSetup()->setFitToHeight(0);  
		// $this->sheet->getPageSetup()->setPrintArea("A1:" . $lastKey . $lastItem);  

		$this->phpExcel->getProperties()->setCreator("Tess Attie"); 
		$this->phpExcel->getProperties()->setLastModifiedBy("Today"); 
		$this->phpExcel->getProperties()->setTitle($title); 
		$this->phpExcel->getProperties()->setSubject("Office 2005 XLS Test Document"); 
		$this->phpExcel->getProperties()->setDescription("Test document for Office 2005 XLS, generated using PHP classes."); 
		$this->phpExcel->getProperties()->setKeywords("office 2007 openxml php"); 
		$this->phpExcel->getProperties()->setCategory("Test result file");

		$this->phpExcel->getActiveSheet()
		    ->getHeaderFooter()->setOddHeader('&R &P / &N');
		$this->phpExcel->getActiveSheet()
		    ->getHeaderFooter()->setEvenHeader('&R &P / &N');

		foreach($header AS $key => $value)
		{
			if($value == "UPC")
			{
				$this->sheet->getColumnDimension($key)->setWidth('15');
			}
			else
			{
				if($value == "VDR ITEM #")
				{
					$this->sheet->getColumnDimension($key)->setWidth('10');
				}
				else
				{
					if($value == "BRAND")
					{
						$this->sheet->getColumnDimension($key)->setWidth('13');
					}
					else
					{
						if($value == "ITEM DESCRIPTION")
						{
							$this->sheet->getColumnDimension($key)->setWidth('26');
						}
						else
						{
							if($value == "TPR END")
							{
								$this->sheet->getColumnDimension($key)->setWidth('8');
							}
							else
							{
								if($reportType == "dptList")
								{
									$this->sheet->getColumnDimension("B")->setWidth('60');
								}
								else
								{
									$this->sheet->getColumnDimension($key)->setAutoSize(true);
								}
							}
						}
					}
				}
				
			}
			$this->sheet->setCellValue($key."3", $value);
		}
	}


	private function getLastArrayKey($header)
	{
		$last = "A";
		foreach($header as $key => $value)
		{
			$last = $key;
		}
		return $last;
	}


	private function setReport($header, $report)
	{
		$j = 4;
		$lastKey = $this->getLastArrayKey($header);
		for ($i=0;$i<count($report);$i++)
		{
			foreach($header as $key => $value)
			{
				$this->sheet->getStyle($key . $j) ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				if($this->columns[$value] != "UPC")
				{
					if(($value == "TPR PRICE" && $report[$i]["tpr"] == ".00")
					|| ($value == "TPR START" && $report[$i]["tpr"] == ".00") 
					|| ($value == "TPR END" && $report[$i]["tpr"] == ".00"))
					{
						$this->sheet->setCellValue($key . $j, "");
						if($value == "TPR PRICE")
				        {
				        	$this->sheet->getStyle($key . $j)->getFont()->setBold(true);
				        }
					}
					else   
					{
						if($value == "CASE COST")
				        {
				        	$this->sheet->getStyle($key . $j)->getFont()->setBold(true);
				        	$this->sheet->setCellValue($key . $j, number_format($report[$i][$this->columns[$value]], 2, ".", ""));
				        }
				        else
				        {	
				        	if($value == "UNIT PRICE")
				        	{
				        		$this->sheet->setCellValue($key . $j, number_format($report[$i][$this->columns[$value]], 2, ".", ""));
				        		$this->sheet->getStyle($key . $j)->getFont()
							    ->getColor()->setRGB('0066CC');
				        		$this->sheet->getStyle($key . $j)->getFont()->setBold(true);
				        	}
				        	else
				        	{
				        		if($value == "RETAIL")
						        {
						        	$this->sheet->getStyle($key . $j)->getFont()->setBold(true);
						        }
				        		$this->sheet->setCellValue($key . $j, $report[$i][$this->columns[$value]]);
				        	}
				        }
					}
				}
		        else
		        {
		        	$this->sheet->getStyle($key . $j)->getNumberFormat()->setFormatCode('0000000000000');
		        	$this->sheet->setCellValue($key . $j, $report[$i][$this->columns[$value]]);
		        }
		        if($this->columns[$value] == "CertCode")
				{
					$this->sheet->setCellValue($key . $j, trim($report[$i][$this->columns[$value]]));
				}
		        if($value != "ITEM DESCRIPTION")
		        {
		        	$this->sheet->getStyle($key . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		        }
		        if($value == "ON-HAND")
		        {
		        	if($report[$i][$this->columns[$value]] < 0)
		        	{
		        		$this->sheet->getStyle($key . $j)->getFont()
						    ->getColor()->setRGB('FF0000');
			        		$this->sheet->getStyle($key . $j)->getFont()->setBold(true);
		        	}
		        }
			} 
			$j = $j + 1;
		}
		$j = $j - 1;
		$this->sheet->getStyle('A3:'.$lastKey.$j)->getFont()->setSize(8);
		$styleArray = array( 'borders' => array( 'allborders' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000'), ), ), ); 
		$this->phpExcel->getActiveSheet()->getStyle('A1:'.$lastKey.$j)->applyFromArray($styleArray);
	}

	private function setReportWithSection($header, $report)
	{
		$j = 4;
		$lastKey = $this->getLastArrayKey($header);
		$alphabet = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
		$start = $alphabet[array_search($this->getItemDescriptionColumn($header), $alphabet) - 1];
		$current =  $this->getItemDescriptionColumn($header);
		$finish = $alphabet[array_search($this->getItemDescriptionColumn($header), $alphabet) + 1];
		$increment = 0;
		$condition = 'ht';
		for ($i=0; $i<count($report); $i++)
		{
			if($increment == 0 || $condition != $report[$i]["SctNo"])
			{
				$this->sheet->mergeCells('A' . $j . ':' . $start . $j);
				$this->sheet->setCellValue($current . $j, $report[$i]['SctNo'].' - '.$report[$i]['SctName']);
				$condition = $report[$i]["SctNo"];
				$this->sheet->getStyle($current . $j)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$this->sheet->getStyle($current . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->sheet->getStyle($current . $j)->getFont()->setBold(true);
				$this->sheet->mergeCells($finish . $j . ':' . $this->getLastArrayKey($header) . $j);
				$this->phpExcel->getActiveSheet()
				    ->getStyle($current . $j)
				    ->getFill()
				    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
				    ->getStartColor()
				    ->setARGB('FFE0E0E0');
				$j = $j + 1;
			}
			foreach($header as $key => $value)
			{
				$this->sheet->getStyle($key . $j) ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				if($this->columns[$value] != "UPC")
				{
					if(($value == "TPR PRICE" && $report[$i]["tpr"] == ".00")
					|| ($value == "TPR START" && $report[$i]["tpr"] == ".00") 
					|| ($value == "TPR END" && $report[$i]["tpr"] == ".00"))
					{
						if($value == "TPR PRICE")
				        {
				        	$this->sheet->getStyle($key . $j)->getFont()->setBold(true);
				        }
						$this->sheet->setCellValue($key . $j, " ");
					}
					else
					{
						if($value == "CASE COST")
				        {
				        	$this->sheet->getStyle($key . $j)->getFont()->setBold(true);
				        	$this->sheet->setCellValue($key . $j, number_format($report[$i][$this->columns[$value]], 2, ".", ""));
				        }
				        else
				        {
				        	if($value == "UNIT PRICE")
				        	{
				        		$this->sheet->getStyle($key . $j)->getFont()
							    ->getColor()->setRGB('0066CC');
				        		$this->sheet->getStyle($key . $j)->getFont()->setBold(true);
				        		$this->sheet->setCellValue($key . $j, number_format($report[$i][$this->columns[$value]], 2, ".", ""));
				        	}
				        	else
				        	{
				        		if($value == "RETAIL" || $value == "CASE COST")
						        {
						        	$this->sheet->getStyle($key . $j)->getFont()->setBold(true);
						        }
				        		$this->sheet->setCellValue($key . $j, $report[$i][$this->columns[$value]]);
				        	}
				        }
					}
				}
		        else
		        {
		        	$this->sheet->getStyle($key . $j)->getNumberFormat()->setFormatCode('0000000000000');
		        	$this->sheet->setCellValue($key . $j, $report[$i][$this->columns[$value]]);
		        }
		        if($this->columns[$value] == "CertCode")
				{
					$this->sheet->setCellValue($key . $j, trim($report[$i][$this->columns[$value]]));
				}
		        if($value != "ITEM DESCRIPTION")
		        {
		        	$this->sheet->getStyle($key . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		        }
		        if($value == "ON-HAND")
		        {
		        	if($report[$i][$this->columns[$value]] < 0)
		        	{
		        		$this->sheet->getStyle($key . $j)->getFont()
					    ->getColor()->setRGB('FF0000');
		        		$this->sheet->getStyle($key . $j)->getFont()->setBold(true);
		        	}
		        }
			} 
			$j = $j + 1;
			$increment = 1;
		}
		$j = $j - 1;
		$this->sheet->getStyle('A3:'.$lastKey.$j)->getFont()->setSize(8);
		$styleArray = array( 'borders' => array( 'allborders' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000'), ), ), ); 
		$this->phpExcel->getActiveSheet()->getStyle('A1:'.$lastKey.$j)->applyFromArray($styleArray);
	}

	private function setCompareReport($header, $report)
	{
		$j = 4;
		$lastKey = $this->getLastArrayKey($header);
		$alphabet = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
		$start = $alphabet[array_search($this->getItemDescriptionColumn($header), $alphabet) - 1];
		$current =  $this->getItemDescriptionColumn($header);
		$finish = $alphabet[array_search($this->getItemDescriptionColumn($header), $alphabet) + 1];
		$increment = 0;
		$condition = 'ht';
		for ($i=0; $i<count($report); $i++)
		{
			if($increment == 0 || $condition != $report[$i]["SctNo"])
			{
				$this->sheet->mergeCells('A' . $j . ':' . $start . $j);
				$this->sheet->setCellValue($current . $j, $report[$i]['SctNo'].' - '.$report[$i]['SctName']);
				$condition = $report[$i]["SctNo"];
				$this->sheet->getStyle($current . $j)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$this->sheet->getStyle($current . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->sheet->getStyle($current . $j)->getFont()->setBold(true);
				$this->sheet->mergeCells($finish . $j . ':' . $this->getLastArrayKey($header) . $j);
				$this->phpExcel->getActiveSheet()
				    ->getStyle($current . $j)
				    ->getFill()
				    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
				    ->getStartColor()
				    ->setARGB('FFE0E0E0');
				$j = $j + 1;
			}
			foreach($header as $key => $value)
			{
				if($this->columns[$value] == "UPC" || $this->columns[$value] == "Brand" || $this->columns[$value] == "ItemDescription"
					|| $this->columns[$value] == "sales" || $this->columns[$value] == "onhand"
					|| $this->columns[$value] == "tpr" || $this->columns[$value] == "tprStart" || $this->columns[$value] == "tprEnd")
				{
					$this->sheet->getStyle($key . $j) ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
					$this->sheet->mergeCells($key . $j . ":" . $key . ($j+1));
					// $cell = $this->sheet->Range($key . $j . ":" . $key . ($j+1));
					if($this->columns[$value] == "UPC")
					{
						$this->sheet->getStyle($key . $j)->getNumberFormat()->setFormatCode('0000000000000');
		        		$this->sheet->setCellValue($key . $j, $report[$i][$this->columns[$value]]);
					}
			        else
			        {
			        	if(($value == "TPR PRICE" && $report[$i]["tpr"] == ".00")
						|| ($value == "TPR START" && $report[$i]["tpr"] == ".00") 
						|| ($value == "TPR END" && $report[$i]["tpr"] == ".00"))
						{
							$this->sheet->setCellValue($key . $j, " ");
						}
						else
						{
							if($value == "CASE COST")
					        {
					        	$this->sheet->getStyle($key . $j)->getFont()->setBold(true);
					        	$this->sheet->setCellValue($key . $j, number_format($report[$i][$this->columns[$value]], 2, ".", ""));
					        }
					        else
					        {
					        	if($value == "UNIT PRICE")
					        	{
					        		$this->sheet->getStyle($key . $j)->getFont()
								    ->getColor()->setRGB('0066CC');
					        		$this->sheet->getStyle($key . $j)->getFont()->setBold(true);
					        		$this->sheet->setCellValue($key . $j, number_format($report[$i][$this->columns[$value]], 2, ".", ""));
					        	}
					        	else
					        	{
					        		if($value == "TPR PRICE" || $value == "RETAIL")
							        {
							        	$this->sheet->getStyle($key . $j)->getFont()->setBold(true);
							        }
					        		$this->sheet->setCellValue($key . $j, $report[$i][$this->columns[$value]]);
					        	}
					        }
						}
			        }
			        if($value != "ITEM DESCRIPTION")
			        {
			        	$this->sheet->getStyle($key . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			        }
			        if($value == "ON-HAND")
			        {
			        	if($report[$i][$this->columns[$value]] < 0)
			        	{
			        		$this->sheet->getStyle($key . $j)->getFont()
						    ->getColor()->setRGB('FF0000');
			        		$this->sheet->getStyle($key . $j)->getFont()->setBold(true);
			        	}
			        }
				}
				else
				{
					if($report[$i]["unitPriceOne"] <= $report[$i]["unitPriceTwo"])
					{
						if($this->columns[$value] == "CaseCost" || $this->columns[$value] == "unitPrice")
						{
							$this->sheet->getStyle($key . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->sheet->getStyle($key . ($j+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->sheet->setCellValue($key . $j, number_format($report[$i][$this->columns[$value]."One"], 2, ".", ''));
							$this->sheet->setCellValue($key . ($j+1), number_format($report[$i][$this->columns[$value]."Two"], 2, ".", ''));
						}
						else
						{
							$this->sheet->getStyle($key . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->sheet->getStyle($key . ($j+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->sheet->setCellValue($key . $j, $report[$i][$this->columns[$value]."One"]);
							$this->sheet->setCellValue($key . ($j+1), $report[$i][$this->columns[$value]."Two"]);
						}
						
					}
					else
					{
						if($this->columns[$value] == "CaseCost" || $this->columns[$value] == "unitPrice")
						{
							$this->sheet->getStyle($key . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->sheet->getStyle($key . ($j+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->sheet->setCellValue($key . ($j+1), number_format($report[$i][$this->columns[$value]."One"], 2, ".", ''));
							$this->sheet->setCellValue($key . $j, round($report[$i][$this->columns[$value]."Two"]));
						}
						else
						{
							$this->sheet->getStyle($key . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->sheet->getStyle($key . ($j+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->sheet->setCellValue($key . ($j+1), $report[$i][$this->columns[$value]."One"]);
							$this->sheet->setCellValue($key . $j, $report[$i][$this->columns[$value]."Two"]);
						}
					}
					if($this->columns[$value] == "unitPrice")
					{
						$this->sheet->getStyle($key . $j)->getFont()
								    ->getColor()->setRGB('0066CC');
					    $this->sheet->getStyle($key . $j)->getFont()->setBold(true);

					    $this->sheet->getStyle($key . ($j+1))->getFont()
								    ->getColor()->setRGB('0066CC');
					    $this->sheet->getStyle($key . ($j+1))->getFont()->setBold(true);
					}
					if($this->columns[$value] == "Retail" || $this->columns[$value] == "CaseCost")
					{
					    $this->sheet->getStyle($key . $j)->getFont()->setBold(true);

					    $this->sheet->getStyle($key . ($j+1))->getFont()->setBold(true);
					}
				}
			} 
			$j = $j + 2;
			$increment = 1;
		}
		$j = $j - 1;
		$this->sheet->getStyle('A3:'.$lastKey.$j)->getFont()->setSize(8);
		$styleArray = array( 'borders' => array( 'allborders' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000'), ), ), ); 
		$this->phpExcel->getActiveSheet()->getStyle('A1:'.$lastKey.$j)->applyFromArray($styleArray);
	}

	private function SaveReport($documentName)
	{
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="' . $documentName . '.xls"'); 
		header('Cache-Control: max-age=0'); $objWriter = PHPExcel_IOFactory::createWriter($this->phpExcel, 'Excel5'); 
		$objWriter->save('php://output');
	}
}