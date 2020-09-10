<?php
include_once("lib/conf.php");
include_once("lib/auth.php");
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
	$epss = $evs->getEpreuves();
	$eps = 	$epss[0];
	$mchss = $eps->getManches();
	$_GET["mch"] = $mchss[0]->id;
?>

<form>
<input type="hidden" name="evs" value="<?=@$_GET["evs"]?>"/>
Block n°<input type="integer" name="block" value="<?=@$_GET["block"]?>" />
<input type="submit">
</form>

<?php
}



if (isset($_GET["mch"]) && isset($_GET["block"]))
{
	
		$b=explode(",",$_GET["block"]);
		
		$mchs = new Manche($_GET["mch"]);
	
		$bps = $mchs->getPointsBlocs();
		
		
		
		echo "<h1>Block n° ".implode(" - n°",$b)."</h1><table class=resultats>";

		
		
		$cs = $evs->getConcurents();
		if (!isset($coureurListByBlocHeaders) || sizeof($coureurListByBlocHeaders) == 0)
				$coureurListByBlocHeaders = array_keys($cs[0]->data);
		$hB = "";
		for ($i=0;$i<sizeof($b);$i++)
			$hB .= "<th>Block n°".$b[$i]."</th>";
			
		echo helpers_tableLine($coureurListByBlocHeaders,array(),"th",$hB);
		foreach($cs as $c)
		{
		
			$action = "";
			for ($i=0;$i<sizeof($b);$i++)
			{
			
				 $cBck =	new CoureurBlock (array(
							"Code_evenement"=>$c->data["Code_evenement"],
							"Code_coureur"=>$c->data["Code_coureur"],
							"Code_manche"=>((floor($mchs->id/1000)*1000)+$b[$i])
						)
				 ,array(
								"pts"=>((isset($c->data["BlocsInfos"]["Details"][$b[$i]]))?$c->data["BlocsInfos"]["Details"][$b[$i]]:"-"),
								"Status"=>((isset($c->data["BlocsInfos"]["Details"][$b[$i]]))?"O":false)
						));	
				
				$cBckList = $c->getResultat();
				
				
				
				
				$cBck = $cBckList[$b[$i]];
				
				$action .= "<td class='cBck-".$cBck->id." action ".$cBck->isValideString()."'><a onclick=\"cBck_update('cBck-".$cBck->id."','".(($cBck->isValide())?"cBckDel":"cBckAdd")."','".$cBck->id."')\">".(($cBck->isValide())?"-":"+")."</a></td>";
			}
			//$action .= "<td>".$c->data["Dossard"]."</td>";
			
			
			echo helpers_tableLine($c->data,$coureurListByBlocHeaders,"td",$action);
		}
		echo "</table>";
		
		
		
		
			
	
}	
	



?>

</body>
</html>