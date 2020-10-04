<?php
include_once("lib/conf.php");
include_once("lib/auth.php");
$saisie=false;
$printBlock=false;
?>
<html>
<head>
<?php include_once("lib/header.php")?>
</head>
<body>
<?php
// liste des epeuves pour cet evenement
if (isset($_GET["evs"]))
{
	$evs = new Evenement($_GET["evs"]);
}

if (isset($_GET["eps"]))
{
	$eps = new Epreuve($_GET["eps"]);
}
	
if (isset($_GET["mch"]))
{
		$mchs = new Manche($_GET["mch"]);
	
	
		if (isset($_GET["filter"]))
		{
			reset($_GET["filter"]);
			foreach($_GET["filter"] as $name => $value)
			{
				$mchs->addFilter($name,$value);
			}
		}	
	
		$bps = $mchs->getPointsBlocs();

		echo "<table class=resultats>";
		echo "<tr>";
		
		
		$cs = $mchs->getResultatsByCoureurs();
		if (!isset($coureurResultPrintHeaders) || sizeof($coureurResultPrintHeaders) == 0)
		{
				$coureurResultPrintHeaders = array_keys($cs[0]->data);
				$coureurResultPrintHeaders[] = "Total blocs";
				$coureurResultPrintHeaders[] = "Total points";
		}
		
		
		
		echo helpers_tableCels($coureurResultPrintHeaders,array(),"th","rowspan=2");
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
		if (1)
		foreach($cs as $c)
		{

			
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
			
			echo helpers_tableCels($c->data,$coureurResultPrintHeaders,"td");
			
			
			
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
			
	
}	
	



?>
<script type="text/javascript">
print();
</script>
</body>
</html>