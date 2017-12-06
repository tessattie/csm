<?php

class brdata{

	private $db;

	public function __construct()
	{
		$server_name = 'HOST-STORE';
		$this->db = new PDO( "sqlsrv:server=".$server_name." ; Database = BRDATA", "sa", "BRd@t@123");
	}

	public function get_vendorReport($vendorNumber, $today, $from, $to)
	{
		$SQL = "SELECT  vc.UPC, v.Vendor AS VdrNo, v.VendorName AS VdrName, vc.VendorItem AS CertCode, vc.CaseCost, p.TPRPrice AS tpr, p.TPRStartDate AS tprStart, p.TPREndDate AS tprEnd,
				i.Brand, vc.Pack, i.SizeAlpha, i.Department AS SctNo, i.MajorDept AS DptNo, i.Description AS ItemDescription, p.BasePrice as Retail,
				d.Description AS SctName, md.Description AS DptName, 
				(SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=vc.UPC AND id.Vendor = v.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceivingDate,
				ISNULL((SELECT SUM(id.Units) FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=vc.UPC  AND id.Date = 
				(SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=vc.UPC AND id.Vendor = v.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC)),0) AS lastReceiving,
				(SELECT SUM(im.QtySold) FROM dbo.ItemMovement im 
				WHERE im.Date BETWEEN '".$from."' AND '".$to."' AND im.UPC = p.UPC) AS sales,
				(SELECT TOP 1 ISNULL((SELECT TOP 1 ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE UPC= p.UPC AND id.RecordType = 'P' ORDER BY id.Date DESC, id.LastUpdated DESC),0)
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'A' AND ((Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY Date DESC, LastUpdated DESC)) OR (Date = (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY Date DESC, LastUpdated DESC)) AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY Date DESC, LastUpdated DESC)) AND UPC= p.UPC),0) 
				- ISNULL((SELECT SUM(QtySold) FROM dbo.ItemMovement WHERE Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY Date DESC, LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'R' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY Date DESC, LastUpdated DESC) AND UPC=p.UPC),0) 
				FROM dbo.InventoryDetail WHERE UPC=p.UPC),99999) FROM dbo.InventoryDetail) AS onhand, (vc.CaseCost / NULLIF(vc.Pack, 0)) AS unitPrice
				FROM dbo.Vendors v 
				RIGHT JOIN dbo.VendorCost vc ON vc.Vendor = v.Vendor
				LEFT JOIN dbo.Price p ON p.UPC = vc.UPC
				INNER JOIN dbo.Item i ON i.UPC = vc.UPC
				INNER JOIN dbo.Departments d ON d.Department = i.Department
				INNER JOIN dbo.MajorDept md ON md.MajorDept = i.MajorDept
				WHERE v.Vendor = '".$vendorNumber."' AND p.Store = '00000A'
				ORDER BY i.Department, i.Description, i.SizeAlpha ASC, vc.Pack DESC;";
		// Execute query
		$results = $this->db->query($SQL);
		// print_r($this->db->errorInfo());die();
		$report = $results->fetchall(PDO::FETCH_BOTH);

		return $report ;
	}
	

	public function get_specialReport($today, $from, $to)
	{
		$SQL = "SELECT * FROM specials ORDER BY SctNo, VdrNo, ItemDescription";
		// Execute query
		$results = $this->db->query($SQL);
		// print_r($this->db->errorInfo());die();
		$report = $results->fetchall(PDO::FETCH_BOTH);

		return $report ;
	}

	public function get_upcRangeReport($upc1, $upc2, $today, $to, $from)
	{
		$SQL ="SELECT i.UPC, i.Department AS SctNo, vc.Pack, i.SizeAlpha, i.Brand, i.Description AS ItemDescription, vc.Vendor AS VdrNo, p.TPRPrice AS tpr, p.TPRStartDate AS tprStart, p.TPREndDate AS tprEnd,
			vc.VendorItem AS CertCode, vc.CaseCost, p.BasePrice AS Retail, v.VendorName AS VdrName, d.Description AS SctName, 
			(SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=vc.UPC AND id.Vendor = v.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceivingDate,
			ISNULL((SELECT SUM(id.Units) FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=vc.UPC  AND id.Date = 
			(SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=vc.UPC AND id.Vendor = v.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC)),0) AS lastReceiving,
			(SELECT SUM(im.QtySold) FROM dbo.ItemMovement im 
			WHERE im.UPC = i.UPC AND im.Date BETWEEN '".$from."' AND '".$to."') AS sales, 
			(SELECT TOP 1 ISNULL((SELECT TOP 1 ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE UPC= p.UPC AND id.RecordType = 'P' ORDER BY id.LastUpdated DESC),0)
			+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'A' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
			WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
			+ ISNULL((SELECT SUM(QtySold) FROM dbo.ItemMovement WHERE Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
			WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
			+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'R' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
			WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC=p.UPC),0) 
			FROM dbo.InventoryDetail WHERE UPC=p.UPC),99999) FROM dbo.InventoryDetail) AS onhand, (vc.CaseCost / NULLIF(vc.Pack, 0)) AS unitPrice
			FROM dbo.Item i 
			LEFT JOIN dbo.VendorCost vc ON vc.UPC = i.UPC
			LEFT JOIN dbo.Price p ON p.UPC = i.UPC
			LEFT JOIN dbo.Vendors v ON v.Vendor = vc.Vendor
			LEFT JOIN dbo.Departments d ON d.Department = i.Department
			WHERE i.UPC >= '".$upc1."' AND i.UPC <= '".$upc2."' AND p.Store = '00000A'
			ORDER BY i.Department, vc.Vendor, i.Description;";

		// Execute query
		$results = $this->db->query($SQL);
		// print_r($this->db->errorInfo());die();
		$report = $results->fetchall(PDO::FETCH_BOTH);

		return $report ;
	}

	public function get_itemDescription($itemDescription, $today, $to, $from)
	{
		$itemDescription = strtoupper($itemDescription);

		$SQL ="SELECT i.UPC, i.Department AS SctNo, i.MajorDept AS DptNo, vc.Pack, vc.Vendor as VdrNo, vc.VendorItem AS CertCode, vc.CaseCost, i.Brand, i.Description AS ItemDescription, d.Description AS SctName, p.TPRPrice AS tpr, p.TPRStartDate AS tprStart, p.TPREndDate AS tprEnd,
				i.SizeAlpha, p.BasePRice AS Retail, v.VendorName AS VdrName,
				(SELECT SUM(im.QtySold) FROM dbo.ItemMovement im 
				WHERE im.UPC = p.UPC AND im.Date BETWEEN '".$from."' AND '".$to."') AS sales,
				(SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC AND id.Vendor = vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceivingDate,
				ISNULL((SELECT SUM(id.Units) FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC  AND id.Vendor = vc.Vendor AND id.Date = (SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=i.UPC AND id.Vendor = vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC)),0) AS lastReceiving,
				(SELECT TOP 1 ISNULL((SELECT TOP 1 ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE UPC= p.UPC AND id.RecordType = 'P' ORDER BY id.LastUpdated DESC),0)
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'A' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(QtySold) FROM dbo.ItemMovement WHERE Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'R' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC=p.UPC),0) 
				FROM dbo.InventoryDetail WHERE UPC=p.UPC),99999) FROM dbo.InventoryDetail) AS onhand, (vc.CaseCost / NULLIF(vc.Pack, 0)) AS unitPrice
				FROM dbo.Item i 
				LEFT JOIN dbo.VendorCost vc ON vc.UPC = i.UPC
				LEFT JOIN dbo.Price p ON p.UPC = i.UPC
				LEFT JOIN dbo.Vendors v ON v.Vendor = vc.Vendor
				INNER JOIN dbo.Departments d ON d.Department = i.Department
				WHERE i.Description LIKE '%".$itemDescription."%'
				ORDER BY i.Department, vc.Pack, vc.Vendor;";

		// Execute query
		$results = $this->db->query($SQL);
		// print_r($this->db->errorInfo());die();
		$report = $results->fetchall(PDO::FETCH_BOTH);
		return $report ;
	}

	public function get_vendorSectionReport($vendorNumber, $sectionNumber, $today, $to, $from)
	{
		$SQL = "SELECT  vc.UPC, v.Vendor AS VdrNo, v.VendorName AS VdrName, vc.VendorItem AS CertCode, vc.CaseCost,
				i.Brand, vc.Pack, i.SizeAlpha, i.Department AS SctNo, i.MajorDept AS DptNo, i.Description AS ItemDescription, p.BasePrice as Retail, p.TPRPrice AS tpr, p.TPRStartDate AS tprStart, p.TPREndDate AS tprEnd,
				d.Description AS SctName, md.Description AS DptName,
				(SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC AND id.Vendor = vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceivingDate,
				ISNULL((SELECT SUM(id.Units) FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC  AND id.Vendor = vc.Vendor AND id.Date = (SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=i.UPC AND id.Vendor = vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC)),0) AS lastReceiving,
				(SELECT SUM(im.QtySold) FROM dbo.ItemMovement im 
				WHERE im.UPC = p.UPC AND im.Date BETWEEN '".$from."' AND '".$to."') AS sales, 
				(SELECT TOP 1 ISNULL((SELECT TOP 1 ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE UPC= p.UPC AND id.RecordType = 'P' ORDER BY id.LastUpdated DESC),0)
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'A' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(QtySold) FROM dbo.ItemMovement WHERE Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'R' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC=p.UPC),0) 
				FROM dbo.InventoryDetail WHERE UPC=p.UPC),99999) FROM dbo.InventoryDetail) AS onhand, (vc.CaseCost / NULLIF(vc.Pack, 0)) AS unitPrice
				FROM dbo.Vendors v 
				RIGHT JOIN dbo.VendorCost vc ON vc.Vendor = v.Vendor
				LEFT JOIN dbo.Price p ON p.UPC = vc.UPC
				INNER JOIN dbo.Item i ON i.UPC = vc.UPC
				INNER JOIN dbo.Departments d ON d.Department = i.Department
				INNER JOIN dbo.MajorDept md ON md.MajorDept = i.MajorDept
				WHERE v.Vendor = '".$vendorNumber."' AND p.Store = '00000A' AND i.Department = '".$sectionNumber."'
				ORDER BY i.Department, i.Description, vc.Pack DESC, i.SizeAlpha DESC;";

		// Execute query
		$results = $this->db->query($SQL);
		// print_r($this->db->errorInfo());die();
		$report = $results->fetchall(PDO::FETCH_BOTH);

		return $report ;
	}

	public function get_sectionReport($sectionNumber, $today, $from, $to)
	{
		$SQL = "SELECT vc.UPC, vc.Vendor AS VdrNo, vc.VendorItem AS CertCode, vc.CaseCost, i.Brand, i.Description AS ItemDescription, i.SizeAlpha, vc.Pack, i.Department AS SctNo, i.MajorDept AS DptNo, v.VendorName AS VdrName,
				d.Description AS SctName, md.Description AS DptName, p.BasePrice as Retail, p.TPRPrice AS tpr, p.TPRStartDate AS tprStart, p.TPREndDate AS tprEnd,
				(SELECT SUM(im.QtySold) FROM dbo.ItemMovement im 
				WHERE im.UPC = vc.UPC AND im.Date BETWEEN '".$from."' AND '".$to."') AS sales, 
				(SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC AND id.Vendor = vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceivingDate,
				ISNULL((SELECT SUM(id.Units) FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC  AND id.Vendor = vc.Vendor AND id.Date = (SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=i.UPC AND id.Vendor = vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC)),0) AS lastReceiving,
				(SELECT TOP 1 ISNULL((SELECT TOP 1 ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE UPC= p.UPC AND id.RecordType = 'P' ORDER BY id.LastUpdated DESC),0)
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'A' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(QtySold) FROM dbo.ItemMovement WHERE Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'R' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC=p.UPC),0) 
				FROM dbo.InventoryDetail WHERE UPC=p.UPC),99999) FROM dbo.InventoryDetail) AS onhand, (vc.CaseCost / NULLIF(vc.Pack, 0)) AS unitPrice
				FROM dbo.VendorCost vc 
				LEFT JOIN dbo.Item i ON i.UPC = vc.UPC
				LEFT JOIN dbo.Vendors v ON v.Vendor = vc.Vendor
				LEFT JOIN dbo.Price p ON p.UPC = vc.UPC
				INNER JOIN dbo.Departments d ON d.Department = i.Department
				INNER JOIN dbo.MajorDept md ON md.MajorDept = i.MajorDept
				WHERE i.Department = '".$sectionNumber."' AND p.Store = '00000A'
				ORDER BY v.VendorName ASC, i.Description ASC, vc.Pack DESC, i.SizeAlpha DESC;";

		// Execute query
		$results = $this->db->query($SQL);
		// print_r($this->db->errorInfo());die();
		$report = $results->fetchall(PDO::FETCH_BOTH);

		return $report ;
	}

	public function get_multipleSectionReport($sections, $today, $from, $to){
		$sectionString = "";
		for($i=0;$i<count($sections);$i++){
			if($sectionString == ""){
				$sectionString .= " i.department = '".$sections[$i]."' ";
			}else{
				$sectionString .= " OR i.department = '".$sections[$i]."' ";
			}
		}

		// var_dump($sectionString);

		$SQL = "SELECT vc.UPC, vc.Vendor AS VdrNo, vc.VendorItem AS CertCode, vc.CaseCost, i.Brand, i.Description AS ItemDescription, i.SizeAlpha, vc.Pack, i.Department AS SctNo, i.MajorDept AS DptNo, v.VendorName AS VdrName,
				d.Description AS SctName, md.Description AS DptName, p.BasePrice as Retail, p.TPRPrice AS tpr, p.TPRStartDate AS tprStart, p.TPREndDate AS tprEnd,
				(SELECT SUM(im.QtySold) FROM dbo.ItemMovement im 
				WHERE im.UPC = vc.UPC AND im.Date BETWEEN '".$from."' AND '".$to."') AS sales, 
				(SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC AND id.Vendor = vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceivingDate,
				ISNULL((SELECT SUM(id.Units) FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC  AND id.Vendor = vc.Vendor AND id.Date = (SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=i.UPC AND id.Vendor = vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC)),0) AS lastReceiving,
				(SELECT TOP 1 ISNULL((SELECT TOP 1 ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE UPC= p.UPC AND id.RecordType = 'P' ORDER BY id.LastUpdated DESC),0)
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'A' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(QtySold) FROM dbo.ItemMovement WHERE Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'R' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC=p.UPC),0) 
				FROM dbo.InventoryDetail WHERE UPC=p.UPC),99999) FROM dbo.InventoryDetail) AS onhand, (vc.CaseCost / NULLIF(vc.Pack, 0)) AS unitPrice
				FROM dbo.VendorCost vc 
				LEFT JOIN dbo.Item i ON i.UPC = vc.UPC
				LEFT JOIN dbo.Vendors v ON v.Vendor = vc.Vendor
				LEFT JOIN dbo.Price p ON p.UPC = vc.UPC
				INNER JOIN dbo.Departments d ON d.Department = i.Department
				INNER JOIN dbo.MajorDept md ON md.MajorDept = i.MajorDept
				WHERE ".$sectionString." AND p.Store = '00000A'
				ORDER BY i.Department ASC, v.VendorName ASC, i.Description ASC, vc.Pack DESC, i.SizeAlpha DESC;";

		// Execute query
		$results = $this->db->query($SQL);
		// print_r($this->db->errorInfo());die();
		$report = $results->fetchall(PDO::FETCH_BOTH);

		return $report ;
	}

	public function get_multipleSectionNegReport($sections, $today, $from, $to){
		$sectionString = "";
		for($i=0;$i<count($sections);$i++){
			if($sectionString == ""){
				$sectionString .= " i.department = '".$sections[$i]."' ";
			}else{
				$sectionString .= " OR i.department = '".$sections[$i]."' ";
			}
		}

		// var_dump($sectionString);

		$SQL = "SELECT DISTINCT i.UPC, vc.Vendor AS VdrNo, vc.VendorItem AS CertCode, vc.CaseCost, i.Brand, i.Description AS ItemDescription, i.SizeAlpha, vc.Pack, i.Department AS SctNo, i.MajorDept AS DptNo, v.VendorName AS VdrName,
				d.Description AS SctName, md.Description AS DptName, p.BasePrice as Retail, p.TPRPrice AS tpr, p.TPRStartDate AS tprStart, p.TPREndDate AS tprEnd,
				(SELECT SUM(im.QtySold) FROM dbo.ItemMovement im 
				WHERE im.UPC = vc.UPC AND im.Date BETWEEN '".$from."' AND '".$to."') AS sales, 
				(SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=i.UPC ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceivingDate,
				ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC ORDER BY id.LastUpdated DESC, id.Date DESC),0) AS lastReceiving,
				(SELECT TOP 1 ISNULL((SELECT TOP 1 ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE UPC= p.UPC AND id.RecordType = 'P' ORDER BY id.LastUpdated DESC),0)
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'A' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(QtySold) FROM dbo.ItemMovement WHERE Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'R' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC=p.UPC),0) 
				FROM dbo.InventoryDetail WHERE UPC=p.UPC),99999) FROM dbo.InventoryDetail) AS onhand, (vc.CaseCost / NULLIF(vc.Pack, 0)) AS unitPrice
				FROM dbo.Item i 
				LEFT JOIN dbo.VendorCost vc ON i.UPC = vc.UPC
				LEFT JOIN dbo.Vendors v ON v.Vendor = vc.Vendor
				LEFT JOIN dbo.Price p ON p.UPC = vc.UPC
				INNER JOIN dbo.Departments d ON d.Department = i.Department
				INNER JOIN dbo.MajorDept md ON md.MajorDept = i.MajorDept
				WHERE ".$sectionString." AND p.Store = '00000A'
				ORDER BY i.UPC, v.VendorName ASC, i.Description ASC, vc.Pack DESC, i.SizeAlpha DESC;";

		// Execute query
		$results = $this->db->query($SQL);
		// print_r($this->db->errorInfo());die();
		$report = $results->fetchall(PDO::FETCH_BOTH);

		return $report ;
	}

	public function get_sectionNegReport($sectionNumber, $today, $from, $to)
	{
		$SQL = "SELECT DISTINCT vc.UPC, vc.Vendor AS VdrNo, vc.VendorItem AS CertCode, vc.CaseCost, i.Brand, i.Description AS ItemDescription, i.SizeAlpha, vc.Pack, i.Department AS SctNo, i.MajorDept AS DptNo, v.VendorName AS VdrName,
				d.Description AS SctName, md.Description AS DptName, p.BasePrice as Retail, p.TPRPrice AS tpr, p.TPRStartDate AS tprStart, p.TPREndDate AS tprEnd,
				(SELECT SUM(im.QtySold) FROM dbo.ItemMovement im 
				WHERE im.UPC = vc.UPC AND im.Date BETWEEN '".$from."' AND '".$to."') AS sales, 
				(SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=i.UPC ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceivingDate,
				ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC ORDER BY id.LastUpdated DESC, id.Date DESC),0) AS lastReceiving,
				(SELECT TOP 1 ISNULL((SELECT TOP 1 ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE UPC= p.UPC AND id.RecordType = 'P' ORDER BY id.LastUpdated DESC),0)
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'A' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(QtySold) FROM dbo.ItemMovement WHERE Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'R' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC=p.UPC),0) 
				FROM dbo.InventoryDetail WHERE UPC=p.UPC),99999) FROM dbo.InventoryDetail) AS onhand, (vc.CaseCost / NULLIF(vc.Pack, 0)) AS unitPrice
				FROM dbo.VendorCost vc 
				LEFT JOIN dbo.Item i ON i.UPC = vc.UPC
				LEFT JOIN dbo.Vendors v ON v.Vendor = vc.Vendor
				LEFT JOIN dbo.Price p ON p.UPC = vc.UPC
				INNER JOIN dbo.Departments d ON d.Department = i.Department
				INNER JOIN dbo.MajorDept md ON md.MajorDept = i.MajorDept
				WHERE i.Department = '".$sectionNumber."' AND p.Store = '00000A'
				ORDER BY vc.UPC, v.VendorName ASC, i.Description ASC, vc.Pack DESC, i.SizeAlpha DESC;";

		// Execute query
		$results = $this->db->query($SQL);
		// print_r($this->db->errorInfo());die();
		$report = $results->fetchall(PDO::FETCH_BOTH);

		return $report ;
	}

	public function get_departmentReport($departmentNumber,$today,$from, $to)
	{
		$SQL = "SELECT DISTINCT vc.UPC, v.Vendor AS VdrNo, v.VendorName AS VdrName, vc.VendorItem AS CertCode, vc.CaseCost, p.TPRPrice AS tpr, p.TPRStartDate AS tprStart, p.TPREndDate AS tprEnd,
				i.Brand, vc.Pack, i.SizeAlpha, i.Department AS SctNo, i.MajorDept AS DptNo, i.Description AS ItemDescription, p.BasePrice as Retail,
				d.Description AS SctName, md.Description AS DptName, 
				i.Description AS ItemDescription, vc.Pack AS Pack,
				i.SizeAlpha AS SizeAlpha, vc.CaseCost AS CaseCost, p.BasePrice AS Retail, md.MajorDept AS DptNo,
				(SELECT SUM(im.QtySold) FROM dbo.ItemMovement im WHERE im.UPC = p.UPC AND im.Date BETWEEN '".$from."' AND '".$to."') AS sales, 
				(SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=i.UPC ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceivingDate,
				ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC ORDER BY id.LastUpdated DESC, id.Date DESC),0) AS lastReceiving,
				(SELECT TOP 1 ISNULL((SELECT TOP 1 ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE UPC= p.UPC AND id.RecordType = 'P' ORDER BY id.LastUpdated DESC),0)
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'A' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(QtySold) FROM dbo.ItemMovement WHERE Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'R' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC=p.UPC),0) 
				FROM dbo.InventoryDetail WHERE UPC=p.UPC),99999) FROM dbo.InventoryDetail) AS onhand, (vc.CaseCost / NULLIF(vc.Pack, 0)) AS unitPrice
				FROM dbo.Item i
				INNER JOIN dbo.VendorCost vc ON i.UPC = vc.UPC
				INNER JOIN dbo.Price p ON vc.UPC = p.UPC
				INNER JOIN dbo.Departments d ON i.Department = d.Department
				INNER JOIN dbo.Vendors v ON vc.vendor = v.vendor
				INNER JOIN dbo.MajorDept md ON md.MajorDept = d.MajorDept
				LEFT JOIN dbo.InventoryDetail id ON id.UPC = i.UPC
				WHERE md.MajorDept = '".$departmentNumber."' AND p.Store = '00000A'
				ORDER BY i.Department ASC, v.VendorName ASC, i.Description ASC, vc.Pack, i.SizeAlpha;";

		// Execute query
		$results = $this->db->query($SQL);
		$report = $results->fetchall(PDO::FETCH_BOTH);

		return $report ;
	}

	public function get_vendorDepartmentReport($vendorNumber, $departmentNumber, $today, $from, $to)
	{
		$SQL = "SELECT DISTINCT  p.UPC, v.Vendor AS VdrNo, v.VendorName AS VdrName, vc.VendorItem AS CertCode, vc.CaseCost, p.TPRPrice AS tpr, p.TPRStartDate AS tprStart, p.TPREndDate AS tprEnd,
				i.Brand, vc.Pack, i.SizeAlpha, i.Department AS SctNo, i.MajorDept AS DptNo, i.Description AS ItemDescription, p.BasePrice as Retail, 
				d.Description AS SctName, md.Description AS DptName,
				(SELECT SUM(im.QtySold) FROM dbo.ItemMovement im 
				WHERE im.UPC = p.UPC AND im.Date BETWEEN '".$from."' AND '".$to."') AS sales, 
				(SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=vc.UPC AND id.Vendor = v.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceivingDate,
				ISNULL((SELECT SUM(id.Units) FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=vc.UPC  AND id.Date = 
				(SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=vc.UPC AND id.Vendor = v.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC)),0) AS lastReceiving,
				(SELECT TOP 1 ISNULL((SELECT TOP 1 ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE UPC= p.UPC AND id.RecordType = 'P' ORDER BY id.LastUpdated DESC),0)
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'A' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(QtySold) FROM dbo.ItemMovement WHERE Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'R' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC=p.UPC),0) 
				FROM dbo.InventoryDetail WHERE UPC=p.UPC),99999) FROM dbo.InventoryDetail) AS onhand, (vc.CaseCost / NULLIF(vc.Pack, 0)) AS unitPrice
				FROM dbo.Vendors v 
				LEFT JOIN dbo.VendorCost vc ON vc.Vendor = v.Vendor
				LEFT JOIN dbo.Price p ON p.UPC = vc.UPC
				INNER JOIN dbo.Item i ON i.UPC = p.UPC
				INNER JOIN dbo.Departments d ON d.Department = i.Department
				INNER JOIN dbo.MajorDept md ON md.MajorDept = i.MajorDept
				LEFT JOIN dbo.InventoryDetail id ON p.UPC = id.UPC 
				WHERE v.Vendor = '".$vendorNumber."' AND i.MajorDept = '".$departmentNumber."' AND p.Store = '00000A'
				ORDER BY i.Department, i.Description, vc.Pack DESC, i.SizeAlpha DESC;";

		// Execute query
		$results = $this->db->query($SQL);
		// print_r($this->db->errorInfo());die();
		$report = $results->fetchall(PDO::FETCH_BOTH);

		return $report ;
	}

	public function get_upcReport($upcNumber, $today, $to, $from)
	{
		$SQL ="SELECT DISTINCT vc.UPC, vc.Vendor AS VdrNo, p.BasePRice AS Retail, vc.VendorItem AS CertCode, vc.CaseCost, i.Brand, i.Description AS ItemDescription,
				i.SizeAlpha, vc.Pack, v.VendorName AS VdrName, p.TPRPrice AS tpr, p.TPRStartDate AS tprStart, p.TPREndDate AS tprEnd,
				(SELECT SUM(im.QtySold) FROM dbo.ItemMovement im 
				WHERE im.UPC = p.UPC AND im.Date BETWEEN '".$from."' AND '".$to."') AS sales, 
				(SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=i.UPC AND id.Vendor = vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceivingDate,
				ISNULL((SELECT SUM(id.Units) FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC  AND id.Vendor = vc.Vendor AND id.Date = (SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=i.UPC AND id.Vendor = vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC)),0) AS lastReceiving,
				(SELECT TOP 1 ISNULL((SELECT TOP 1 ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE UPC= p.UPC AND id.RecordType = 'P' ORDER BY id.LastUpdated DESC),0)
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'A' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY Date DESC, LastUpdated DESC) AND UPC= p.UPC),0) 
				- ISNULL((SELECT SUM(QtySold) FROM dbo.ItemMovement WHERE Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY Date DESC, LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'R' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY Date DESC, LastUpdated DESC) AND UPC=p.UPC),0) 
				FROM dbo.InventoryDetail WHERE UPC=p.UPC),99999) FROM dbo.InventoryDetail) AS onhand, (vc.CaseCost / NULLIF(vc.Pack, 0)) AS unitPrice
				FROM dbo.VendorCost vc
				LEFT JOIN dbo.Price p ON p.UPC = vc.UPC
				INNER JOIN dbo.Item i ON i.UPC = vc.UPC 
				INNER JOIN dbo.Vendors v ON v.Vendor = vc.Vendor 
				LEFT JOIN dbo.InventoryDetail id ON id.UPC = vc.UPC
				WHERE vc.UPC LIKE '%".$upcNumber."' AND p.Store = '00000A'
				ORDER BY unitPrice, vc.CaseCost;";

		// Execute query
		$results = $this->db->query($SQL);
		$report = $results->fetchall(PDO::FETCH_BOTH);

		return $report ;
	}

	public function get_upcReceivingHistory($upcNumber, $today, $to, $from)
	{
		$SQL ="SELECT DISTINCT id.Date AS r_date, id.UPC, id.RecordType, id.Vendor AS VdrNo, v.VendorName AS VdrName,
				i.Brand, i.Description AS ItemDescription, i.SizeAlpha, i.Pack, i.Department AS SctNo, i.MajorDept AS DptNo,
				p.TPRPrice AS tpr, p.TPRStartDate AS tprStart, p.TPREndDate AS tprEnd, p.BasePRice AS Retail, 
				vc.VendorItem AS CertCode, vc.CaseCost,
				(SELECT SUM(im.QtySold) FROM dbo.ItemMovement im 
				WHERE im.UPC = id.UPC AND im.Date BETWEEN '".$from."' AND '".$to."') AS sales, (vc.CaseCost / NULLIF(vc.Pack, 0)) AS unitPrice,
				(SELECT TOP 1 id.Date FROM dbo.InventoryDetail invDet WHERE invDet.RecordType = 'R' AND invDet.UPC=id.UPC AND invDet.Vendor=vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceivingDate,
				ISNULL((SELECT SUM(inv.Units) FROM dbo.InventoryDetail inv WHERE inv.RecordType = 'R' AND inv.Vendor=vc.Vendor AND inv.UPC=id.UPC AND 
				inv.Date = (SELECT TOP 1 id.Date FROM dbo.InventoryDetail WHERE RecordType = 'R' AND UPC=id.UPC AND Vendor=vc.Vendor ORDER BY LastUpdated DESC, Date DESC)),0) AS lastReceiving,
				(SELECT TOP 1 ISNULL((SELECT TOP 1 ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE UPC= p.UPC AND id.RecordType = 'P' ORDER BY id.LastUpdated DESC),0)
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'A' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(QtySold) FROM dbo.ItemMovement WHERE Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'R' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC=p.UPC),0) 
				FROM dbo.InventoryDetail WHERE UPC=p.UPC),99999) FROM dbo.InventoryDetail) AS onhand
				FROM dbo.InventoryDetail id
				LEFT JOIN Vendors v ON v.Vendor = id.Vendor
				LEFT JOIN dbo.Item i ON i.UPC = id.UPC
				LEFT JOIN dbo.Price p ON p.UPC = id.UPC
				LEFT JOIN dbo.VendorCost vc ON vc.UPC = id.UPC AND vc.Vendor = id.Vendor 
				WHERE RecordType = 'R' AND id.UPC ='".$upcNumber."' AND id.Date BETWEEN '".$from."' AND '".$to."' 
				ORDER BY id.Date DESC";

		// Execute query
		$results = $this->db->query($SQL);
		// print_r($this->db->errorInfo());die();
		$report = $results->fetchall(PDO::FETCH_BOTH);

		return $report ;
	}

	public function get_itemcodeReport($itemcode, $today, $to, $from)
	{
		$SQL ="SELECT DISTINCT vc.UPC, vc.Vendor AS VdrNo, p.BasePRice AS Retail, vc.VendorItem AS CertCode, vc.CaseCost, i.Brand, i.Description AS ItemDescription, p.TPRPrice AS tpr, p.TPRStartDate AS tprStart, p.TPREndDate AS tprEnd,
				i.SizeAlpha, vc.Pack, v.VendorName AS VdrName,
				(SELECT SUM(im.QtySold) FROM dbo.ItemMovement im 
				WHERE im.UPC = p.UPC AND im.Date BETWEEN '".$from."' AND '".$to."') AS sales, 
				(SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=i.UPC AND id.Vendor = vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceivingDate,
				ISNULL((SELECT SUM(id.Units) FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC  AND id.Vendor = vc.Vendor AND id.Date = (SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=i.UPC AND id.Vendor = vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC)),0) AS lastReceiving,
				(SELECT TOP 1 ISNULL((SELECT TOP 1 ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE UPC= p.UPC AND id.RecordType = 'P' ORDER BY id.LastUpdated DESC),0)
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'A' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(QtySold) FROM dbo.ItemMovement WHERE Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'R' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC=p.UPC),0) 
				FROM dbo.InventoryDetail WHERE UPC=p.UPC),99999) FROM dbo.InventoryDetail) AS onhand, (vc.CaseCost / NULLIF(vc.Pack, 0)) AS unitPrice
				FROM dbo.VendorCost vc
				LEFT JOIN dbo.Price p ON p.UPC = vc.UPC
				INNER JOIN dbo.Item i ON i.UPC = vc.UPC 
				INNER JOIN dbo.Vendors v ON v.Vendor = vc.Vendor 
				LEFT JOIN dbo.InventoryDetail id ON id.UPC = vc.UPC
				WHERE vc.VendorItem LIKE '%".$itemcode."%' AND p.Store = '00000A';";

		// Execute query
		$results = $this->db->query($SQL);
		$report = $results->fetchall(PDO::FETCH_BOTH);

		return $report ;
	}

	public function vendorPriceCompare($vendor1, $vendor2, $today, $from, $to)
	{
		$drop1 = "DROP VIEW vendor1";
		$view1 = "CREATE VIEW vendor1 AS (SELECT  vc.UPC, v.Vendor AS VdrNo, v.VendorName AS VdrName, vc.VendorItem AS CertCode, vc.CaseCost, p.TPRPrice AS tpr, p.TPRStartDate AS tprStart, p.TPREndDate AS tprEnd,
				i.Brand, vc.Pack, i.SizeAlpha, i.Department AS SctNo, i.MajorDept AS DptNo, i.Description AS ItemDescription, p.BasePrice as Retail,
				d.Description AS SctName, md.Description AS DptName, 
				(SELECT SUM(im.QtySold) FROM dbo.ItemMovement im 
				WHERE im.UPC = p.UPC AND im.Date BETWEEN '".$from."' AND '".$to."') AS sales, 
				(SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC AND id.Vendor = vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceivingDate,
				ISNULL((SELECT SUM(id.Units) FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC  AND id.Vendor = vc.Vendor AND id.Date = (SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=i.UPC AND id.Vendor = vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC)),0) AS lastReceiving,
				(SELECT TOP 1 ISNULL((SELECT TOP 1 ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE UPC= p.UPC AND id.RecordType = 'P' ORDER BY id.LastUpdated DESC),0)
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'A' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(QtySold) FROM dbo.ItemMovement WHERE Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'R' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC=p.UPC),0) 
				FROM dbo.InventoryDetail WHERE UPC=p.UPC),99999) FROM dbo.InventoryDetail) AS onhand, (vc.CaseCost / NULLIF(vc.Pack, 0)) AS unitPrice
				FROM dbo.Vendors v 
				RIGHT JOIN dbo.VendorCost vc ON vc.Vendor = v.Vendor
				LEFT JOIN dbo.Price p ON p.UPC = vc.UPC
				INNER JOIN dbo.Item i ON i.UPC = vc.UPC
				INNER JOIN dbo.Departments d ON d.Department = i.Department
				INNER JOIN dbo.MajorDept md ON md.MajorDept = i.MajorDept
				WHERE v.Vendor = '".$vendor1."' AND p.Store = '00000A');";
		
		$drop2 = "DROP VIEW vendor2";
		$view2 = "CREATE VIEW vendor2 AS (SELECT  vc.UPC, v.Vendor AS VdrNo, v.VendorName AS VdrName, vc.VendorItem AS CertCode, vc.CaseCost, p.TPRPrice AS tpr, p.TPRStartDate AS tprStart, p.TPREndDate AS tprEnd,
				i.Brand, vc.Pack, i.SizeAlpha, i.Department AS SctNo, i.MajorDept AS DptNo, i.Description AS ItemDescription, p.BasePrice as Retail,
				d.Description AS SctName, md.Description AS DptName,
				(SELECT SUM(im.QtySold) FROM dbo.ItemMovement im WHERE im.UPC = p.UPC AND im.Date BETWEEN '".$from."' AND '".$to."') AS sales, 
				(SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC AND id.Vendor = vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceivingDate,
				ISNULL((SELECT SUM(id.Units) FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC  AND id.Vendor = vc.Vendor AND id.Date = (SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=i.UPC AND id.Vendor = vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC)),0) AS lastReceiving,
				(SELECT TOP 1 ISNULL((SELECT TOP 1 ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE UPC= p.UPC AND id.RecordType = 'P' ORDER BY id.LastUpdated DESC),0)
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'A' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(QtySold) FROM dbo.ItemMovement WHERE Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'R' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC=p.UPC),0) 
				FROM dbo.InventoryDetail WHERE UPC=p.UPC),99999) FROM dbo.InventoryDetail) AS onhand, (vc.CaseCost / NULLIF(vc.Pack, 0)) AS unitPrice
				FROM dbo.Vendors v 
				RIGHT JOIN dbo.VendorCost vc ON vc.Vendor = v.Vendor
				LEFT JOIN dbo.Price p ON p.UPC = vc.UPC
				INNER JOIN dbo.Item i ON i.UPC = vc.UPC
				INNER JOIN dbo.Departments d ON d.Department = i.Department
				INNER JOIN dbo.MajorDept md ON md.MajorDept = i.MajorDept
				WHERE v.Vendor = '".$vendor2."' AND p.Store = '00000A');";

		$SQL = "SELECT one.UPC, one.SctNo, one.SctName, one.ItemDescription, one.Retail AS RetailOne, two.retail AS RetailTwo, one.DptNo, one.DptName, one.onhand, one.sales, one.tpr, one.tprStart, 
				one.tprEnd, one.Brand, one.VdrName AS VdrNameOne, one.unitPrice AS unitPriceOne, two.unitPrice AS unitPriceTwo, one.VdrNo AS VdrNoOne, one.Pack AS PackOne, one.SizeAlpha AS SizeAlphaOne, 
				one.CaseCost AS CaseCostOne, one.lastReceiving AS lastReceivingOne, one.lastReceivingDate AS lastReceivingDateOne, 
				two.VdrName AS VdrNameTwo, two.VdrNo AS VdrNoTwo, two.Pack AS PackTwo, two.SizeAlpha AS SizeAlphaTwo, 
				two.CaseCost AS CaseCostTwo, two.lastReceiving AS lastReceivingTwo, two.lastReceivingDate AS lastReceivingDateTwo, 
				one.CertCode AS CertCodeOne, two.CertCode AS CertCodeTwo
				FROM vendor1 one
				INNER JOIN vendor2 two ON two.UPC = one.UPC ORDER BY one.SctNo";

		$this->db->query($drop1);
		$this->db->query($view1);
		$this->db->query($drop2);
		$this->db->query($view2);
		$results = $this->db->query($SQL);


		$report = $results->fetchall(PDO::FETCH_BOTH);

		return $report;
	}

	public function sectionPriceCompare($vendor1, $vendor2, $section, $today, $from, $to)
	{

		$drop1 = "DROP VIEW section1";
		$view1 = "CREATE VIEW section1 AS (SELECT  vc.UPC, v.Vendor AS VdrNo, v.VendorName AS VdrName, vc.VendorItem AS CertCode, vc.CaseCost,
				i.Brand, vc.Pack, i.SizeAlpha, i.Department AS SctNo, i.MajorDept AS DptNo, i.Description AS ItemDescription, p.BasePrice as Retail, p.TPRPrice AS tpr, p.TPRStartDate AS tprStart, p.TPREndDate AS tprEnd,
				d.Description AS SctName, md.Description AS DptName, 
				(SELECT SUM(im.QtySold) FROM dbo.ItemMovement im 
				WHERE im.UPC = p.UPC AND im.Date BETWEEN '".$from."' AND '".$to."') AS sales, 
				(SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC AND id.Vendor = vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceivingDate,
				ISNULL((SELECT SUM(id.Units) FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC  AND id.Vendor = vc.Vendor AND id.Date = (SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=i.UPC AND id.Vendor = vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC)),0) AS lastReceiving,
				(SELECT TOP 1 ISNULL((SELECT TOP 1 ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE UPC= p.UPC AND id.RecordType = 'P' ORDER BY id.LastUpdated DESC),0)
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'A' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(QtySold) FROM dbo.ItemMovement WHERE Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'R' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC=p.UPC),0) 
				FROM dbo.InventoryDetail WHERE UPC=p.UPC),99999) FROM dbo.InventoryDetail) AS onhand, (vc.CaseCost / NULLIF(vc.Pack, 0)) AS unitPrice
				FROM dbo.Vendors v 
				RIGHT JOIN dbo.VendorCost vc ON vc.Vendor = v.Vendor
				LEFT JOIN dbo.Price p ON p.UPC = vc.UPC
				INNER JOIN dbo.Item i ON i.UPC = vc.UPC
				INNER JOIN dbo.Departments d ON d.Department = i.Department
				INNER JOIN dbo.MajorDept md ON md.MajorDept = i.MajorDept
				WHERE v.Vendor = '".$vendor1."' AND p.Store = '00000A' AND i.Department = '".$section."');";
		
		$drop2 = "DROP VIEW section2";
		$view2 = "CREATE VIEW section2 AS (SELECT  vc.UPC, v.Vendor AS VdrNo, v.VendorName AS VdrName, vc.VendorItem AS CertCode, vc.CaseCost,
				i.Brand, vc.Pack, i.SizeAlpha, i.Department AS SctNo, i.MajorDept AS DptNo, i.Description AS ItemDescription, p.BasePrice as Retail, p.TPRPrice AS tpr, p.TPRStartDate AS tprStart, p.TPREndDate AS tprEnd,
				d.Description AS SctName, md.Description AS DptName,
				(SELECT SUM(im.QtySold) FROM dbo.ItemMovement im 
				WHERE im.UPC = p.UPC AND im.Date BETWEEN '".$from."' AND '".$to."') AS sales, 
				(SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC AND id.Vendor = vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC) AS lastReceivingDate,
				ISNULL((SELECT SUM(id.Units) FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=p.UPC  AND id.Vendor = vc.Vendor AND id.Date = (SELECT TOP 1 id.Date FROM dbo.InventoryDetail id WHERE id.RecordType = 'R' AND id.UPC=i.UPC AND id.Vendor = vc.Vendor ORDER BY id.LastUpdated DESC, id.Date DESC)),0) AS lastReceiving,
				(SELECT TOP 1 ISNULL((SELECT TOP 1 ISNULL((SELECT TOP 1 id.Units FROM dbo.InventoryDetail id WHERE UPC= p.UPC AND id.RecordType = 'P' ORDER BY id.LastUpdated DESC),0)
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'A' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(QtySold) FROM dbo.ItemMovement WHERE Date > (SELECT TOP 1 Date FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC= p.UPC),0) 
				+ ISNULL((SELECT SUM(Units) FROM dbo.InventoryDetail WHERE RecordType = 'R' AND LastUpdated > (SELECT TOP 1 LastUpdated FROM dbo.InventoryDetail id 
				WHERE id.RecordType = 'P' AND id.UPC = p.UPC ORDER BY LastUpdated DESC) AND UPC=p.UPC),0) 
				FROM dbo.InventoryDetail WHERE UPC=p.UPC),99999) FROM dbo.InventoryDetail) AS onhand, (vc.CaseCost / NULLIF(vc.Pack, 0)) AS unitPrice
				FROM dbo.Vendors v 
				RIGHT JOIN dbo.VendorCost vc ON vc.Vendor = v.Vendor
				LEFT JOIN dbo.Price p ON p.UPC = vc.UPC
				INNER JOIN dbo.Item i ON i.UPC = vc.UPC
				INNER JOIN dbo.Departments d ON d.Department = i.Department
				INNER JOIN dbo.MajorDept md ON md.MajorDept = i.MajorDept
				WHERE v.Vendor = '".$vendor2."' AND p.Store = '00000A' AND i.Department = '".$section."');";

		$SQL = "SELECT one.UPC, one.SctNo, one.Retail AS RetailOne, two.retail AS RetailTwo, one.SctName, one.ItemDescription, one.unitPrice AS unitPriceOne, two.unitPrice AS unitPriceTwo,  one.DptNo, one.DptName, one.onhand, one.sales, one.tpr, one.tprStart, 
				one.tprEnd, one.Brand, one.VdrName AS VdrNameOne, one.VdrNo AS VdrNoOne, one.Pack AS PackOne, one.SizeAlpha AS SizeAlphaOne, 
				one.CaseCost AS CaseCostOne, one.lastReceiving AS lastReceivingOne, one.lastReceivingDate AS lastReceivingDateOne, 
				two.VdrName AS VdrNameTwo, two.VdrNo AS VdrNoTwo, two.Pack AS PackTwo, two.SizeAlpha AS SizeAlphaTwo, 
				two.CaseCost AS CaseCostTwo, two.lastReceiving AS lastReceivingTwo, two.lastReceivingDate AS lastReceivingDateTwo, 
				one.CertCode AS CertCodeOne, two.CertCode AS CertCodeTwo, two.unitPrice AS unitPriceTwo, one.unitPrice AS unitPriceOne
				FROM dbo.section1 one
				INNER JOIN section2 two ON two.UPC = one.UPC";

		$this->db->query($drop1);
		$this->db->query($view1);
		$this->db->query($drop2);
		$this->db->query($view2);
		$results = $this->db->query($SQL);

		$report = $results->fetchall(PDO::FETCH_BOTH);

		return $report;
	}

	public function get_sectionNames()
	{
		$SQL = "SELECT d.Department AS SctNo, d.Description AS SctName, m.MajorDept AS DptNo, m.Description AS DptName
				FROM dbo.Departments d
				LEFT JOIN dbo.MajorDept m ON m.MajorDept = d.MajorDept
				ORDER BY m.MajorDept, d.Description;";

		// Execute query
		$results = $this->db->query($SQL);
		$report = $results->fetchall(PDO::FETCH_BOTH);

		return $report;
	}

	public function get_vendorNames()
	{
		$SQL = "SELECT v.Vendor AS VdrNo, v.VendorName AS VdrName
				FROM dbo.Vendors v
				ORDER BY v.Vendor;";

		// Execute query
		$results = $this->db->query($SQL);
		$report = $results->fetchall(PDO::FETCH_BOTH);

		return $report;
	}

	public function get_departmentNames()
	{
		$SQL = "SELECT d.MajorDept AS DptNo, d.Description AS DptName
				FROM dbo.MajorDept d
				ORDER BY d.MajorDept;";

		// Execute query
		$results = $this->db->query($SQL);
		$report = $results->fetchall(PDO::FETCH_BOTH);

		return $report;
	}

	public function get_adjReport()
	{
		$SQL = "SELECT id.UPC, id.Units AS adj, id.Date, id.lastUpdated AS adjLastUpdt, id.RecordType, 
				i.Description AS ItemDescription, i.Department AS SctNo, i.MajorDept DptNo, md.Description DptName, d.Description SctName,
				(SELECT TOP 1 ind.Date FROM dbo.InventoryDetail ind WHERE ind.RecordType = 'R' AND ind.UPC=id.UPC ORDER BY ind.LastUpdated DESC, ind.Date DESC) AS lastReceivingDate,
				ISNULL((SELECT SUM(ind.Units) FROM dbo.InventoryDetail ind WHERE ind.RecordType = 'R' AND ind.UPC=id.UPC AND ind.Date = (SELECT TOP 1 ind.Date FROM dbo.InventoryDetail 
				ind WHERE ind.RecordType = 'R' AND ind.UPC=id.UPC ORDER BY id.LastUpdated DESC, id.Date DESC)),0) AS lastReceiving
				FROM dbo.InventoryDetail id 
				INNER JOIN dbo.Item i ON i.UPC = id.UPC
				INNER JOIN dbo.MajorDept md ON md.MajorDept = i.MajorDept
				INNER JOIN dbo.Departments d ON d.Department = i.Department
				WHERE id.Units > 0 AND id.RecordType = 'A' AND id.Date = '2017-07-04'";
		// Execute query
		$results = $this->db->query($SQL);
		// print_r($this->db->errorInfo());die();
		$report = $results->fetchall(PDO::FETCH_BOTH);

		return $report ;
	}
}