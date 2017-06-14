<?php include 'tablenav.php';?>
<?php 
$increment = 0; 
$condition = 'ht' ;
$elementCount = count($data['thead']);
$totalElements = count($data['report']);
$tdCount = $elementCount - 3;
if(!empty($data['report']) && $data['report'] != null && $data['report'] != false && count($data['report']) != 0)
{
    echo "<thead class='thead_position'><tr>";
    echo "<tr><th colspan='6' class='vendorOne'></th><th colspan='6' class='vendorTwo'></th><th colspan='6' class='vendorEqual'></th></tr>";
    for($j = 0; $j < $elementCount; $j++)
    {
        echo "<th>" . $data['thead'][$j] . "</th>";
    }
    echo "</tr></thead><tbody>";
    $vdrOne = 0;
    $vdrEqual = 0;
    $vendorPercent[1] = array( "name" => "", "number" => "",  "percent" => 0);
    $vendorPercent[0] = array( "name" => "", "number" => "",  "percent" => 0);
    $vendorPercent[2] = array( "name" => "", "number" => "",  "percent" => 0);
        $vdrTwo = 0;
	for ($i = 0; $i < count($data['report']); $i++) 
    {
        $onhandClass = "positive"; 
    	if($increment == 0 || $condition != $data['report'][$i]['SctNo'])
    	{
			echo '<tr class = "section_name"><td></td><td></td>';
            echo '<td class="SectionName">SECTION '.$data['report'][$i]['SctNo'].' - '.$data['report'][$i]['SctName'].'</td>';
            for($k = 0; $k < $tdCount; $k++)
            {
                echo '<td></td>';
            }
            echo '</tr>';
		}
    	if(floor($data['report'][$i]["onhand"] < 0))
    	{
    		$onhandClass = "negative";
    	}
        $data['report'][$i]["onhand"] = floor($data['report'][$i]["onhand"]);
        $data['report'][$i]["lastReceivingOne"] = abs($data['report'][$i]["lastReceivingOne"]);
        $data['report'][$i]["lastReceivingTwo"] = abs($data['report'][$i]["lastReceivingTwo"]);
        $data['report'][$i]["sales"] = abs(floor($data['report'][$i]["sales"]));
        echo "<tr>";
        
        if(round($data['report'][$i]["unitPriceOne"]) > round($data['report'][$i]["unitPriceTwo"]))
    	{
    		$vdrOne = $vdrOne + 1;
    		$vendorPercent[0] = array("name" => $data['report'][$i]["VdrNameTwo"], "number" => str_replace(" ", "",$data['report'][$i]["VdrNoTwo"]), "percent" => round((100 * $vdrOne)/$totalElements));
    		$firstArray = $data['qt'][8];
    		$secondArray = $data['qt'][9];
    		$data['qt'][8] = $secondArray;
    		$data['qt'][9] = $firstArray;
    	}
    	else
    	{
    		if($data['report'][$i]["unitPriceOne"] == $data['report'][$i]["unitPriceTwo"])
    		{
    			$vdrEqual = $vdrEqual + 1;
    			$vendorPercent[2] = array( "name" => "", "number" => "",  "percent" => round((100 * $vdrEqual)/$totalElements));
    		}
    		else
    		{
    			$vdrTwo = $vdrTwo + 1;
    			$vendorPercent[1] = array( "name" => $data['report'][$i]["VdrNameOne"], "number" => str_replace(" ", "",$data['report'][$i]["VdrNoOne"]),  "percent" => round((100 * $vdrTwo)/$totalElements));
    		}
    		
    	}
        for($l=0; $l < count($data['qt']); $l++)
        {
            if($data["qt"][$l] == "Retail")
            {
                echo "<td rowspan= '2' class='" . $data["qt"][$l] . " compareUs'>$" . $data['report'][$i][$data["qt"][$l]] . "</td>";
            }
            else
            {
                if($data["qt"][$l] == "onhand")
                {
                    echo "<td rowspan= '2' class='compareUs " . $data["qt"][$l] . " " . $onhandClass . "'>" . $data['report'][$i][$data["qt"][$l]] . "</td>";
                }
                else
                {
                	if($l == 8)
                	{
                		for($k = 0; $k < count($data["qt"][$l]); $k++)
                		{
                			if($data["qt"][$l][$k] == "lastReceivingOne" && empty($data['report'][$i]["lastReceivingDateOne"]))
		                    {
		                        echo "<td class='" . $data["qt"][$l][$k] . " bg-success'></td>";
		                    }
		                    else
		                    {
		                    	if($data["qt"][$l][$k] == "CertCodeTwo" || $data["qt"][$l][$k] == "CertCodeOne")
		                    	{
		                    		echo "<td class='" . $data["qt"][$l][$k] . " bg-success'>
		                    		<a href = '/csm/public/home/vendorItemCode_url/" . str_replace(' ', '', $data['report'][$i][$data["qt"][$l][$k]]) . "'>" . str_replace(' ', '', $data['report'][$i][$data["qt"][$l][$k]]) . "</a></td>";
		                    	}
		                    	else
		                    	{
		                    		if($data["qt"][$l][$k] == "VdrNameTwo" || $data["qt"][$l][$k] == "VdrNameOne")
			                    	{
			                    		echo "<td class='" . $data["qt"][$l][$k] . " bg-success'>
			                    		<a href = '/csm/public/home/vendor_url/" . $data['report'][$i]['VdrNoOne'] . "'>" . $data['report'][$i][$data["qt"][$l][$k]] . "</a></td>";
			                    	}
			                    	else
			                    	{
			                    		if($data["qt"][$l][$k] == "CaseCostTwo" || $data["qt"][$l][$k] == "CaseCostOne")
			                    		{
			                    			echo "<td class='" . $data["qt"][$l][$k] . " bg-success'>" . number_format($data['report'][$i][$data["qt"][$l][$k]], 2, ".", '') . "</td>";
			                    		}
			                    		else
			                    		{
			                    			if($data["qt"][$l][$k] == "unitPriceTwo" || $data["qt"][$l][$k] == "unitPriceOne")
			                    			{
			                    				echo "<td class='" . $data["qt"][$l][$k] . " bg-success'>" . number_format($data['report'][$i][$data["qt"][$l][$k]], 2, '.', '') . "</td>";
			                    			}
			                    			else
			                    			{
			                    				echo "<td class='" . $data["qt"][$l][$k] . " bg-success'>" . $data['report'][$i][$data["qt"][$l][$k]] . "</td>";
			                    			}
			                    		}
			                    	}
		                    	}
		                    }
                		}
                	}
                	else
                	{
                		if($l == 9)
	                	{
	                		echo "</tr><tr class='bg-danger'>";
	                		for($k = 0; $k < count($data["qt"][$l]); $k++)
	                		{
	                			if($data["qt"][$l][$k] == "lastReceivingTwo" && empty($data['report'][$i]["lastReceivingDateTwo"]))
			                    {
			                        echo "<td class='" . $data["qt"][$l][$k] . "'></td>";
			                    }
			                    else
			                    {
			                    	if($data["qt"][$l][$k] == "CertCodeTwo" || $data["qt"][$l][$k] == "CertCodeOne")
			                    	{
			                    		echo "<td class='" . $data["qt"][$l][$k] . "'>
			                    		<a href = '/csm/public/home/vendorItemCode_url/" . str_replace(' ', '', $data['report'][$i][$data["qt"][$l][$k]]) . "'>" . str_replace(' ', '', $data['report'][$i][$data["qt"][$l][$k]]) . "</a></td>";
			                    	}
			                    	else
			                    	{
			                    		if($data["qt"][$l][$k] == "VdrNameTwo" || $data["qt"][$l][$k] == "VdrNameOne")
				                    	{
				                    		echo "<td class='" . $data["qt"][$l][$k] . "'>
				                    		<a href = '/csm/public/home/vendor_url/" . $data['report'][$i]['VdrNoTwo'] . "'>" . $data['report'][$i][$data["qt"][$l][$k]] . "</a></td>";
				                    	}
				                    	else
				                    	{
				                    		if($data["qt"][$l][$k] == "CaseCostTwo" || $data["qt"][$l][$k] == "CaseCostOne")
				                    		{
				                    			echo "<td class='" . $data["qt"][$l][$k] . "'>" . number_format($data['report'][$i][$data["qt"][$l][$k]], 2, '.', '') . "</td>";
				                    		}
				                    		else
				                    		{
				                    			if($data["qt"][$l][$k] == "unitPriceTwo" || $data["qt"][$l][$k] == "unitPriceOne")
				                    			{
				                    				echo "<td class='" . $data["qt"][$l][$k] . "'>" . number_format($data['report'][$i][$data["qt"][$l][$k]], 2, '.', '')  . "</td>";
				                    			}
				                    			else
				                    			{
				                    				echo "<td class='" . $data["qt"][$l][$k] . "'>" . $data['report'][$i][$data["qt"][$l][$k]] . "</td>";
				                    			}
				                    		}
				                    	}
			                    	}
			                    }
	                		}
	                		echo "</tr>";
	                	}
	                	else
	                	{
	                		if($data["qt"][$l] == "UPC")
	                        {
	                            echo "<td rowspan='2' class='compareUs " . $data["qt"][$l] . " " . $onhandClass . "'>
	                            <a href = '/csm/public/home/UPCPriceCompare_url/" . $data['report'][$i][$data["qt"][$l]] . "'>" . $data['report'][$i][$data["qt"][$l]] . "
	                            </a></td>";
	                        }
	                        else
	                        {
	                        	if(($data["qt"][$l] == "tpr" && $data['report'][$i]["tpr"] == ".00")
		                        || ($data["qt"][$l] == "tprStart" && $data['report'][$i]["tpr"] == ".00")
		                        || ($data["qt"][$l] == "tprEnd" && $data['report'][$i]["tpr"] == ".00"))
			                    {
			                        echo "<td rowspan = '2' class='" . $data["qt"][$l] . "'></td>";
			                    }
			                    else
			                    {
			                    	echo "<td rowspan = '2' class= 'compareUs " . $data["qt"][$l] . "'>" . $data['report'][$i][$data["qt"][$l]] . "</td>";
			                    }
	                        }
		                }
	               	}
                }
            }
        }
        if(round($data['report'][$i]["unitPriceOne"]) > round($data['report'][$i]["unitPriceTwo"]))
    	{
    		$firstArray = $data['qt'][8];
    		$secondArray = $data['qt'][9];
    		$data['qt'][8] = $secondArray;
    		$data['qt'][9] = $firstArray;
    	}
	    $increment = $increment + 1 ;
	    $condition = $data['report'][$i]['SctNo'];
    }
}
else
{
	echo "<a href='/csm/public/home/'><p class='text-warning errortext'>THE REPORT DID NOT GENERATE ANY RESULTS. PLEASE CHECK THE UPC NUMBER. DID YOU ENTER THE RIGHT SALES DATES ?</p></a>";
}
?>
</tbody>
</table>
</div>
</div>
</div>
<span id = "vendor1" style = "display:none"><?= "VENDOR " . $vendorPercent[0]['number'] . " - " . strtoupper($vendorPercent[0]['name']) . " : " . $vendorPercent[0]['percent'] . "%" ?></span>
<span id = "vendor2" style = "display:none"><?= "VENDOR " . $vendorPercent[1]['number'] . " - " . strtoupper($vendorPercent[1]['name']) . " : " . $vendorPercent[1]['percent'] . "%" ?></span>
<span id = "vendor3" style = "display:none"><?= "EQUAL PRICES " . " : " . $vendorPercent[2]['percent'] . "%" ?></span>