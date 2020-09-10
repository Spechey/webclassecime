<?php
include_once("bdd.php");

// fichier de demo
$dbbfile="bdd/ffme.db3";

// fichier trouver sur le disque
if (is_file("C:\\Program Files\\ClassCimes\\base\\ffme.db3"))
	$dbbfile="C:\\Program Files\\ClassCimes\\base\\ffme.db3";

// fichier trouver sur le disque
if (is_file("C:\\Program Files (x86)\\ClassCimes\\base\\ffme.db3"))
	$dbbfile="C:\\Program Files (x86)\\ClassCimes\\base\\ffme.db3";

// fichier trouver sur le disque
if (is_file("D:\\RTG\\ClassCimes\\base\\ffme.db3"))
	$dbbfile="D:\\RTG\\ClassCimes\\base\\ffme.db3";


classecime::$bdd = new SQLite3($dbbfile);
$cc = new classecime();
$coureurListHeaders = array(
		"Code_coureur",
		"Dossard",
		"Nom",
		"Prenom",
		"Sexe",
		"Nation",
		"Club",
		"Categ",
	);
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
$login = "rtg";
$password = "rtg";
include_once("helpers.php");
include_once("actions.php");	