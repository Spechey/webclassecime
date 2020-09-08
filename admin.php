<html>
<head>
<?php include_once("lib/header.php")?>
</head>
<body>
<a href="live">Live</a>
<br/>
<a href="resultats.php" target="resulats">Resultats</a>
<br/>
<a href="saisies_concurents.php" target="saisies_concurents">Saisies fiches concurents</a>
<br/>
<a href="saisies_blocks.php" target="saisies_blocks">Saisies fiches blocks</a>
<pre>
<?php
@print_r(system("ipconfig"));
?>
</body>