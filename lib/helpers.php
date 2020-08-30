<?php
function helpers_listEpreuves()
{
	foreach($GLOBALS["cc"]->getEvenements() as $evs)
	{
		//var_dump($evs);
		$class = (isset($GLOBALS["_GET"]["evs"]) && $GLOBALS["_GET"]["evs"] == $evs->id)?"active":"inactive";
		
		echo "<li class='".$class."'><a href='?evs=".$evs->id."'>".$evs->data["Nom"]."</a></li>";
	}
}
function helpers_tableLine($datas,$cols=array(),$balise="td")
{
	$r ="<tr>";
	if (sizeof($cols) == 0)
		$cols = array_keys($datas);
	for ($i=0;$i<sizeof($cols);$i++)
	{
		$r .= "<".$balise.">".$datas[$cols[$i]]."</".$balise.">";
	}
	$r .="</tr>";
	return $r;
}