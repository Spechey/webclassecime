<?php
include_once("lib/conf.php");
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
	
		$b=$_GET["block"];
		
		$mchs = new Manche($_GET["mch"]);
	
		$bps = $mchs->getPointsBlocs();
		
		
		
		echo "<h1>Block n° $b</h2><table class=resultats>";

		
		
		$cs = $evs->getConcurents();
		if (!isset($coureurListHeaders) || sizeof($coureurListHeaders) == 0)
				$coureurDetailsHeaders = array_keys($cs[0]->data);
		echo helpers_tableLine($coureurListHeaders,array(),"th","<th>Block n°".$b."</th>");
		foreach($cs as $c)
		{
		
			
			
			
			 $cBck =	new CoureurBlock (array(
						"Code_evenement"=>$c->data["Code_evenement"],
						"Code_coureur"=>$c->data["Code_coureur"],
						"Code_manche"=>((floor($mchs->id/1000)*1000)+$b)
					)
			 ,array(
							"pts"=>((isset($c->data["BlocsInfos"]["Details"][$b]))?$c->data["BlocsInfos"]["Details"][$b]:"-"),
							"Status"=>((isset($c->data["BlocsInfos"]["Details"][$b]))?"O":false)
					));	
			
			$cBckList = $c->getResultat();
			
			
			
			
			$cBck = $cBckList[$b];
			
			$action = "<td class='cBck-".$cBck->id." action ".$cBck->isValideString()."'><a onclick=\"cBck_update('cBck-".$cBck->id."','".(($cBck->isValide())?"cBckDel":"cBckAdd")."','".$cBck->id."')\">".(($cBck->isValide())?"-":"+")."</a></td>";
			$action .= "<td>".$c->data["Dossard"]."</td>";
			
			
			echo helpers_tableLine($c->data,$coureurListHeaders,"td",$action);
		}
		echo "</table>";
		
		
		
		
			
	
}	
	



?>

</body>
</html>