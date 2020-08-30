<?php
include_once("bdd.php");

$dbbfile="D:\\RTG\\ClassCimes\\base\\ffme.db3";
if (!is_file($dbbfile))
	$dbbfile="bdd/ffme.db3";

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
include_once("helpers.php");
include_once("actions.php");	