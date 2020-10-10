<?php
include_once("lib/conf.php");
include_once("lib/auth.php");
if (isset($_GET["evs"]) && isset($_GET["dos"]) && $_GET["dos"] != "")
{
	$c = new Coureur($_GET["evs"]."-".$_GET["dos"]);
}
if (isset($_GET["evs"]) && isset($_GET["cc"]))
{
	$c = new Coureur($_GET["cc"]);
}
?>
<html>
<head>
<?php include_once("lib/header.php")?>
<script type="text/javascript">
function init()
{
	<?php 
	if (!isset($_GET["dos"]) || $_GET["dos"] == "")
	{
		echo '$("#dos").focus();';
	}
	else
		echo '$("#bloc").focus();';
	?>
	$("#bloc").attr("onFocus",'$("#saisie").submit();');
}
function dossard(event)
{
	if (event.keyCode == 13 ||  event.keyCode == 46) // caratére .
	{
		$("#saisie").submit();
	}

}
function setbloc(event)
{
	$("#bloc").val($("#bloc").val().replace(".",""))
	console.log(event.keyCode );
	if (event.keyCode == 13 ||  event.keyCode == 46) // caratére return
	{
		<?php if (isset($c)) { ?>
		if (Number.isInteger($("#bloc").val()))
		{
			var bloc = ($("#bloc").val()*1) + ($("#mch").val() * 1000);
			var blocId = "<?=@$c->id?>-"+bloc;
			cBck_update("cBck-"+blocId,'cBckAdd',blocId);
			$("#bloc").val("");
		}
		else
		{
			var s=$("#bloc").val().toUpperCase(), 
			    a = $("#bm-"+s);
			if (a) {
				a.click();
				if (s.endsWith('<?=$suffixModulo[1]["initial"]?>')) {
					s = s.replace('<?=$suffixModulo[1]["initial"]?>','<?=$suffixModulo[0]["initial"]?>');
					a = $("#bm-"+s);
					a.click();
				}
			}
			$("#bloc").val("");
		}
		event.keyCode = 13;
		<?php } ?>
		
	}
	if (event.keyCode == "48") // un 0
	{
		if ($("#bloc").val() == "")
		{
		  $("#bloc").val("");
		  $("#dos").val("");
		  $("#saisie").submit();
		  event.keyCode = 13;
		}
	}
	
}
</script>
</head>
<body onload="init()">
<ul class="evt"><?php helpers_listEpreuves(); ?></ul>
<div class="msg"><?= ($actionMsg)?$actionMsg:"&#160;" ?></div>
<fieldset>
<?php if (isset($_GET["evs"])) { ?>
<form id="saisie">
	<input type="hidden" name="evs" value="<?=$_GET["evs"]?>"/>
	Manche : <input  id="mch" type="int" name="mch" value="<?=isset($_GET["mch"])?$_GET["mch"]:"1"?>" size="1"/>
	-
	Dossard : <input id="dos" type="int" size="4" onkeypress="dossard(event)" name="dos" value="<?=@$c->data["Dossard"]?>">
	- 
	Ajouter le bloc n°<input id="bloc" type="int" size="4" onkeypress="setbloc(event)">
</form>
<?php } ?>
</fieldset>
<?php
// liste des epeuves pour cet evenement
if (isset($c))
{
	//var_dump($c);
	echo "<table class=coureurDetail>";
	if (!isset($coureurDetailsHeaders) || sizeof($coureurDetailsHeaders) == 0)
			$coureurDetailsHeaders = array_keys($c->data);
	echo helpers_tableLine($coureurDetailsHeaders,array(),"th");
	echo helpers_tableLine($c->data,$coureurDetailsHeaders);
	echo "<table>";
	
	$rms = $c->getResultatManches();
	

	foreach($rms as $rm)
	{
		
		echo "<h2>Manche ".$rm->data["Code_manche"]."</h2>";

		$rs = $c->getResultat($rm->data["Code_manche"]);
		$r = array();

		foreach($rs as $bckId => $cBck)
		{
			$m = ($bckId +1) % $modulo;
			$bn = floor(($bckId+1) / $modulo);
			
				@$r[3+(($m+1)*3)] .= "<td class='cBck-".$cBck->id." id ".$cBck->isValideString()."'><i>".$bn."</i>&#160;".$suffixModulo[$m]["initial"]."</td>";
				@$r[2+(($m+1)*3)] .= "<td class='cBck-".$cBck->id." pts ".$cBck->isValideString()."'>".$cBck->getPts()."pts</td>";
				@$r[1+(($m+1)*3)] .= "<td class='cBck-".$cBck->id." action ".$cBck->isValideString()."'><a id=\"bm-".$bn.$suffixModulo[$m]["initial"]."\" onclick=\"cBck_update('cBck-".$cBck->id."','".(($cBck->isValide())?"cBckDel":"cBckAdd")."','".$cBck->id."')\" href__='?".http_build_query($_GET)."&action=".(($cBck->isValide())?"cBckDel":"cBckAdd")."&actionid=".$cBck->id."'>".(($cBck->isValide())?"-":"+")."</a></td>";
				
		}			
		ksort($r);
		$r = array_reverse($r);
		echo "<table class=bck><tr>".implode("</tr><tr>",$r)."</tr></table>";

	}
	
}
// liste des epeuves pour cet evenement
else if (isset($_GET["evs"]))
{
	$evs = new Evenement($_GET["evs"]);
	echo "<table  class=coureurList>";
	$cs = $evs->getConcurents();
	if (!isset($coureurListHeaders) || sizeof($coureurListHeaders) == 0)
			$coureurDetailsHeaders = array_keys($cs[0]->data);
	echo helpers_tableLine($coureurListHeaders,array(),"th");
	foreach($cs as $c)
	{
		$c->data["Code_coureur"] = '<a href="?evs='.$_GET["evs"].'&cc='.$c->id.'">'.$c->data["Code_coureur"].'</a>';
		echo helpers_tableLine($c->data,$coureurListHeaders);
	}
	echo "</table>";
	
 
}
?>
</body>
</html>