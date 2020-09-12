<?php
include_once("lib/conf.php");
include_once("lib/auth.php");
if(!isset($saisie))
	$saisie=true;
?>
<html>
<head>
<?php include_once("lib/header.php")?>
</head>
<body>
<ul class="evt"><?php helpers_listEpreuves(); ?></ul>
<div class="msg"><?= ($msg)?$msg:"&#160;" ?></div>

<?php

// liste des epeuves pour cet evenement
if (isset($_GET["evs"]))
{
	$evs = new Evenement($_GET["evs"]);
	
	echo "<div class='headerActions'><h2>Epreuve(s)</h2><ul class=epr>";
	$epss = $evs->getEpreuves();
	foreach($epss as $eps)
	{
		echo "<li class='".(($eps->id == @$_GET["evs"])?"active":"")."'><a href='?evs=".$_GET["evs"]."&eps=".$eps->id."'>".$eps->data["Code_categorie"]." (".$eps->data["Sexe"]." : ".$eps->data["Distance"].")</a></li>";
	}
	echo "</ul></div>";
 
}

if (isset($_GET["eps"]))
{
	$eps = new Epreuve($_GET["eps"]);
	
	echo "<div class='headerActions'><h2>Manche(s)</h2><ul class=mch>";
	$mchss = $eps->getManches();
	foreach($mchss as $mchs)
	{
		echo "<li class='".(($mchs->id == @$_GET["mch"])?"active":"")."'><a href='?evs=".$_GET["evs"]."&eps=".$_GET["eps"]."&mch=".$mchs->id."'7>".$mchs->data["Libelle_niveau"]."</a></li>";
	}
	echo "</ul></div>";
	// auto load manche si une seul
	if (sizeof($mchss) == 1)
	{
		$_GET["mch"] = $mchss[0]->id;
	}	
}
	
if (isset($_GET["mch"]))
{
		$mchs = new Manche($_GET["mch"]);
	
		$bps = $mchs->getPointsBlocs();

		
		$filters = $mchs->getFiltersValues($saisieFilterValues);
		echo "<div class='headerActions'><h2>Filtre(s)</h2>";
		foreach($filters as $filter_name => $filter_values)
		{
			echo "<ul class=filter><lI>$filter_name</li><ul>";
			
			for($i=0;$i<sizeof($filter_values);$i++)
			{
				if ($filter_values[$i] == null)
					$filter_values[$i] = "";
				
				echo "<li class='".((isset($_GET["filterValue"]) && $_GET["filterValue"] == $filter_values[$i] && isset($_GET["filterName"]) && $_GET["filterName"] == $filter_name)?"active":"")."'><a href='?evs=".$_GET["evs"]."&eps=".$_GET["eps"]."&mch=".$mchs->id."&filterName=".$filter_name."&filterValue=".urlencode($filter_values[$i])."' >".(($filter_values[$i])?(($filter_values[$i]=="*")?"[Avec]":$filter_values[$i]):"[Sans]")."</a></li>";
			}
			echo "</ul></ul>";
		}
		
		if (isset($_GET["filterName"]) && isset($_GET["filterValue"]))
		{
			$mchs->addFilter($_GET["filterName"],$_GET["filterValue"]);
		}
		echo "</div>";

		echo "<table class=resultats>";
		echo "<tr>";
		
		
		$cs = $mchs->getResultatsByCoureurs();
		if (!isset($coureurResultHeaders) || sizeof($coureurResultHeaders) == 0)
		{
				$coureurResultHeaders = array_keys($cs[0]->data);
				$coureurResultHeaders[] = "Total blocs";
				$coureurResultHeaders[] = "Total points";
		}
		
		
		
		echo helpers_tableCels($coureurResultHeaders,array(),"th","rowspan=2");
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
			
			echo helpers_tableCels($c->data,$coureurResultHeaders,"td");
			
			
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
			echo "</tr>";
		}
		echo "<table>";
			
	
}	
	



?>

</body>
</html>