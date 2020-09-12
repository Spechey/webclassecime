<?php
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
$cc = new classecime();
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
	
	
$liveFilterValues = array("Categ","Sexe","Club","Dept","Ligue");
$saisieFilterValues = array("Categ","Sexe","Club","Dept","Ligue","Certificat_Medical");

$login = "rtg";
$password = "rtg";
include_once("helpers.php");
include_once("actions.php");	