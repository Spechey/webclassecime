<?php
include_once("../lib/conf.php");
?>

<!doctype html>
<html>
<head>
    <!-- Required meta tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	
	<title>Live ReadyToGrimpe</title>
	<link rel="stylesheet" href="css/rtg.css" />	
	<link rel="stylesheet" href="../bootstrap-4.4.1-dist/css/bootstrap.min.css">
	<script   src="../js/jquery-3.4.1.min.js"></script>	
	<script src="../bootstrap-4.4.1-dist/js/bootstrap.min.js"></script>
	<script type="text/javascript">

	</script>
	
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="#">ReadyToGrimpe - Live !</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <div class="navbar-nav mr-auto">
    <div class="btn-group">
		<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			Epreuves
		</button>
		<div class="dropdown-menu" aria-labelledby="navbarDropdown">
		<?php 
			foreach($GLOBALS["cc"]->getEvenements() as $evs)
			{
					$evs = new Evenement($evs->id);
					echo "<p class=\"btn-primary\">".$evs->data["Nom"]."<p>";
				
					$epss = $evs->getEpreuves();
					foreach($epss as $eps)
					{
						echo '<a class="dropdown-item" href="?evs='.$evs->id.'&eps='.$eps->id.'" id="navbarDropdown" role="button">'.(($eps->data["Code_categorie"] == "*")?"Scratch":$eps->data["Code_categorie"]).' ('.(($eps->data["Sexe"]=="T")?"Mixte":$eps->data["Sexe"]).')</a>';
						if (!isset($_GET["eps"]))
						{
							$_GET["evs"] = $evs->id;
							$_GET["eps"] = $eps->id;
						}
					}
			}
			?>
		</div>
	</div>&#160;
	<?php // is il ya plusieur manche
	
	
	if (isset($_GET["eps"]))
	{
		$eps = new Epreuve($_GET["eps"]);
		$mchss = $eps->getManches();
		// auto load manche si une seul
		if (sizeof($mchss) == 1)
		{
			$_GET["mch"] = $mchss[0]->id;
		}
		//if (true) {
		else{
			?>
			<div class="btn-group">
			<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				Manches
			</button>
			<div class="dropdown-menu" aria-labelledby="navbarDropdown"><?php
		foreach($mchss as $mchs)
		{
			echo '<a class="dropdown-item" href="?evs='.$_GET["evs"].'&eps='.$_GET["eps"].'&mch='.$mchs->id.'" id="navbarDropdown" role="button">'.$mchs->data["Libelle_niveau"].' </a>';
		}
		?></div></div><?php
		}// fin else
	}
	if (isset($_GET["mch"]))
	{
	$mchs = new Manche($_GET["mch"]);
	// les filtres
	?><div class="btn-group">
		<button class="btn btn-warning dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			Filtres
		</button>
		<div class="dropdown-menu" aria-labelledby="navbarDropdown"><?php	
				echo '<a class="dropdown-item" href="?evs='.$_GET["evs"]."&eps=".$_GET["eps"]."&mch=".$mchs->id.'" id="navbarDropdown" role="button">Supprimer le filtre</a>';				
		$filters = $mchs->getFiltersValues();
		foreach($filters as $filter_name => $filter_values)
		{
			echo "<p class=\"btn-warning\">$filter_name</p>";
			for($i=0;$i<sizeof($filter_values);$i++)
			{
				if ($filter_values[$i] == null)
					$filter_values[$i] = "";

				echo '<a class="dropdown-item" href="?evs='.$_GET["evs"]."&eps=".$_GET["eps"]."&mch=".$mchs->id."&filterName=".$filter_name."&filterValue=".urlencode($filter_values[$i]).'" id="navbarDropdown" role="button">'.(($filter_values[$i])?$filter_values[$i]:"[sans indication]").' </a>';				
			}
		}	
	?></div></div><?php } ?>
	
	</div>
	<!--<div class="form-inline my-2 my-lg-0" >
      <input id="search" name="search" class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="search" onKeyPress="doFilter();">
	  <button class="btn btn-outline-success my-2 my-sm-0" onClick="clearFilter();">Effacer</button>
    </div>-->
  </div>
</nav>
<script type="text/javascript">

</script>
<div id="result" class="container-fluid">
<?php if (isset($_GET["mch"])) { ?>
<div id="header">
<h1><?=(new Evenement($_GET["evs"]))->data["Nom"] ?></h1>
<h2><?php 
$ep = new Epreuve($_GET["eps"]);
echo (($ep->data["Code_categorie"] == "*")?"Scratch":$ep->data["Code_categorie"]).' ('.(($ep->data["Sexe"]=="T")?"Mixte":$ep->data["Sexe"]).")";

if (isset($_GET["filterName"]) && isset($_GET["filterValue"]))
{
	echo '</h2><h2><button class="btn btn-warning"><b>Filtre</b> '.$_GET["filterName"]." : ".$_GET["filterValue"]."</button></h2>";
}

?>

</h2>
<div id="message"><b>Résultats et scores à titre informatif !!</b></div>
</div>
 <div class="container list-group">
<?php


if (isset($_GET["mch"]))
{
		$mchs = new Manche($_GET["mch"]);
		$bps = $mchs->getPointsBlocs();
		if (isset($_GET["filterName"]) && isset($_GET["filterValue"]))
		{
			$mchs->addFilter($_GET["filterName"],$_GET["filterValue"]);
		}

		
		
		


		

		foreach($mchs->getResultatsByCoureurs() as $c)
		{
			
			$result = "";
			reset($bps);
			foreach($bps as $b => $p)
			{
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
														
					$result .= '<div class="bloc-result"><div class="bloc-id">'.$b.'</div><div class="bloc-top '.(($cBck->isValide())?"bloc-valid":"").'">'.floor($p).'</div></div>';
				}
			}			
			
			
			
			$scores = "<div class='scores float-right'><div class='points'>".$c->data["BlocsInfos"]["TotalPoints"]."<span>pts</span></div><div class='top'>".$c->data["BlocsInfos"]["TotalBlocs"]."<span>blocs</span></div></div>";
			//print_r($c);
			echo "<li class='list-group-item concurent contest'><div>".$scores."<h1 class='float-left order'>".$c->data["Classement"]."</h1><span class='sexe'>".$c->data["Sexe"]."</span><span class='identity'>".$c->data["Nom"]." ".$c->data["Prenom"]."</span><span class='categ'>".$c->data["Categ"]."</span><span class='club'>".$c->data["Club"]."</span></div><div class='float-none blocs-result'>".$result."</div></li>";
			
		

		}
}

				
				 



?>
</div>
<?php } ?>
</div>
</body>
</html>

