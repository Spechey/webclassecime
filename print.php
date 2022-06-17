<?php
include_once("lib/conf.php");
include_once("lib/auth.php");
if(!isset($saisie))
	$saisie=false;
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
		echo "<li class='".(($mchs->id == @$_GET["mch"])?"active":"")."'><a target='print' href='print_resultats_all.php?evs=".$_GET["evs"]."&eps=".$_GET["eps"]."&mch=".$mchs->id."'7>".$mchs->data["Libelle_niveau"]."</a></li>";
	}
	echo "</ul></div>";
	// auto load manche si une seul
	if (sizeof($mchss) == 1)
	{
		$_GET["mch"] = $mchss[0]->id;
	}	
}


?>

</body>
</html>