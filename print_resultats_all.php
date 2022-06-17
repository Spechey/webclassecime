<?php
include_once("lib/conf.php");
include_once("lib/auth.php");
$saisie=false;
$printBlock=false;
?>
<html>
<head>
<?php include_once("lib/header.php")?>
<style type="text/css">
@media print {
    .pagebreak { page-break-before: always; } /* page-break-after works, as well */
}
</style>
</head>
<body>
<?php


function printRes($evs,$mch,$filter,$printBlock) {
	
	    $mchs = new Manche($mch);
		$h2 = array();
		if (isset($filter["filter"]))
		{
			reset($filter["filter"]);
			foreach($filter["filter"] as $name => $value)
			{
				$mchs->addFilter($name,$value);
				if (true || !isset($coureurResultPrintHeaders) || in_array($name,$coureurResultPrintHeaders))
				{
					$h2[] = $value;
				}
			}
		}
		$bps = $mchs->getPointsBlocs();
		$cs  = $mchs->getResultatsByCoureurs();
		//if (sizeof($cs) <= 0)
		//	return;
		
		echo "<h2>".implode($h2," - ")."</h2>";

		echo "<table class=resultats>";
		echo "<tr>";
		
		
		
		if (!isset($coureurResultPrintHeaders) || sizeof($coureurResultPrintHeaders) == 0)
		{
				$coureurResultPrintHeaders = @array_keys($cs[0]->data);
				$coureurResultPrintHeaders[] = "Total blocs";
				$coureurResultPrintHeaders[] = "Total points";
		}
		
		
		
		echo helpers_tableCels($GLOBALS['coureurResultPrintHeaders'],array(),"th","rowspan=2");
			/*echo "<th rowspan=2>Classement</th>";			
			echo "<th rowspan=2>n°</th>";
			echo "<th rowspan=2>Nom</th>";
			echo "<th rowspan=2>Club</th>";
			echo "<th rowspan=2>Categorie</th>";
			echo "<th rowspan=2>Groupe</th>";
			echo "<th rowspan=2>Total blocs</th>";
			echo "<th rowspan=2>Total points</th>";*/
		
		$l1 = "";
		$l2 = "";
		if ($printBlock)
		foreach($bps as $b => $p)
		{
			$l1 .=  "<th>n°$b</th>";
			$l2 .=  "<th class='pts'>$p pts</th>";
			
		}
		echo $l1."</tr><tr>".$l2."</tr>";
		
		
		
		
		
		//print_r($mchs->getResultats());
		$nb = 0;
		foreach($cs as $c)
		{
			if ($nb++ > $_GET["max"])
				continue;

			
			echo "<tr>";
			
			/*
			echo "<th>".$c->data["Classement"]."</th>";
			echo "<th><a name='".$c->data["Code_coureur"]."'>".$c->data["Code_coureur"]."</a></th>";
			echo "<th>".$c->data["Nom"]." ".$c->data["Prenom"]."</th>";
			echo "<th>".$c->data["Club"]."</th>";
			echo "<th>".$c->data["Categ"]." ".$c->data["Sexe"]."</th>";
			echo "<th>".$c->data["Groupe"]."</th>";
			echo "<th>".$c->data["BlocsInfos"]["TotalBlocs"]."</th>";
			echo "<th>".$c->data["BlocsInfos"]["TotalPoints"]."</th>";
			*/
			
			$c->data["Total blocs"] = $c->data["BlocsInfos"]["TotalBlocs"];
			$c->data["Total points"] = $c->data["BlocsInfos"]["TotalPoints"];
			
			echo helpers_tableCels($c->data,$GLOBALS['coureurResultPrintHeaders'],"td");
			
			
			
			if ($printBlock)
			{
				reset($bps);
				foreach($bps as $b => $p)
				{
					//var_dump($c);
					
					if (isset($c->data["BlocsInfos"]["Details"]))
					{
							$cBck =	new CoureurBlock (array(
																"Code_evenement"=>$c->data["Code_evenement"],
																"Code_coureur"=>$c->data["Code_coureur"],
																"Code_manche"=>((floor($c->data["Code_manche"]/1000)*1000)+$b)
															)
													 ,array(
																	"pts"=>((isset($c->data["BlocsInfos"]["Details"][$b]))?$c->data["BlocsInfos"]["Details"][$b]:"-"),
																	"Status"=>((isset($c->data["BlocsInfos"]["Details"][$b]))?"O":false)
															));	
						if ($saisie)
							echo "<td class='cBck-".$cBck->id." action ".$cBck->isValideString()."' btnSaisie><a onclick=\"cBck_update('cBck-".$cBck->id."','".(($cBck->isValide())?"cBckDel":"cBckAdd")."','".$cBck->id."')\">".(($cBck->isValide())?"-":"+")."</a></td>";
						else
							echo "<td class='cBck-".$cBck->id." action ".$cBck->isValideString()."' btnSaisie>".(($cBck->isValide())?"-":"+")."</td>";
					}
					else
					{
						echo "<td>err</td>";
					}
					
				}
			}
			echo "</tr>";
		}
		echo "<table>";
			
	

echo '<div id="president">Président du jury : <b>'.$evs->getPresidentJury().'</b>&#160;&#160;&#160;&#160;&#160;&#160;&#160;-&#160;&#160;&#160;&#160;&#160;&#160;Signature : </div>';
	
	echo "<div class='pagebreak'> </div>";
}


function printResRec($evs,$mch,$groupBys,$printBlock,$filterP=null) {
	if ($filterP === null) {
		$filterP = array();
		$filterP["filter"] = array();
	}
	
	$key =  (key($groupBys));
	$groupBy = array_shift($groupBys);
	foreach ($groupBy as $value){
		if ($value === "*")
			continue;
		if (is_array($GLOBALS["printAllFilterValues"][$key]) && !array_search($value,$GLOBALS["printAllFilterValues"][$key]))
			continue;
		$filterP["filter"][$key] = $value;
		//print_r($filterP);
		printRes($evs,$mch,$filterP,$printBlock);
		if (sizeof($groupBys) > 0) {
			printResRec($evs,$mch,$groupBys,$printBlock,$filterP);
		}
		
	}
}



$evs = $_GET["evs"];
$eps = $_GET["eps"];
$mch = $_GET["mch"];
if (!isset($_GET["max"])) { $_GET["max"] = 10000; }


$evs = new Evenement($evs);
$mchs = new Manche($mch);
$groupBys = $mchs->getFiltersValues(array_keys($printAllFilterValues));

printResRec($evs,$mch,$groupBys,$printBlock);


?>
<script type="text/javascript">
// print();
</script>
</body>
</html>