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

	protected $memcache;


	public function __construct()
	{
		parent::__construct();
		// date_default_timezone_set('America/Port-au-Prince');
		$this->today = date('Y-m-d', strtotime("-1 days"));
		// $this->cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory; 
		$this->phpExcel = $this->phpExcel();
		$this->phpExcel->createSheet();
		$this->sheet = $this->phpExcel->getActiveSheet();
		$this->users = $this->model('users');
		$this->brdata = $this->model('brdata');
		$this->today = date('Y-m-d', strtotime("-1 days"));
		$this->columnWidths = array("UPC" => 13, "VDR ITEM #" => 11, "BRAND" => 10, "ITEM DESCRIPTION" => 30, "PACK" => 6, "SIZE" => 8, 
			"CASE COST" => 10, "RETAIL" => 7, "ON-HAND" => 8, "LAST REC" => 12, "ADJUSTMENT" => 12, "LAST REC DATE" => 15, "ADJUSTMENT DATE" => 15, "SALES" => 5, "VDR #" => 7, "VDR NAME" => 22, 
			"TPR PRICE" => 7, "TPR START" => 8, "TPR END" => 8, "SCT NO" => 8, "SCT NAME" => 30, "DPT NO" => 8, "DPT NAME" => 30, "UNIT PRICE" => 10);
		$this->columns = array("UPC" => "UPC", "VDR ITEM #" => "CertCode", "BRAND" => "Brand", "ITEM DESCRIPTION" => "ItemDescription", "PACK" => "Pack", "SIZE" => "SizeAlpha", "CASE COST" => "CaseCost", "RETAIL" => "Retail", 
			"ON-HAND" => "onhand", "LAST REC" => "lastReceiving", "LAST REC DATE" => "lastReceivingDate", "SALES" => "sales", "VDR #" => "VdrNo", "VDR NAME" => "VdrName", "TPR PRICE" => "tpr", "TPR START" => "tprStart", 
			"TPR END" => "tprEnd", "SCT NO" => "SctNo", "SCT NAME" => "SctName", "DPT NO" => "DptNo", "DPT NAME" => "DptName", "UNIT PRICE" => "unitPrice", "ADJUSTMENT" => "adj", "ADJUSTMENT DATE" => "Date");
	} 

	public function microtime_float()
	{
	    list($usec, $sec) = explode(" ", microtime());
	    return ((float)$usec + (float)$sec);
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
						"J" => "LAST REC", 
						"K" => "LAST REC DATE", 
						"L" => "SALES", 
						"M" => "TPR PRICE", 
						"N" => "TPR START", 
						"O" => "TPR END");
		$this->setSheetName("VENDOR REPORT");
		$bold = array("G", "M", "H", "I");
		$unit_price_col = "";
		$report = $this->brdata->get_vendorReport($vendor, $this->today, $from, $to);
		$lastItem = count($report) + 4;
		$this->setHeader("VENDOR SECTION FINAL REPORT","[ VENDOR : " . $report[0]['VdrNo'] . " - ".$report[0]['VdrName']." ] - [ 
			".$from." - ".$to." ]"." - [ ".count($report)." ITEMS ]", $header, 'vdrReport', $lastItem);
		$this->setReportWithSection($header, $report, $bold, $unit_price_col);
		$this->saveReport('VendorSectionFinal_'.$report[0]['VdrNo'].'_'.$report[0]['VdrName'].'_'.$this->today);
	}

	public function limitedVendor($vendor, $from, $to)
	{
		$header = array("A" => "UPC", 
						"B" => "VDR ITEM #", 
						"C" => "ITEM DESCRIPTION", 
						"D" => "PACK", 
						"E" => "SIZE", 
						"F" => "RETAIL", 
						"G" => "ON-HAND", 
						"H" => "RETAIL", 
						"I" => "ON-HAND");
		$this->setSheetName("VENDOR LIMITED REPORT");
		$bold = array("G");
		$unit_price_col = "";
		$report = $this->brdata->get_vendorReport($vendor, $this->today, $from, $to);
		$lastItem = count($report) + 4;
		$this->setHeader("VENDOR LIMITED REPORT","[ VENDOR : " . $report[0]['VdrNo'] . " - ".$report[0]['VdrName']." ] - [ 
			".$from." - ".$to." ]"." - [ ".count($report)." ITEMS ]", $header, 'vdrReport', $lastItem);
		$this->setReport($header, $report, $bold, $unit_price_col);
		$this->saveReport('VendorLimited_'.$report[0]['VdrNo'].'_'.$report[0]['VdrName'].'_'.$this->today);
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
						"J" => "LAST REC", 
						"K" => "LAST REC DATE", 
						"L" => "SALES", 
						"M" => "TPR PRICE", 
						"N" => "TPR START", 
						"O" => "TPR END");
		$this->setSheetName("VENDOR NEGATIVE ON-HAND REPORT");
		$report = $this->brdata->get_vendorReport($vendor, $this->today, $from, $to);
		$bold = array("G", "M", "H", "I");
		$lastItem = count($report) + 4;
		$this->setHeader("VENDOR SECTION FINAL REPORT WITH NEGATIVE ON-HAND","[ VENDOR : " . $vendor . " - ".$report[0]['VdrName']." ] - [ 
			".$from." - ".$to." ]"." - [ ".count($report)." ITEMS ]", $header, 'vdrReport', $lastItem);
		$this->setReportWithSectionNegative($header, $report, $bold, "A", "", "D");
		$this->saveReport('VendorSectionFinalNegative_'.$vendor.'_'.$report[0]['VdrName'].'_'.$this->today);
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
						"J" => "LAST REC", 
						"K" => "LAST REC DATE", 
						"L" => "SALES", 
						"M" => "TPR PRICE", 
						"N" => "TPR START", 
						"O" => "TPR END");
		$this->setSheetName("VENDOR REPORT");
		$vendorReport = $this->brdata->get_vendorReport($vendor, $this->today, $from, $to);
		$j=0;
		$i=0;
		foreach($vendorReport as $key => $value)
		{
			if($value['sales'] != NULL || $value['onhand'] == ".0000")
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
		$bold = array("G", "M", "H", "I");
		$this->setHeader("VENDOR MOVEMENT REPORT","[ VENDOR : " . $vendor . " - ".$report[0]['VdrName']." ] - [ 
			".$from." - ".$to." ]"." - [ ".count($report)." ITEMS ]", $header, 'vdrReport', $lastItem);
		$this->setReportWithSection($header, $report, $bold, "A", "", "D");
		$this->saveReport('VendorMovement_'.$vendor.'_'.$report[0]['VdrName'].'_'.$this->today);
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
						"K" => "LAST REC", 
						"L" => "LAST REC DATE", 
						"M" => "SALES", 
						"N" => "TPR PRICE", 
						"O" => "TPR START", 
						"P" => "TPR END");
		$this->setSheetName("TPR REPORT");
		$report = $this->brdata->get_specialReport($this->today, $from, $to);
		$bold = array("G", "N", "H", "I", "J");
		$lastItem = count($report) + 4;
		$this->setHeader("TPR SPECIALS REPORT", " [  " . $from . " - " . $to . " ]"." - [ ".count($report)." ITEMS ]", $header, 'specials_r', $lastItem, $lastItem);
		$this->setReportWithSection($header, $report, $bold, "A", "H", "D");
		$this->saveReport('TPRReport_'.$this->today);
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
						"J" => "LAST REC", 
						"K" => "LAST REC DATE", 
						"L" => "SALES", 
						"M" => "VDR #", 
						"N" => "VDR NAME", 
						"O" => "TPR PRICE", 
						"P" => "TPR START", 
						"Q" => "TPR END");
		$this->setSheetName("DEPARTMENT REPORT");
		$report = $this->brdata->get_departmentReport($department, $this->today, $from, $to);
		$lastItem = count($report) + 4;
		$bold = array("G", "H", "I", "O");
		$this->setHeader("DEPARTMENT REPORT", "[ DPT " . $department . " - " . $report[0]['DptName'] . " ] - 
			 [ ".$from." - ".$to. " ]"." - [ ".count($report)." ITEMS ]", $header, "dptReport", $lastItem);
		$this->setReportWithSection($header, $report, $bold, "A", "", "D");
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
						"J" => "LAST REC", 
						"K" => "LAST REC DATE", 
						"L" => "SALES", 
						"M" => "TPR PRICE", 
						"N" => "TPR START", 
						"O" => "TPR END");
		$this->setSheetName("VENDOR DEPARTMENT REPORT");
		$report = $this->brdata->get_vendorDepartmentReport($vendor, $department, $this->today, $from, $to);
		$lastItem = count($report) + 4;
		$bold = array("G", "H", "I", "M");
		$this->setHeader("VENDOR DEPARTMENT REPORT" ,"[ VENDOR : " . $vendor . " -  ".$report[0]['VdrName']." ] - [ DPT : 
			" . $department . " - " . $report[0]['DptName'] . "] - [ ".$from." - ".$to."]"." - [ ".count($report)." ITEMS ]", $header, "vdrDpt", $lastItem);
		$this->setReportWithSection($header, $report, $bold, "A", "", "D");
		$this->saveReport('VendorDepartment'.$report[0]['VdrName'].'_'.$report[0]['DptName'].'_'.$this->today);
	}

	public function vendorDepartmentNegative($vendor, $department, $from, $to)
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
						"J" => "LAST REC", 
						"K" => "LAST REC DATE", 
						"L" => "SALES", 
						"M" => "TPR PRICE", 
						"N" => "TPR START", 
						"O" => "TPR END");
		$this->setSheetName("VENDOR DEPARTMENT REPORT");
		$report = $this->brdata->get_vendorDepartmentReport($vendor, $department, $this->today, $from, $to);
		$lastItem = count($report) + 4;
		$bold = array("G", "H", "I", "M");
		$this->setHeader("VENDOR DEPARTMENT NEGATIVE REPORT" ,"[ VENDOR : " . $vendor . " -  ".$report[0]['VdrName']." ] - [ DPT : 
			" . $department . " - " . $report[0]['DptName'] . "] - [ ".$from." - ".$to."]"." - [ ".count($report)." ITEMS ]", $header, "vdrDpt", $lastItem);
		$this->setReportWithSectionNegative($header, $report, $bold, "A", "", "D");
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
						"J" => "LAST REC", 
						"K" => "LAST REC DATE", 
						"L" => "SALES", 
						"M" => "VDR #", 
						"N" => "VDR NAME", 
						"O" => "TPR PRICE", 
						"P" => "TPR START", 
						"Q" => "TPR END");
		$this->setSheetName("UPC RANGE REPORT");
		$report = $this->brdata->get_upcRangeReport($upc1, $upc2, $this->today, $to, $from);
		$lastItem = count($report) + 4;
		$bold = array("G", "H", "I", "O");
		$this->setHeader("UPC RANGE REPORT" ,"[ UPC 1 : " . $upc1 . " / UPC2 : 
			" . $upc2 . " ] - [ ".$from." - ".$to." ]"." - [ ".count($report)." ITEMS ]", $header, "upcRange", $lastItem);
		$this->setReportWithSection($header, $report, $bold, "A", "", "D");
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
						"J" => "LAST REC", 
						"K" => "LAST REC DATE", 
						"L" => "SALES", 
						"M" => "VDR #", 
						"N" => "VDR NAME", 
						"O" => "TPR PRICE", 
						"P" => "TPR START", 
						"Q" => "TPR END");
		$this->setSheetName("ITEM DESCRIPTION REPORT");
		$description = str_replace("_", " ", $description);
		$report = $this->brdata->get_itemDescription($description, $this->today, $to, $from);
		$bold = array("G", "H", "I", "O");
		$lastItem = count($report) + 4;
		$this->setReportWithSection($header, $report, $bold, "A", "", "D");
		$this->setHeader("ITEM DESCRIPTION REPORT", "[ ". $description." ] [ FROM ".$from." TO ".$to." ]"." - [ ".count($report)." ITEMS ]", $header, "itemDescription", $lastItem);
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
						"J" => "LAST REC", 
						"K" => "LAST REC DATE", 
						"L" => "SALES", 
						"M" => "VDR #", 
						"N" => "VDR NAME", 
						"O" => "TPR PRICE", 
						"P" => "TPR START", 
						"Q" => "TPR END");
		$this->setSheetName("SECTION REPORT");
		$report = $this->brdata->get_sectionReport($section, $this->today, $from, $to);
		$bold = array("G", "H", "I", "O");
		$lastItem = count($report) + 4;
		$this->setHeader("SECTION REPORT" ," [SCT : ".$section." - " . $report[0]['SctName'] . " ] [ ".$from." - ".$to." ]"." - [ ".count($report)." ITEMS ]", $header, "sctReport", $lastItem);
		$this->setReport($header, $report, $bold, "A", "", "D");
		// print_r(array("Memcache" => $timeMemcache, "PHPExcel" => $timePHPExcel));
		$this->saveReport('Section_' . $report[0]['SctName'] . '_' . $this->today);
	}

	public function multipleSections($sections, $from, $to){
		$header = array("A" => "UPC", 
						"B" => "VDR ITEM #", 
						"C" => "BRAND", 
						"D" => "ITEM DESCRIPTION", 
						"E" => "PACK", 
						"F" => "SIZE", 
						"G" => "CASE COST", 
						"H"	 => "RETAIL", 
						"I" => "ON-HAND", 
						"J" => "LAST REC", 
						"K" => "LAST REC DATE", 
						"L" => "SALES", 
						"M" => "VDR #", 
						"N" => "VDR NAME", 
						"O" => "TPR PRICE", 
						"P" => "TPR START", 
						"Q" => "TPR END");
		$this->setSheetName("MULTIPLE SECTIONS REPORT");
		$secArray = explode("_", $sections);
		$report = $this->brdata->get_multipleSectionReport($secArray, $this->today, $from, $to);
		$bold = array("G", "H", "I", "O");
		$lastItem = count($report) + 4;
		$this->setHeader("MULTIPLE SECTIONS REPORT" ," [ SCT : ".$sections." ] [ ".$from." - ".$to." ]"." - [ ".count($report)." ITEMS ]", $header, "sctReport", $lastItem);
		$this->setReportWithSection($header, $report, $bold, "A", "", "D");
		// print_r(array("Memcache" => $timeMemcache, "PHPExcel" => $timePHPExcel));
		$this->saveReport('MultipleSections_' . $sections . '_' . $this->today);
	}

	public function multipleSectionsNeg($sections, $from, $to){
		$header = array("A" => "UPC", 
						"B" => "VDR ITEM #", 
						"C" => "BRAND", 
						"D" => "ITEM DESCRIPTION", 
						"E" => "PACK", 
						"F" => "SIZE", 
						"G" => "CASE COST", 
						"H"	 => "RETAIL", 
						"I" => "ON-HAND", 
						"J" => "LAST REC", 
						"K" => "LAST REC DATE", 
						"L" => "SALES", 
						"M" => "VDR #", 
						"N" => "VDR NAME", 
						"O" => "TPR PRICE", 
						"P" => "TPR START", 
						"Q" => "TPR END");
		$this->setSheetName("MULTIPLE SECTIONS REPORT");
		$secArray = explode("_", $sections);
		$report = $this->brdata->get_multipleSectionNegReport($secArray, $this->today, $from, $to);
		$bold = array("G", "H", "I", "O");
		$lastItem = count($report) + 4;
		$this->setHeader("MULTIPLE SECTIONS REPORT" ," [SCT : ".$sections." ] [ ".$from." - ".$to." ]"." - [ ".count($report)." ITEMS ]", $header, "sctReport", $lastItem);
		$this->setReportWithSectionNegativeRepeat($header, $report, $bold, "A", "", "D");
		// print_r(array("Memcache" => $timeMemcache, "PHPExcel" => $timePHPExcel));
		$this->saveReport('MultipleSections_' . $sections . '_' . $this->today);
	}

	public function sectionNegative($section, $from, $to)
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
						"J" => "LAST REC", 
						"K" => "LAST REC DATE", 
						"L" => "SALES", 
						"M" => "VDR #", 
						"N" => "VDR NAME", 
						"O" => "TPR PRICE", 
						"P" => "TPR START", 
						"Q" => "TPR END");
		$this->setSheetName("SECTION NEGATIVE REPORT");
		$report = $this->brdata->get_sectionNegReport($section, $this->today, $from, $to);
		$bold = array("G", "H", "I", "O");
		$lastItem = count($report) + 4;
		$this->setHeader("SECTION NEGATIVE REPORT" ," [SCT : ".$section." - " . $report[0]['SctName'] . " ] [ ".$from." - ".$to." ]", $header, "sctReport", $lastItem);
		$this->setReportNegative($header, $report, $bold, "A", "", "D", "sectionNeg");
		// print_r(array("Memcache" => $timeMemcache, "PHPExcel" => $timePHPExcel));
		$this->saveReport('SectionNegative_' . $report[0]['SctName'] . '_' . "_". $section . "_" . $this->today);
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
						"J" => "LAST REC", 
						"K" => "LAST REC DATE", 
						"L" => "SALES", 
						"M" => "TPR PRICE", 
						"N" => "TPR START", 
						"O" => "TPR END");
		$this->setSheetName("VENDOR SECTION NEGATIVE ON-HAND REPORT");
		$report = $this->brdata->get_vendorSectionReport($vendor, $section, $this->today, $to, $from);
		$bold = array("G", "H", "I", "M");
		$lastItem = count($report) + 4;
		$this->setHeader("VENDOR SECTION REPORT WITH NEGATIVE ON-HAND","[ VENDOR : " . $vendor . " - ".$report[0]['VdrName'] ." ] - [ SECTION : " . $section . " - " . $report[0]['SctName'] . "] - [ 
			" . $from . " - " . $to . " ]"." - [ ".count($report)." ITEMS ]", $header, 'vdrSctNegativeReport', $lastItem);
		$this->setReportNegative($header, $report, $bold, "A", "", "D");
		$this->saveReport('VendorSectionNegative_'.$vendor.'_'.$report[0]['VdrName'].'_'.$this->today);
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
						"H" => "RETAIL", 
						"I" => "ON-HAND", 
						"J" => "LAST REC", 
						"K" => "LAST REC DATE", 
						"L" => "SALES", 
						"M" => "VDR #", 
						"N" => "VDR NAME", 
						"O" => "TPR PRICE", 
						"P" => "TPR START", 
						"Q" => "TPR END");
		$this->setSheetName("SECTION MOVEMENT REPORT");
		$vdrSctReport = $this->brdata->get_sectionReport($section, $this->today, $to, $from);
		$bold = array("G", "H", "I", "O");
		$j=0;
		$i=0;
		foreach($vdrSctReport as $key => $value)
		{
			if($value['sales'] != NULL || $value['onhand'] == ".0000")
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
	}

		public function departmentMovement($department, $from, $to)
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
						"J" => "LAST REC", 
						"K" => "LAST REC DATE", 
						"L" => "SALES", 
						"M" => "TPR PRICE", 
						"N" => "TPR START", 
						"O" => "TPR END",
						"P" => "VDR #",
						"Q" => "VDR NAME");
		$this->setSheetName("DEPARTMENT MOVEMENT REPORT");
		$vdrSctReport = $this->brdata->get_departmentReport($department, $this->today, $to, $from);
		$bold = array("G", "H", "I", "O");
		$j=0;
		$i=0;
		foreach($vdrSctReport as $key => $value)
		{
			if(!empty($value['lastReceivingDate'])){
						unset($vdrSctReport[$i]);
					}
			if(!empty($value['sales']) || ($value['onhand'] != ".0000" && $value['onhand'] != "99999"))
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
		$lastItem = count($report) + 4;
		$this->setHeader("DEPARTMENT MOVEMENT REPORT" ," [DPT : ".$department." - " . $report[0]['DptName'] . " ] [ ".$from." - ".$to." ]"." - [ ".count($report)." ITEMS ]", $header, "dptReport", $lastItem);
		$this->setReportWithSection($header, $report, $bold, "A", "", "D");
		$this->saveReport('DepartmentMovement_'.$department.'_' . $report[0]['DptName'] . '_' . $this->today);
	}

	public function adjustments()
	{
		$header = array("A" => "UPC", 
						"B" => "ITEM DESCRIPTION", 
						"C" => "SCT NO", 
						"D" => "SCT NAME", 
						"E" => "LAST REC", 
						"F" => "LAST REC DATE", 
						"G" => "ADJUSTMENT", 
						"H" => "ADJUSTMENT DATE");
		$this->setSheetName("ADJUSTMENTS REPORT");
		$report = $this->brdata->get_adjReport();
		$bold = array();
		$lastItem = count($report) + 4;
		$this->setHeader("ADJUSTMENTREPORT 2017/JUL/04" ,""." - [ ".count($report)." ITEMS ]", $header, "sctReport", $lastItem);
		$this->setReport($header, $report, $bold, "A", "", "B");
		$this->saveReport('adjustmentReport_'.$this->today);
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
						"J" => "LAST REC", 
						"K" => "LAST REC DATE", 
						"L" => "SALES", 
						"M" => "TPR PRICE", 
						"N" => "TPR START", 
						"O" => "TPR END");
		$this->setSheetName("VENDOR SECTION MOVEMENT REPORT");
		$vdrSctReport = $this->brdata->get_vendorSectionReport($vendor, $section, $this->today, $to, $from);
		$j=0;
		$i=0;
		foreach($vdrSctReport as $key => $value)
		{
			if($value['sales'] != NULL  || $value['onhand'] == ".0000")
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
		$lastItem = count($report) + 4;
		$bold = array("G", "H", "I", "M");
		$this->setHeader("VENDOR SECTION MOVEMENT REPORT" ,"[ VENDOR : " . $vendor . " - " . $report[0]['VdrName'] . 
			" ] - [ SECTION ".$section." - " . $report[0]['SctName'] . "] - [ ".$from." - ".$to." ]"." - [ ".count($report)." ITEMS ]", 
			$header, 'vdrSctReport', $lastItem);
		$this->setReport($header, $report, $bold, "A", "", "D");
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
						"J" => "LAST REC", 
						"K" => "LAST REC DATE", 
						"L" => "SALES", 
						"M" => "TPR PRICE", 
						"N" => "TPR START", 
						"O" => "TPR END",
						"P" => "VDR #", 
						"Q" => "VDR NAME");
		$this->setSheetName("UPC RECEIVING HISTORY");
		$report = $this->brdata->get_upcReceivingHistory($upc, $this->today, $to, $from);
		$lastItem = count($report) + 4;
		$bold = array("G", "H", "I", "M");
		$this->setHeader("UPC RECEIVING HISTORY","[ UPC : ".$upc." ] - [ 
			".$from." - ".$to." ]"." - [ ".count($report)." ITEMS ]", $header, 'upcReceivingReport', $lastItem);
		$this->setReport($header, $report, $bold, "A", "", "D");
		$this->saveReport('upcReceivingHistory_'.$upc.'_'.$this->today);
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
						"J" => "LAST REC", 
						"K" => "LAST REC DATE", 
						"L" => "SALES", 
						"M" => "TPR PRICE", 
						"N" => "TPR START", 
						"O" => "TPR END");
		$this->setSheetName("VENDOR SECTION REPORT");
		$report = $this->brdata->get_vendorSectionReport($vendor, $section, $this->today, $to, $from);
		$lastItem = count($report) + 4;
		$bold = array("G", "H", "I", "M");
		$this->setHeader("VENDOR SECTION REPORT" ,"[ VENDOR : " . $vendor . " - " . $report[0]['VdrName'] . 
			" ] - [ SECTION ".$section." - " . $report[0]['SctName'] . "] - [ ".$from." - ".$to." ]"." - [ ".count($report)." ITEMS ]", 
			$header, 'vdrSctReport', $lastItem);
		$this->setReport($header, $report, $bold, "A", "", "D");
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
						"M" => "LAST REC", 
						"N" => "LAST REC DATE", 
						"O" => "SALES", 
						"P" => "TPR PRICE", 
						"Q" => "TPR START", 
						"R" => "TPR END");
		$this->setSheetName("VENDOR UPC PRICE COMPARE REPORT");
		$report = $this->brdata->get_upcReport($upc, $this->today, $to, $from);
		$lastItem = count($report) + 4;
		$bold = array("J", "K", "I", "L", "P");
		$this->setHeader("UPC PRICE COMPARE REPORT" ,"[ UPC : " . $upc . " ] - [ ". $from . " - " . $to . " ]"." - [ ".count($report)." ITEMS ]", $header, "upcPriceCompare", $lastItem);
		$this->setReport($header, $report, $bold, "C", "J", "F");
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
						"L" => "LAST REC", 
						"M" => "LAST REC DATE", 
						"N" => "SALES", 
						"O" => "TPR PRICE", 
						"P" => "TPR START", 
						"Q" => "TPR END");
		$this->setSheetName("VENDOR ITEM CODE REPORT");
		$report = $this->brdata->get_itemcodeReport($code, $this->today, $to, $from);
		$lastItem = count($report) + 4;
		$bold = array("J", "K", "I", "O");
		$this->setHeader("VENDOR ITEM CODE REPORT" ,"[ ITEM CODE " . $code . " ] - [ ". $from . " - " . $to." ]"." - [ ".count($report)." ITEMS ]", $header, 'itemCode', $lastItem);
		$this->setReport($header, $report, $bold, "C", "", "F");
		$this->saveReport('VendorItemCode_' . $code . '_' . $this->today);
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
						"O" => "LAST REC", 
						"P" => "LAST REC DATE", 
						"Q" => "VDR #", 
						"R" => "VDR NAME");
		$this->setSheetName("VENDOR PRICE COMPARE REPORT");
		$report = $this->brdata->vendorPriceCompare($vendor1, $vendor2, $this->today, $from, $to);
		$lastItem = count($report) + 5;
		$bold = array("D", "K", "L", "M", "F");
		$this->setHeader("VENDOR PRICE COMPARE REPORT", " [ VENDORS : " . $vendor1 . " - ".$report[0]['VdrNameOne']." / " . $vendor2 . " - " .
			$report[0]['VdrNameTwo'] . " ] - [ ".$from." - ".$to." ]"." - [ ".count($report)." ITEMS ]", $header, "vdrPriceCompare", $lastItem);
		$this->setCompareReport($header, $report, $bold, "A", "L", "C");
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
						"O" => "LAST REC", 
						"P" => "LAST REC DATE", 
						"Q" => "VDR #", 
						"R" => "VDR NAME");
		$this->setSheetName("SECTION PRICE COMPARE REPORT");
		$report = $this->brdata->sectionPriceCompare($vendor1, $vendor2, $section, $this->today, $from, $to);
		$bold = array("D", "K", "L", "M", "F");
		$lastItem = count($report) + 5;
		$this->setHeader("VENDOR PRICE COMPARE PER SECTION", " [ VENDORS : " . $vendor1 . " - ".$report[0]['VdrNameOne']." / " . $vendor2 . " - " .
			$report[0]['VdrNameTwo'] . " ] - [ SECTION : " . $section . " - " . $report[0]['SctName'] . " ] - [ ".$from." - ".$to." ]"." - [ ".count($report)." ITEMS ]", $header, "vdrPriceCompare", $lastItem);
		$this->setCompareReport($header, $report, $bold, "A", "L", "C");
		$this->saveReport('SectionPriceCompare_'.$report[0]['VdrNameOne'].'_'.$report[0]['VdrNameTwo'] .'_'.$section."_".$this->today);
	}

	public function sectionNames()
	{
		$header = array("A" => "SCT NO", "B" => "SCT NAME", "C" => "DPT NO", "D" => "DPT NAME");
		$this->setSheetName("SECTION NAMES REPORT");
		$report = $this->brdata->get_sectionNames();
		$bold = array();
		$lastItem = count($report) + 4;
		$this->setHeader("SECTION NAMES REPORT", "LIST OF STORE SECTIONS", $header, "sctList", $lastItem, "ORIENTATION_PORTRAIT");
		$this->setReport($header, $report, $bold, "E", "E", "B");
		$this->saveReport('SectionNames_' . $this->today);
	}

	public function departmentNames()
	{
		$header = array("A" => "DPT NO", "B" => "DPT NAME");
		$this->setSheetName("DEPARTMENT NAMES REPORT");
		$bold = array();
		$report = $this->brdata->get_departmentNames();
		$lastItem = count($report) + 4;
		$this->setHeader("DEPARTMENT NAMES REPORT", "LIST OF STORE DEPARTMENTS", $header, 'dptList', $lastItem, "ORIENTATION_PORTRAIT");
		$this->setReport($header, $report, $bold, "E", "E", "E");
		$this->saveReport('DepartmentNames_' . $this->today);
	}

	public function vendorNames()
	{
		$header = array("A" => "VDR #", "B" => "VDR NAME");
		$this->setSheetName("VENDOR NAMES REPORTS");
		$report = $this->brdata->get_vendorNames();
		$bold = array();
		$lastItem = count($report) + 4;
		$this->setHeader("VENDOR NAMES REPORT", "LIST OF VENDORS", $header, 'vdrList', $lastItem, "ORIENTATION_PORTRAIT");
		$this->setReport($header, $report, $bold, "E", "E", "E");
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

	private function setHeader($title, $subtitle, $header, $reportType, $lastItem, $orientation = "ORIENTATION_LANDSCAPE")
	{
		$myWorkSheet = new PHPExcel_Worksheet($this->phpExcel, $reportType); 
		// Attach the “My Data” worksheet as the first worksheet in the PHPExcel object 
		$lastKey = $this->getLastArrayKey($header);
		$this->phpExcel->addSheet($myWorkSheet, 0);
		// Set report to landscape 
		if($orientation == "ORIENTATION_PORTRAIT"){
			$this->phpExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
		}
		else{
			$this->phpExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		}
		

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


	private function setReport($header, $report, $bold, $upc_col = "A", $unit_price_col = "", $itemDescription = "D")
	{
		$j = 4;
		$lastKey = $this->getLastArrayKey($header);
		for ($i=0;$i<count($report);$i++)
		{
			foreach($header as $key => $value)
			{
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
						if($value == "CASE COST" || $value == "UNIT PRICE")
				        {
				        	$this->sheet->setCellValue($key . $j, number_format($report[$i][$this->columns[$value]], 2, ".", ""));
				        }
				        else
				        {	
				        	$this->sheet->setCellValue($key . $j, $report[$i][$this->columns[$value]]);
				        }
					}
				}
		        else
		        {
		        	$this->sheet->setCellValue($key . $j, $report[$i][$this->columns[$value]]);
		        }
		        if($this->columns[$value] == "CertCode")
				{
					$this->sheet->setCellValue($key . $j, trim($report[$i][$this->columns[$value]]));
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
		$this->sheet->getStyle("A1:" . $lastKey . $j) ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$this->sheet->getStyle("A3:" . $lastKey . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->sheet->getStyle('A3:'.$lastKey.$j)->getFont()->setSize(8);
		for($z=0;$z<count($bold);$z++)
		{
			$this->sheet->getStyle($bold[$z].'3:'.$bold[$z].$j)->getFont()->setBold(true);
		}
		if($unit_price_col != "")
		{
			$this->sheet->getStyle($unit_price_col."3:" . $unit_price_col . $j)->getFont()
						    ->getColor()->setRGB('0066CC');
		}
		$this->sheet->getStyle($itemDescription."3:" . $itemDescription . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$this->sheet->getStyle($upc_col."3:". $upc_col . $j)->getNumberFormat()->setFormatCode('0000000000000');
		$styleArray = array( 'borders' => array( 'allborders' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000'), ), ), ); 
		$this->phpExcel->getActiveSheet()->getStyle('A1:'.$lastKey.$j)->applyFromArray($styleArray);
	}

	private function setReportNegative($header, $report, $bold, $upc_col = "A", $unit_price_col = "", $itemDescription = "D", $reportType = '')
	{
		$j = 4;
		$lastKey = $this->getLastArrayKey($header);
		for ($i=0;$i<count($report);$i++)
		{
			if($report[$i]['onhand'] < 0){
				if($reportType == "sectionNeg" && $i >= 1 && $report[$i]["UPC"] == $report[$i-1]["UPC"]){

				}else{
					foreach($header as $key => $value)
					{
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
								if($value == "CASE COST" || $value == "UNIT PRICE")
						        {
						        	$this->sheet->setCellValue($key . $j, number_format($report[$i][$this->columns[$value]], 2, ".", ""));
						        }
						        else
						        {	
						        	$this->sheet->setCellValue($key . $j, $report[$i][$this->columns[$value]]);
						        }
							}
						}
				        else
				        {
				        	$this->sheet->setCellValue($key . $j, $report[$i][$this->columns[$value]]);
				        }
				        if($this->columns[$value] == "CertCode")
						{
							$this->sheet->setCellValue($key . $j, trim($report[$i][$this->columns[$value]]));
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
			}
		}
		$j = $j - 1;
		$this->sheet->getStyle("A1:" . $lastKey . $j) ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$this->sheet->getStyle("A3:" . $lastKey . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->sheet->getStyle('A3:'.$lastKey.$j)->getFont()->setSize(8);
		for($z=0;$z<count($bold);$z++)
		{
			$this->sheet->getStyle($bold[$z].'3:'.$bold[$z].$j)->getFont()->setBold(true);
		}
		if($unit_price_col != "")
		{
			$this->sheet->getStyle($unit_price_col."3:" . $unit_price_col . $j)->getFont()
						    ->getColor()->setRGB('0066CC');
		}
		$this->sheet->getStyle($itemDescription."3:" . $itemDescription . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$this->sheet->getStyle($upc_col."3:". $upc_col . $j)->getNumberFormat()->setFormatCode('0000000000000');
		$styleArray = array( 'borders' => array( 'allborders' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000'), ), ), ); 
		$this->phpExcel->getActiveSheet()->getStyle('A1:'.$lastKey.$j)->applyFromArray($styleArray);
	}

	private function setReportWithSection($header, $report, $bold, $upc_col = "A", $unit_price_col = "", $itemDescription = "D")
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
			        	$this->sheet->setCellValue($key . $j, number_format($report[$i][$this->columns[$value]], 2, ".", ""));
			        }
			        else
			        {
			        	if($value == "UNIT PRICE")
			        	{
			        		$this->sheet->setCellValue($key . $j, number_format($report[$i][$this->columns[$value]], 2, ".", ""));
			        	}
			        	else
			        	{
			        		if($value == "LAST REC"){
			        			if($report[$i][$this->columns[$value]] == 0){
			        				$this->sheet->setCellValue($key . $j, "");
			        			}else{
			        				$this->sheet->setCellValue($key . $j, $report[$i][$this->columns[$value]]);
			        			}
			        		}else{
			        			$this->sheet->setCellValue($key . $j, $report[$i][$this->columns[$value]]);
			        		}
			        	}
			        }
				}
		        if($this->columns[$value] == "CertCode")
				{
					$this->sheet->setCellValue($key . $j, trim($report[$i][$this->columns[$value]]));
				}
		        if($value == "ON-HAND")
		        {
		        	if($report[$i][$this->columns[$value]] < 0)
		        	{
		        		$this->sheet->getStyle($key . $j)->getFont()
					    ->getColor()->setRGB('FF0000');
		        	}
		        }
			} 
			$j = $j + 1;
			$increment = 1;
		}
		$j = $j - 1;
		$this->sheet->getStyle('A3:'.$lastKey.$j)->getFont()->setSize(8);
		for($z=0;$z<count($bold);$z++)
		{
			$this->sheet->getStyle($bold[$z].'3:'.$bold[$z].$j)->getFont()->setBold(true);
		}
		if($unit_price_col != "")
		{
			$this->sheet->getStyle($unit_price_col."3:" . $unit_price_col . $j)->getFont()
						    ->getColor()->setRGB('0066CC');
		}
		$this->sheet->getStyle("A1:" . $lastKey . $j) ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$this->sheet->getStyle("A3:" . $lastKey . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->sheet->getStyle($itemDescription."3:" . $itemDescription . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$this->sheet->getStyle($upc_col . "3:" . $upc_col . $j)->getNumberFormat()->setFormatCode('0000000000000');
		$styleArray = array( 'borders' => array( 'allborders' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000'), ), ), ); 
		$this->phpExcel->getActiveSheet()->getStyle('A1:'.$lastKey.$j)->applyFromArray($styleArray);
	}

	private function setReportWithSectionNegative($header, $report, $bold, $upc_col = "A", $unit_price_col = "", $itemDescription = "D")
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
			if($report[$i]['onhand'] < 0 && $report[$i]["SctNo"] != 184)
			{
				if($increment == 0 || $condition != $report[$i]["SctNo"])
				{
					$this->sheet->mergeCells('A' . $j . ':' . $start . $j);
					$this->sheet->setCellValue($current . $j, $report[$i]['SctNo'].' - '.$report[$i]['SctName']);
					$condition = $report[$i]["SctNo"];
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
				        	$this->sheet->setCellValue($key . $j, number_format($report[$i][$this->columns[$value]], 2, ".", ""));
				        }
				        else
				        {
				        	if($value == "UNIT PRICE")
				        	{
				        		$this->sheet->setCellValue($key . $j, number_format($report[$i][$this->columns[$value]], 2, ".", ""));
				        	}
				        	else
				        	{
				        		$this->sheet->setCellValue($key . $j, $report[$i][$this->columns[$value]]);
				        	}
				        }
					}
			        if($this->columns[$value] == "CertCode")
					{
						$this->sheet->setCellValue($key . $j, trim($report[$i][$this->columns[$value]]));
					}
			        if($value == "ON-HAND")
			        {
			        	if($report[$i][$this->columns[$value]] < 0)
			        	{
			        		$this->sheet->getStyle($key . $j)->getFont()
						    ->getColor()->setRGB('FF0000');
			        	}
			        }
				} 
				$j = $j + 1;
				$increment = 1;
			}
		}
		$j = $j - 1;
		$this->sheet->getStyle('A3:'.$lastKey.$j)->getFont()->setSize(8);
		for($z=0;$z<count($bold);$z++)
		{
			$this->sheet->getStyle($bold[$z].'3:'.$bold[$z].$j)->getFont()->setBold(true);
		}
		if($unit_price_col != "")
		{
			$this->sheet->getStyle($unit_price_col."3:" . $unit_price_col . $j)->getFont()
						    ->getColor()->setRGB('0066CC');
		}
		$this->sheet->getStyle("A1:" . $lastKey . $j) ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$this->sheet->getStyle("A3:" . $lastKey . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->sheet->getStyle($itemDescription."3:" . $itemDescription . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$this->sheet->getStyle($upc_col . "3:" . $upc_col . $j)->getNumberFormat()->setFormatCode('0000000000000');
		$styleArray = array( 'borders' => array( 'allborders' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000'), ), ), ); 
		$this->phpExcel->getActiveSheet()->getStyle('A1:'.$lastKey.$j)->applyFromArray($styleArray);
	}

	private function setReportWithSectionNegativeRepeat($header, $report, $bold, $upc_col = "A", $unit_price_col = "", $itemDescription = "D")
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
			if($report[$i]['onhand'] < 0 && $report[$i]["SctNo"] != 184 && $report[$i]["UPC"] != $report[$i+1]["UPC"])
			{
				if($increment == 0 || $condition != $report[$i]["SctNo"])
				{
					$this->sheet->mergeCells('A' . $j . ':' . $start . $j);
					$this->sheet->setCellValue($current . $j, $report[$i]['SctNo'].' - '.$report[$i]['SctName']);
					$condition = $report[$i]["SctNo"];
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
				        	$this->sheet->setCellValue($key . $j, number_format($report[$i][$this->columns[$value]], 2, ".", ""));
				        }
				        else
				        {
				        	if($value == "UNIT PRICE")
				        	{
				        		$this->sheet->setCellValue($key . $j, number_format($report[$i][$this->columns[$value]], 2, ".", ""));
				        	}
				        	else
				        	{
				        		$this->sheet->setCellValue($key . $j, $report[$i][$this->columns[$value]]);
				        	}
				        }
					}
			        if($this->columns[$value] == "CertCode")
					{
						$this->sheet->setCellValue($key . $j, trim($report[$i][$this->columns[$value]]));
					}
			        if($value == "ON-HAND")
			        {
			        	if($report[$i][$this->columns[$value]] < 0)
			        	{
			        		$this->sheet->getStyle($key . $j)->getFont()
						    ->getColor()->setRGB('FF0000');
			        	}
			        }
				} 
				$j = $j + 1;
				$increment = 1;
			}
		}
		$j = $j - 1;
		$this->sheet->getStyle('A3:'.$lastKey.$j)->getFont()->setSize(8);
		for($z=0;$z<count($bold);$z++)
		{
			$this->sheet->getStyle($bold[$z].'3:'.$bold[$z].$j)->getFont()->setBold(true);
		}
		if($unit_price_col != "")
		{
			$this->sheet->getStyle($unit_price_col."3:" . $unit_price_col . $j)->getFont()
						    ->getColor()->setRGB('0066CC');
		}
		$this->sheet->getStyle("A1:" . $lastKey . $j) ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$this->sheet->getStyle("A3:" . $lastKey . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->sheet->getStyle($itemDescription."3:" . $itemDescription . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$this->sheet->getStyle($upc_col . "3:" . $upc_col . $j)->getNumberFormat()->setFormatCode('0000000000000');
		$styleArray = array( 'borders' => array( 'allborders' => array( 'style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000'), ), ), ); 
		$this->phpExcel->getActiveSheet()->getStyle('A1:'.$lastKey.$j)->applyFromArray($styleArray);
	}

	private function setCompareReport($header, $report, $bold, $upc_col = "A", $unit_price_col = "L", $itemDescription = "C")
	{
		$j = 4;
		$lastKey = $this->getLastArrayKey($header);
		$alphabet = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
		$start = $alphabet[array_search($this->getItemDescriptionColumn($header), $alphabet) - 1];
		$current =  $this->getItemDescriptionColumn($header);
		$finish = $alphabet[array_search($this->getItemDescriptionColumn($header), $alphabet) + 1];
		$increment = 0;
		$totalElements = count($report);
		$condition = 'ht';
		$vdrOne = 0;
		$vdrEqual = 0;
		$vdrTwo = 0;
		$vendorPercent[1] = array( "name" => "", "number" => "",  "percent" => 0);
	    $vendorPercent[0] = array( "name" => "", "number" => "",  "percent" => 0);
	    $vendorPercent[2] = array( "name" => "", "number" => "",  "percent" => 0);
		for ($i=0; $i<count($report); $i++)
		{
			if($increment == 0 || $condition != $report[$i]["SctNo"])
			{
				$this->sheet->mergeCells('A' . $j . ':' . $start . $j);
				$this->sheet->setCellValue($current . $j, $report[$i]['SctNo'].' - '.$report[$i]['SctName']);
				$condition = $report[$i]["SctNo"];
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
			if($report[$i]["unitPriceOne"] > $report[$i]["unitPriceTwo"])
			{
				$vdrTwo = $vdrTwo + 1;
				$vendorPercent[1] = array("name" => $report[$i]["VdrNameTwo"], "number" => str_replace(" ", "",$report[$i]["VdrNoTwo"]), "percent" => round((100 * $vdrTwo)/$totalElements)); 
			}
			if($report[$i]["unitPriceOne"] == $report[$i]["unitPriceTwo"])
			{
				$vdrEqual = $vdrEqual + 1;
				$vendorPercent[2] = array("name" => "", "number" => "", "percent" => round((100 * $vdrEqual)/$totalElements)); 
			}
			if($report[$i]["unitPriceOne"] < $report[$i]["unitPriceTwo"])
			{
				$vdrOne = $vdrOne + 1;
				$vendorPercent[0] = array("name" => $report[$i]["VdrNameOne"], "number" => str_replace(" ", "",$report[$i]["VdrNoOne"]), "percent" => round((100 * $vdrOne)/$totalElements)); 
			}
			foreach($header as $key => $value)
			{
				if($this->columns[$value] == "UPC" || $this->columns[$value] == "Brand" || $this->columns[$value] == "ItemDescription"
					|| $this->columns[$value] == "sales" || $this->columns[$value] == "onhand"
					|| $this->columns[$value] == "tpr" || $this->columns[$value] == "tprStart" || $this->columns[$value] == "tprEnd")
				{
					$this->sheet->mergeCells($key . $j . ":" . $key . ($j+1));
					// $cell = $this->sheet->Range($key . $j . ":" . $key . ($j+1));

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
				        	$this->sheet->setCellValue($key . $j, number_format($report[$i][$this->columns[$value]], 2, ".", ""));
				        }
				        else
				        {
				        	if($value == "UNIT PRICE")
				        	{
				        		$this->sheet->getStyle($key . $j)->getFont()
							    ->getColor()->setRGB('0066CC');
				        		$this->sheet->setCellValue($key . $j, number_format($report[$i][$this->columns[$value]], 2, ".", ""));
				        	}
				        	else
				        	{
				        		$this->sheet->setCellValue($key . $j, $report[$i][$this->columns[$value]]);
				        	}
				        }
					}
			        if($value == "ON-HAND")
			        {
			        	if($report[$i][$this->columns[$value]] < 0)
			        	{
			        		$this->sheet->getStyle($key . $j)->getFont()
						    ->getColor()->setRGB('FF0000');
			        	}
			        }
				}
				else
				{
					if($report[$i]["unitPriceOne"] <= $report[$i]["unitPriceTwo"])
					{
						if($this->columns[$value] == "CaseCost" || $this->columns[$value] == "unitPrice")
						{
							$this->sheet->setCellValue($key . $j, number_format($report[$i][$this->columns[$value]."One"], 2, ".", ''));
							$this->sheet->setCellValue($key . ($j+1), number_format($report[$i][$this->columns[$value]."Two"], 2, ".", ''));
							$this->phpExcel->getActiveSheet()
							    ->getStyle($key . ($j+1))
							    ->getFill()
							    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
							    ->getStartColor()
							    ->setRGB('F2DEDE');
							$this->phpExcel->getActiveSheet()
							    ->getStyle($key . $j)
							    ->getFill()
							    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
							    ->getStartColor()
							    ->setRGB('DFF0D8');
						}
						else
						{
							if($value == "VDR ITEM #"){
								$this->sheet->setCellValue($key . $j, str_replace(" ", "", $report[$i][$this->columns[$value]."One"]));
							$this->sheet->setCellValue($key . ($j+1), str_replace(" ", "", $report[$i][$this->columns[$value]."Two"]));
							$this->phpExcel->getActiveSheet()
							    ->getStyle($key . ($j+1))
							    ->getFill()
							    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
							    ->getStartColor()
							    ->setRGB('F2DEDE');

							$this->phpExcel->getActiveSheet()
							    ->getStyle($key . $j)
							    ->getFill()
							    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
							    ->getStartColor()
							    ->setRGB('DFF0D8');
							}else{
								$this->sheet->setCellValue($key . $j, $report[$i][$this->columns[$value]."One"]);
							$this->sheet->setCellValue($key . ($j+1), $report[$i][$this->columns[$value]."Two"]);
							$this->phpExcel->getActiveSheet()
							    ->getStyle($key . ($j+1))
							    ->getFill()
							    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
							    ->getStartColor()
							    ->setRGB('F2DEDE');

							$this->phpExcel->getActiveSheet()
							    ->getStyle($key . $j)
							    ->getFill()
							    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
							    ->getStartColor()
							    ->setRGB('DFF0D8');
							}
							
						}
						
					}
					else
					{
						if($this->columns[$value] == "CaseCost" || $this->columns[$value] == "unitPrice")
						{
							$this->sheet->setCellValue($key . ($j+1), number_format($report[$i][$this->columns[$value]."One"], 2, ".", ''));
							$this->sheet->setCellValue($key . $j, number_format($report[$i][$this->columns[$value]."Two"], 2, ".", ''));
							$this->phpExcel->getActiveSheet()
							    ->getStyle($key . ($j+1))
							    ->getFill()
							    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
							    ->getStartColor()
							    ->setRGB('F2DEDE');

							$this->phpExcel->getActiveSheet()
							    ->getStyle($key . $j)
							    ->getFill()
							    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
							    ->getStartColor()
							    ->setRGB('DFF0D8');
						}
						else
						{
							if($value == "VDR ITEM #"){
								$this->sheet->setCellValue($key . ($j+1), str_replace(" ", "", $report[$i][$this->columns[$value]."One"]));
							$this->sheet->setCellValue($key . $j, str_replace(" ", "", $report[$i][$this->columns[$value]."Two"]));
							$this->phpExcel->getActiveSheet()
							    ->getStyle($key . ($j+1))
							    ->getFill()
							    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
							    ->getStartColor()
							    ->setRGB('F2DEDE');

							$this->phpExcel->getActiveSheet()
							    ->getStyle($key . $j)
							    ->getFill()
							    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
							    ->getStartColor()
							    ->setRGB('DFF0D8');
							}else{
								$this->sheet->setCellValue($key . ($j+1), $report[$i][$this->columns[$value]."One"]);
							$this->sheet->setCellValue($key . $j, $report[$i][$this->columns[$value]."Two"]);
							$this->phpExcel->getActiveSheet()
							    ->getStyle($key . ($j+1))
							    ->getFill()
							    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
							    ->getStartColor()
							    ->setRGB('F2DEDE');

							$this->phpExcel->getActiveSheet()
							    ->getStyle($key . $j)
							    ->getFill()
							    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
							    ->getStartColor()
							    ->setRGB('DFF0D8');
							}
						}
					}
				}
			}

			$j = $j + 2;
			$increment = 1;
		}
		for($z=0;$z<count($bold);$z++)
		{
			$this->sheet->getStyle($bold[$z].'3:'.$bold[$z].$j)->getFont()->setBold(true);
		}
		if($unit_price_col != "")
		{
			$this->sheet->getStyle($unit_price_col."3:" . $unit_price_col . $j)->getFont()
						    ->getColor()->setRGB('0066CC');
		}
		$this->sheet->insertNewRowBefore(3, 1);
		$this->sheet->mergeCells('A3:F3');
		$this->sheet->setCellValue("A3","VENDOR " . $vendorPercent[0]['number'] . " - " . $vendorPercent[0]['name'] . " : " . $vendorPercent[0]['percent'] . "%");
		$this->sheet->mergeCells('G3:L3');
		$this->sheet->setCellValue("G3", "VENDOR " . $vendorPercent[1]['number'] . " - " . $vendorPercent[1]['name'] . " : " . $vendorPercent[1]['percent'] . "%");
		$this->sheet->mergeCells('M3:R3');
		$this->sheet->setCellValue("M3", "EQUAL PRICES : " . $vendorPercent[2]['percent'] . "%");
		$this->sheet->getStyle("A1:" . $lastKey . $j) ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$this->sheet->getStyle("A3:" . $lastKey . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->sheet->getStyle($itemDescription."3:" . $itemDescription . $j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$this->sheet->getStyle("A3:A" . $j)->getNumberFormat()->setFormatCode('0000000000000');
		$this->sheet->getStyle('A3:' . $lastKey . $j)->getFont()->setSize(8);
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