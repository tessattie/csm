<?php
session_start();
class export extends Controller{
	
	private $users;

	private $phpExcel;
	
	private $brdata;
	
	private $excel;
	
	private $book;
	
	private $sheet;
	
	private $today;

	private $columns;

	private $columnWidths;

	private $cell_border;

	private $today;

	private $cacheMethod;


	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('America/Port-au-Prince');
		$this->today = date('Y-m-d', strtotime("-1 days"));
		$this->cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory; 
		$this->phpExcel = $this->phpExcel();
		$this->setExcelConstants();
		$this->users = $this->model('users');
		$this->brdata = $this->model('brdata');
		$this->excel = new COM("Excel.Application");
		$this->excel->Workbooks->Add();
		$this->book = $this->excel->Workbooks(1);
		$this->sheet = $this->book->Worksheets(1);
		$this->today = date('Y-m-d', strtotime("-1 days"));
		$this->columnWidths = array("UPC" => 13, "VDR ITEM #" => 11, "BRAND" => 10, "ITEM DESCRIPTION" => 30, "PACK" => 6, "SIZE" => 8, 
			"CASE COST" => 10, "RETAIL" => 7, "ON-HAND" => 8, "LAST ORDER" => 12, "LAST ORDER DATE" => 15, "SALES" => 5, "VDR #" => 7, "VDR NAME" => 22, 
			"TPR PRICE" => 7, "TPR START" => 8, "TPR END" => 8, "SCT NO" => 8, "SCT NAME" => 30, "DPT NO" => 8, "DPT NAME" => 30, "UNIT PRICE" => 10);
		$this->columns = array("UPC" => "UPC", "VDR ITEM #" => "CertCode", "BRAND" => "Brand", "ITEM DESCRIPTION" => "ItemDescription", "PACK" => "Pack", "SIZE" => "SizeAlpha", "CASE COST" => "CaseCost", "RETAIL" => "Retail", 
			"ON-HAND" => "onhand", "LAST ORDER" => "lastReceiving", "LAST ORDER DATE" => "lastReceivingDate", "SALES" => "sales", "VDR #" => "VdrNo", "VDR NAME" => "VdrName", "TPR PRICE" => "tpr", "TPR START" => "tprStart", 
			"TPR END" => "tprEnd", "SCT NO" => "SctNo", "SCT NAME" => "SctName", "DPT NO" => "DptNo", "DPT NAME" => "DptName", "UNIT PRICE" => "unitPrice");
		$this->cell_border = array(xlEdgeTop, xlEdgeBottom, xlEdgeRight, xlEdgeLeft);
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
						"J" => "LAST ORDER", 
						"K" => "LAST ORDER DATE", 
						"L" => "SALES", 
						"M" => "TPR PRICE", 
						"N" => "TPR START", 
						"O" => "TPR END");
		$this->setSheetName("VENDOR REPORT");
		$this->setHeader("VENDOR ".$vendor." FROM ".$from." TO ".$to, $header);
		$report = $this->brdata->get_vendorReport($vendor, $this->today, $from, $to);
		$this->setReport($header, $report);
		$this->styleReport($header, "O", 70, xlLandscape);
		$this->saveReport('VendorSectionFinal_'.$vendor.'_'.$this->today);
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
		$this->setHeader("VENDOR ".$vendor." FROM ".$from." TO ".$to, $header);
		$report = $this->brdata->get_vendorReport($vendor, $this->today, $from, $to);
		$this->setReportWithSection($header, $report);
		$this->styleReport($header, "O", 70, xlLandscape);
		$this->saveReport('VendorLimited_'.$vendor.'_'.$this->today);
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
						"K" => "LAST ORDER", 
						"L" => "LAST ORDER DATE", 
						"M" => "SALES", 
						"N" => "TPR PRICE", 
						"O" => "TPR START", 
						"P" => "TPR END");
		$this->setSheetName("TPR REPORT");
		$this->setHeader("TPR REPORT FROM ".$from." TO ".$to, $header);
		$report = $this->brdata->get_specialReport($this->today, $from, $to);
		$this->setReportWithSection($header, $report);
		$this->styleReport($header, "P", 60, xlLandscape);
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
						"O" => "LAST ORDER", 
						"P" => "LAST ORDER DATE", 
						"Q" => "VDR #", 
						"R" => "VDR NAME");
		$this->setSheetName("VENDOR PRICE COMPARE REPORT");
		$this->setHeader("VENDOR PRICE COMPARE  ".$vendor1." - " . $vendor2 . " FROM ".$from." TO ".$to, $header);
		$report = $this->brdata->vendorPriceCompare($vendor1, $vendor2, $this->today, $from, $to);
		$this->setCompareReport($header, $report);
		$this->styleReport($header, "R", 55, xlLandscape);
		$this->saveReport('VendorPriceCompare_'.$vendor1.'_'.$vendor2.'_'.$this->today);
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
						"O" => "LAST ORDER", 
						"P" => "LAST ORDER DATE", 
						"Q" => "VDR #", 
						"R" => "VDR NAME");
		$this->setSheetName("VENDOR PRICE COMPARE REPORT");
		$this->setHeader("SECTION PRICE COMPARE  ".$vendor1." - " . $vendor2 . " - " . $section . " FROM ".$from." TO ".$to, $header);
		$report = $this->brdata->sectionPriceCompare($vendor1, $vendor2, $section, $this->today, $from, $to);
		$this->setCompareReport($header, $report);
		$this->styleReport($header, "R", 55, xlLandscape);
		$this->saveReport('SectionPriceCompare_'.$vendor1.'_'.$vendor2.'_'.$section."_".$this->today);
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
						"J" => "LAST ORDER", 
						"K" => "LAST ORDER DATE", 
						"L" => "SALES", 
						"M" => "VDR #", 
						"N" => "VDR NAME", 
						"O" => "TPR PRICE", 
						"P" => "TPR START", 
						"Q" => "TPR END");
		$this->setSheetName("DEPARTMENT REPORT");
		$this->setHeader("DEPARTMENT ".$department." FROM ".$from." TO ".$to, $header);
		$report = $this->brdata->get_departmentReport($department, $this->today, $from, $to);
		$this->setReportWithSection($header, $report);
		$this->styleReport($header, "Q", 60, xlLandscape);
		$this->saveReport('VendorPriceCompare_'.$department.'_'.$this->today);
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
						"J" => "LAST ORDER", 
						"K" => "LAST ORDER DATE", 
						"L" => "SALES", 
						"M" => "TPR PRICE", 
						"N" => "TPR START", 
						"O" => "TPR END");
		$this->setSheetName("VENDOR DEPARTMENT REPORT");
		$this->setHeader("VENDOR DEPARTMENT : ".$vendor." - " . $department . " FROM ".$from." TO ".$to, $header);
		$report = $this->brdata->get_vendorDepartmentReport($vendor, $department, $this->today, $to, $from);
		$this->setReportWithSection($header, $report);
		$this->styleReport($header, "O", 70, xlLandscape);
		$this->saveReport('VendorDepartment'.$vendor.'_'.$department.'_'.$this->today);
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
						"J" => "LAST ORDER", 
						"K" => "LAST ORDER DATE", 
						"L" => "SALES", 
						"M" => "VDR #", 
						"N" => "VDR NAME", 
						"O" => "TPR PRICE", 
						"P" => "TPR START", 
						"Q" => "TPR END");
		$this->setSheetName("UPC RANGE REPORT");
		$this->setHeader("UPC RANGE : ".$upc1." - " . $upc2 . " FROM ".$from." TO ".$to, $header);
		$report = $this->brdata->get_upcRangeReport($upc1, $upc2, $this->today, $to, $from);
		$this->setReportWithSection($header, $report);
		$this->styleReport($header, "Q", 60, xlLandscape);
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
						"J" => "LAST ORDER", 
						"K" => "LAST ORDER DATE", 
						"L" => "SALES", 
						"M" => "VDR #", 
						"N" => "VDR NAME", 
						"O" => "TPR PRICE", 
						"P" => "TPR START", 
						"Q" => "TPR END");
		$this->setSheetName("ITEM DESCRIPTION REPORT");
		$description = str_replace("_", " ", $description);
		$this->setHeader($description." FROM ".$from." TO ".$to, $header);
		$report = $this->brdata->get_itemDescription($description, $this->today, $to, $from);
		$this->setReportWithSection($header, $report);
		$this->styleReport($header, "Q", 60, xlLandscape);
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
						"J" => "LAST ORDER", 
						"K" => "LAST ORDER DATE", 
						"L" => "SALES", 
						"M" => "VDR #", 
						"N" => "VDR NAME", 
						"O" => "TPR PRICE", 
						"P" => "TPR START", 
						"Q" => "TPR END");
		$this->setSheetName("SECTION REPORT");
		$this->setHeader("SECTION ".$section." FROM ".$from." TO ".$to, $header);
		$report = $this->brdata->get_sectionReport($section, $this->today, $from, $to);
		$this->setReport($header, $report);
		$this->styleReport($header, "Q", 60, xlLandscape);
		$this->saveReport('Section_'.$section.'_'.$this->today);
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
						"J" => "LAST ORDER", 
						"K" => "LAST ORDER DATE", 
						"L" => "SALES", 
						"M" => "TPR PRICE", 
						"N" => "TPR START", 
						"O" => "TPR END");
		$this->setSheetName("VENDOR SECTION REPORT");
		$report = $this->brdata->get_vendorSectionReport($vendor, $section, $this->today, $to, $from);
		$this->setHeader("VENDOR " . $vendor . " SECTION ".$section." FROM ".$from." TO ".$to, $header);
		$this->setReport($header, $report);
		$this->styleReport($header, "O", 70, xlLandscape);
		$this->saveReport('VendorSection_'.$vendor.'_'.$section.'_'.$this->today);
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
						"M" => "LAST ORDER", 
						"N" => "LAST ORDER DATE", 
						"O" => "SALES", 
						"P" => "TPR PRICE", 
						"Q" => "TPR START", 
						"R" => "TPR END");
		$this->setSheetName("VENDOR UPC PRICE COMPARE REPORT");
		$report = $this->brdata->get_upcReport($upc, $this->today, $to, $from);
		$this->setHeader(" UPC " . $upc . " FROM ". $from . " TO " . $to, $header);
		$this->setReport($header, $report);
		$this->styleReport($header, "R", 55, xlLandscape);
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
						"L" => "LAST ORDER", 
						"M" => "LAST ORDER DATE", 
						"N" => "SALES", 
						"O" => "TPR PRICE", 
						"P" => "TPR START", 
						"Q" => "TPR END");
		$this->setSheetName("VENDOR ITEM CODE REPORT");
		$report = $this->brdata->get_itemcodeReport($code, $this->today, $to, $from);
		$this->setHeader("ITEM CODE " . $code . " FROM ". $from . " TO " . $to, $header);
		$this->setReport($header, $report);
		$this->styleReport($header, "Q", 60, xlLandscape);
		$this->saveReport('VendorItemCode_' . $code . '_' . $this->today);
	}

	public function sectionNames()
	{
		$header = array("A" => "SCT NO", "B" => "SCT NAME", "C" => "DPT NO", "D" => "DPT NAME");
		$this->setSheetName("SECTION NAMES REPORT");
		$report = $this->brdata->get_sectionNames();
		$this->setHeader("SECTION NAMES", $header);
		$this->setReport($header, $report);
		$this->styleReport($header, "D", 90, xlPortrait);
		$this->saveReport('SectionNames_' . $this->today);
	}

	public function departmentNames()
	{
		$header = array("A" => "DPT NO", "B" => "DPT NAME");
		$this->setSheetName("DEPARTMENT NAMES REPORT");
		$report = $this->brdata->get_departmentNames();
		$this->setHeader("DEPARTMENT NAMES", $header);
		$this->setReport($header, $report);
		$this->styleReport($header, "B", 90, xlPortrait);
		$this->saveReport('DepartmentNames_' . $this->today);
	}

	public function vendorNames()
	{
		$header = array("A" => "VDR #", "B" => "VDR NAME");
		$this->setSheetName("VENDOR NAMES");
		$report = $this->brdata->get_vendorNames();
		$this->setHeader("VENDOR NAMES", $header);
		$this->setReport($header, $report);
		$this->styleReport($header, "B", 90, xlPortrait);
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

	private function setSheetNameLib($sheetName)
	{
		$this->sheet->Name = $sheetName;
	}

	private function setHeader($title, $header)
	{
		$cell = $this->sheet->Range('A1:'.$this->getLastArrayKey($header).'1')->merge();
		$cell = $this->sheet->Range('A1');
		$cell->value = $title; 
		foreach($header as $key => $value)
		{
			$cell = $this->sheet->Range($key . '2');
			$cell->value = $value; 
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
		$j = 3;
		for ($i=0;$i<count($report);$i++)
		{
			foreach($header as $key => $value)
			{
				$cell = $this->sheet->Range($key . $j);
				if($this->columns[$value] != "UPC")
				{
					if(($value == "TPR PRICE" && $report[$i]["tpr"] == ".00")
					|| ($value == "TPR START" && $report[$i]["tpr"] == ".00") 
					|| ($value == "TPR END" && $report[$i]["tpr"] == ".00"))
					{
						$cell->value = '';
					}
					else   
					{
						if($value == "CASE COST")
				        {
				        	$cell->value = round($report[$i][$this->columns[$value]]);
				        }
				        else
				        {	
				        	if($value == "UNIT PRICE")
				        	{
				        		$cell->value = round($report[$i][$this->columns[$value]]);
				        	}
				        	else
				        	{
				        		$cell->value = $report[$i][$this->columns[$value]];
				        	}
				        }
					}
				}
		        else
		        {
		        	$cell->NumberFormat = "#################";
		        	$cell->value = $report[$i][$this->columns[$value]];
		        }
		        if($this->columns[$value] == "CertCode")
				{
					$cell->value = trim($report[$i][$this->columns[$value]]);
				}
		        $cell->Font->Size = 8;
		        if($value != "ITEM DESCRIPTION")
		        {
		        	$cell->HorizontalAlignment=-4108;
		        }
		        if($value == "ON-HAND")
		        {
		        	if($report[$i][$this->columns[$value]] < 0)
		        	{
		        		$cell->Font->Color = -16776961;
		        		$cell->Font->Bold = true;
		        	}
		        }
		        foreach ($this->cell_border as $cb)
				{
					$cell->Borders($cb)->LineStyle =
					xlContinuous;
				}
			} 
			$j = $j + 1;
		}
	}

	private function setReportWithSection($header, $report)
	{
		$j = 3;
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
				$cell = $this->sheet->Range('A' . $j . ':'.$start.$j)->merge();
				$cell = $this->sheet->Range('A' . $j . ':'.$start.$j);
				foreach ($this->cell_border as $cb)
				{
					$cell->Borders($cb)->LineStyle = xlContinuous;
				}
				$cell = $this->sheet->Range($current . $j);
				foreach ($this->cell_border as $cb)
				{
					$cell->Borders($cb)->LineStyle = xlContinuous;
				}
				$cell->value = $report[$i]['SctNo'].' - '.$report[$i]['SctName']; 
				$condition = $report[$i]["SctNo"];
				$cell->HorizontalAlignment=-4108;
				$cell->Font->Bold = true;
				$cell = $this->sheet->Range($finish . $j . ':' . $this->getLastArrayKey($header) . $j)->merge();
				$cell = $this->sheet->Range($finish . $j . ':' . $this->getLastArrayKey($header) . $j);
				foreach ($this->cell_border as $cb)
				{
					$cell->Borders($cb)->LineStyle = xlContinuous;
				}
				$j = $j + 1;
			}
			foreach($header as $key => $value)
			{
				$cell = $this->sheet->Range($key . $j);
				if($this->columns[$value] != "UPC")
				{
					if(($value == "TPR PRICE" && $report[$i]["tpr"] == ".00")
					|| ($value == "TPR START" && $report[$i]["tpr"] == ".00") 
					|| ($value == "TPR END" && $report[$i]["tpr"] == ".00"))
					{
						$cell->value = '';
					}
					else
					{
						if($value == "CASE COST")
				        {
				        	$cell->value = round($report[$i][$this->columns[$value]]);
				        }
				        else
				        {
				        	if($value == "UNIT PRICE")
				        	{
				        		$cell->value = round($report[$i][$this->columns[$value]]);
				        	}
				        	else
				        	{
				        		$cell->value = $report[$i][$this->columns[$value]];
				        	}
				        }
					}
				}
		        else
		        {
		        	$cell->NumberFormat = "#################";
		        	$cell->value = $report[$i][$this->columns[$value]];
		        }
		        if($this->columns[$value] == "CertCode")
				{
					$cell->value = trim($report[$i][$this->columns[$value]]);
				}
		        $cell->Font->Size = 8;
		        if($value != "ITEM DESCRIPTION")
		        {
		        	$cell->HorizontalAlignment=-4108;
		        }
		        if($value == "ON-HAND")
		        {
		        	if($report[$i][$this->columns[$value]] < 0)
		        	{
		        		$cell->Font->Color = -16776961;
		        		$cell->Font->Bold = true;
		        	}
		        }
		        foreach ($this->cell_border as $cb)
				{
					$cell->Borders($cb)->LineStyle = xlContinuous;
				}
			} 
			$j = $j + 1;
			$increment = 1;
		}
	}

	private function setCompareReport($header, $report)
	{
		$j = 3;
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
				$cell = $this->sheet->Range('A' . $j . ':'.$start.$j)->merge();
				$cell = $this->sheet->Range('A' . $j . ':'.$start.$j);
				foreach ($this->cell_border as $cb)
				{
					$cell->Borders($cb)->LineStyle = xlContinuous;
				}
				$cell = $this->sheet->Range($current . $j);
				foreach ($this->cell_border as $cb)
				{
					$cell->Borders($cb)->LineStyle = xlContinuous;
				}
				$cell->value = $report[$i]['SctNo'].' - '.$report[$i]['SctName']; 
				$condition = $report[$i]["SctNo"];
				$cell->HorizontalAlignment=-4108;
				$cell->Font->Bold = true;
				$cell = $this->sheet->Range($finish . $j . ':' . $this->getLastArrayKey($header) . $j)->merge();
				$cell = $this->sheet->Range($finish . $j . ':' . $this->getLastArrayKey($header) . $j);
				foreach ($this->cell_border as $cb)
				{
					$cell->Borders($cb)->LineStyle = xlContinuous;
				}
				$j = $j + 1;
			}
			foreach($header as $key => $value)
			{
				if($this->columns[$value] == "UPC" || $this->columns[$value] == "Brand" || $this->columns[$value] == "ItemDescription"
					|| $this->columns[$value] == "sales" || $this->columns[$value] == "onhand"
					|| $this->columns[$value] == "tpr" || $this->columns[$value] == "tprStart" || $this->columns[$value] == "tprEnd")
				{
					$cell->VerticalAlignment=-4108;
					$cell = $this->sheet->Range($key . $j . ":" . $key . ($j+1))->merge();
					$cell = $this->sheet->Range($key . $j . ":" . $key . ($j+1));
					foreach ($this->cell_border as $cb)
					{
						$cell->Borders($cb)->LineStyle = xlContinuous;
					}
					if($this->columns[$value] == "UPC")
					{
						$cell->NumberFormat = "#################";
			        	$cell->value = $report[$i][$this->columns[$value]];	
					}
			        else
			        {
			        	if(($value == "TPR PRICE" && $report[$i]["tpr"] == ".00")
						|| ($value == "TPR START" && $report[$i]["tpr"] == ".00") 
						|| ($value == "TPR END" && $report[$i]["tpr"] == ".00"))
						{
							$cell->value = '';
						}
						else
						{
							if($value == "CASE COST")
					        {
					        	$cell->value = round($report[$i][$this->columns[$value]]);
					        }
					        else
					        {
					        	if($value == "UNIT PRICE")
					        	{
					        		$cell->value = round($report[$i][$this->columns[$value]]);
					        	}
					        	else
					        	{
					        		$cell->value = $report[$i][$this->columns[$value]];
					        	}
					        }
						}
			        }
			        $cell->Font->Size = 8;
			        if($value != "ITEM DESCRIPTION")
			        {
			        	$cell->HorizontalAlignment=-4108;
			        }
			        if($value == "ON-HAND")
			        {
			        	if($report[$i][$this->columns[$value]] < 0)
			        	{
			        		$cell->Font->Color = -16776961;
			        		$cell->Font->Bold = true;
			        	}
			        }
			        foreach ($this->cell_border as $cb)
					{
						$cell->Borders($cb)->LineStyle = xlContinuous;
					}
				}
				else
				{
					$cellOne = $this->sheet->Range($key . $j);
					$cellTwo = $this->sheet->Range($key . ($j+1));
					$cellOne->HorizontalAlignment=-4108;
					$cellTwo->HorizontalAlignment=-4108;
					$cellOne->Font->Size = 8;
					$cellTwo->Font->Size = 8;
					if($report[$i]["unitPriceOne"] <= $report[$i]["unitPriceTwo"])
					{
						if($this->columns[$value] == "CaseCost" || $this->columns[$value] == "unitPrice")
						{
							$cellOne->value = round($report[$i][$this->columns[$value]."One"]);
							$cellTwo->value = round($report[$i][$this->columns[$value]."Two"]);
						}
						else
						{
							$cellOne->value = $report[$i][$this->columns[$value]."One"];
							$cellTwo->value = $report[$i][$this->columns[$value]."Two"];
						}
						
					}
					else
					{
						if($this->columns[$value] == "CaseCost" || $this->columns[$value] == "unitPrice")
						{
							$cellOne->value = round($report[$i][$this->columns[$value]."Two"]);
							$cellTwo->value = round($report[$i][$this->columns[$value]."One"]);
						}
						else
						{
							$cellOne->value = $report[$i][$this->columns[$value]."Two"];
							$cellTwo->value = $report[$i][$this->columns[$value]."One"];
						}
					}
					
					foreach ($this->cell_border as $cb)
					{
						$cellOne->Borders($cb)->LineStyle = xlContinuous;
					}
					foreach ($this->cell_border as $cb)
					{
						$cellTwo->Borders($cb)->LineStyle = xlContinuous;
					}
				}
			} 
			$j = $j + 2;
			$increment = 1;
		}
	}


	private function styleReport($header, $limit, $zoom, $orientation = xlLandscape)
	{
		// Height and alignment of title
		$this->sheet->PageSetup->Orientation = $orientation;
		$this->sheet->PageSetup->PrintTitleColumns = "A:".$limit;
		$this->sheet->PageSetup->FitToPagesWide = 1;
		$this->sheet->PageSetup->Zoom = $zoom;
		$this->sheet->PageSetup->FitToPagesTall = false;
		$cell = $this->sheet->Range('A1');
		$cell->HorizontalAlignment=-4108;
		$cell->RowHeight = 20;
		$cell->Font->Bold = true;
		$cell->Font->Size = 10;
		$cell = $this->sheet->Range('A1:'.$this->getLastArrayKey($header).'1');
		foreach ($this->cell_border as $cb)
		{
			$cell->Borders($cb)->LineStyle =
			xlContinuous;
		}

		// Height and alignment of header
		$cell = $this->sheet->Range('A2:'.$this->getLastArrayKey($header).'2');
		$cell->HorizontalAlignment=-4108;
		$cell->RowHeight = 20;

		foreach($header as $key => $value)
		{
			$cell = $this->sheet->Range($key.'2');
			$cell->ColumnWidth = $this->columnWidths[$value];
			$cell->Font->Bold = true;
			$cell->Font->Size = 9;

			foreach ($this->cell_border as $cb)
			{
				$cell->Borders($cb)->LineStyle =
				xlContinuous;
			}
		}
	}


	private function SaveReport($documentName)
	{
		$filename = tempnam(sys_get_temp_dir(), "excel");
		$this->book->SaveAs($filename);
		$this->sheet = null;//Libération de $sheet
		$this->book = null;//Libération de $book
		$this->excel->Workbooks->Close();//Fermeture du classeur
		$this->excel->Quit();//On quitte Excel
		$this->excel = null;//Libération de l'objet $excel
		header('Content-Description: File Transfer');
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment;Filename=" . $documentName . ".xlsx");
		header('Content-Transfer-Encoding: binary');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
		// Send file to browser
		ob_clean();
		flush();
		readfile($filename);
		unlink($filename);
		echo "<script>window.close()</script>";
		exit;
	}


	private function setExcelConstants()
	{
		// XlBordersIndex
		DEFINE("xlEdgeTop" , 8);
		DEFINE("xlEdgeBottom" , 9);
		DEFINE("xlEdgeRight" , 10);
		DEFINE("xlEdgeLeft" , 7);
		DEFINE("xlDiagonalUp" , 6);
		DEFINE("xlDiagonalDown" , 5);
		DEFINE("xlInsideHorizontal", 12);
		DEFINE("xlInsideVertical" , 11);

		DEFINE("xlContinuous", 1);
		DEFINE("xlDash", -4115);
		DEFINE("xlDot", -4118);
		DEFINE("xlDashDot", 4);
		DEFINE("xlDashDotDot", 5);
		DEFINE("xlDouble", -4119);
		DEFINE("xlSlantDashDot", 13);
		DEFINE("xlLineStyleNone", -4142);

		// XlBorderWeight
		DEFINE("xlHaireline", 1);
		DEFINE("xlMedium" , -4138);
		DEFINE("xlThick" , 4);
		DEFINE("xlThin" , 2);

		DEFINE("xlLandscape", 2);
		DEFINE("xlPortrait", 1);

		// XlVAlign
		DEFINE("xlVAlignBottom" , -4107);
		DEFINE("xlVAlignCenter" , -4108);
		DEFINE("xlVAlignDistributed", -4117);
		DEFINE("xlVAlignJustify" , -4130);
		DEFINE("xlVAlignTop" , -4160);

		// Range/Column data alignment
		DEFINE("xlLeft", 2);
		DEFINE("xlCenter", 3);
		DEFINE("xlRight", 4);
	}
}