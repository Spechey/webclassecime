<html>
<head>
<?php include_once("lib/header.php")?>
</head>
<body>
<a href="live">Live</a>
<br/>
<a href="resultats.php" target="resulats">Resultats - contrôle</a>
<br/>
<a href="saisies_resultats.php" target="resulats">Resultats - saisies</a>
<br/>
<a href="saisies_concurents.php" target="saisies_concurents">Saisies fiches concurents</a>
<br/>
<a href="saisies_blocks.php" target="saisies_blocks">Saisies fiches blocks</a>

<br/>
<a href="print_resultats_podium.php" target="saisies_blocks">Impression Podiums</a>

<br/>
<a href="print_resultats_all.php" target="saisies_blocks">Impression Resultats</a>

<?php
//$i = exec("ipconfig");
$localIP = getHostByName(getHostName());
?>
<hr/>
Adresse reseau : <b>http://<?=$localIP?>/</b>
</body>
</html>