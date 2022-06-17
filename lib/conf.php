<?php
// $message="Résultats et scores à titre informatif.<br/>Aucune saisie pour le moment.";

$message="Résultats Vague 3";


include_once("bdd.php");

// fichier de demo
$dbbfile="bdd/ffme.db3";

// fichier trouver sur le disque
if (is_file("C:\\ClassCimes\\base\\ffme.db3"))
	$dbbfile="C:\\ClassCimes\\base\\ffme.db3";


// fichier trouver sur le disque
if (is_file("D:\\RTG\\ClassCimes\\base\\ffme.db3"))
	$dbbfile="D:\\RTG\\ClassCimes\\base\\ffme.db3";


classecime::$bdd = new SQLite3($dbbfile);
classecime::$bdd->enableExceptions(true);
$cc = new classecime();

// faire recalculer les points en foonction des filtres (oui : true, non: false)
classecime::$FiltresRecalculPoints = true;

/* gestion des zones
2 => zones
1 => sans zones
*/ 

$modulo = 1;
$suffixModulo[0]["titre"]       = "Top";
$suffixModulo[0]["initial"]     = "T";
$suffixModulo[0]["class"]       = "bloc-top";
if ($modulo > 1) { 
	$suffixModulo[1]["titre"]       = "Bonus";
	$suffixModulo[1]["initial"]     = "B";
	$suffixModulo[1]["class"]       = "bloc-bonus";
}

// point par blocs
classecime::$blocsPoints = floor(1000 / $modulo);


$titreEvenement = "Commentaire";

//saisie par grimpeur
$coureurResultHeaders = array(
		"Classement",
		"Code_coureur",
		"Dossard",
		"Nom",
		"Prenom",
		"Sexe",
		//"Nation",
		"Club",
		"Categ",
		"Certificat_Medical",
		"Total blocs",
		"Total points",
	);
$coureurResultPrintHeaders = array(
		"Classement",
		"Dossard",
		"Nom",
		"Prenom",
		"Sexe",
		"Club",
		"Categ",
		//"Total blocs",
		"Total points",
	);
$coureurListHeaders = array(
		"Code_coureur",
		"Dossard",
		"Nom",
		"Prenom",
		"Sexe",
		//"Nation",
		"Club",
		"Categ",
		"Certificat_Medical",
	);
 
//saisie par bloc
$coureurDetailsHeaders = array(
		"Code_coureur",
		"Dossard",
		"Nom",
		"Prenom",
		"Sexe",
		"Nation",
		"Club",
		"Categ",
	);
$coureurListByBlocHeaders = array(
		"Categ",
		"Club",
		"Nom",
		"Prenom",
		"Dossard",		
	);	
	
	
$liveFilterValues =  array(
		"Categ", // une epreuve par categogie et pas toutes dans une categorie
		"Sexe",
		"Club", // juste le filtre
		"Dept",
		"Ligue");
$saisieFilterValues = array("Categ",
							"Sexe",
							"Club",
							"Dept",
							"Ligue",
							"Certificat_Medical");
$printAllFilterValues = array(
		"Categ" =>true, // une epreuve par categogie et pas toutes dans une categorie
		"Sexe"  =>true,
		// "Dept"  => array("001","021","071"),
	);

$liveRefreshseconde=30;

$login = "rtg";
$password = "rtg";


/********************************************/
/* ne pas modifier ! ce qu'il y a dessous ! */
/********************************************/

$codesCaterogies = array( // présent dans la base, mais ça fait gagner du temps de l'avoir en static ici
	"U8"       => "U8",
	"MICROBE"  => "U10",
	"POUSSIN"  => "U12",
	"BENJAMIN" => "U14",
	"MINIME"   => "U16",
	"CADET"    => "U18",
	"JUNIOR"   => "U20",	
	"SENIOR"   => "SENIOR",
	"VETERAN"  => "VETERAN",
	"ADULTE"   => "ADU",
	"Toutes les Catégories"  => "*"
);

include_once("helpers.php");
include_once("actions.php");