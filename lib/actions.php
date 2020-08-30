<?php
$msg = false;
$action = false;
$actionOk = false;
$actionMsg = "";
$actionData = array();
if (isset($_GET["action"]))
{
	$action = true;
	switch($_GET["action"])
	{
		case "cBckDel":
			if (isset($_GET["actionid"]))
			{
				$cBck = new CoureurBlock($_GET["actionid"]);
				$cBck->setValide(false);
				$cBck->reload();
				if (!$cBck->isValide())
				{
					$actionOk = true;
					$actionMsg = "Suppression block ok";
					$actionData = array("id"      =>  $cBck->id,
										"isValide"=>  $cBck->isValide(),
										);
				}
				else
					$actionMsg = "Erreur suppression block ok";
			}
			else
			{
				$actionMsg = "Erreur actionid innexistant";
			}
		break;
		case "cBckAdd":
			if (isset($_GET["actionid"]))
			{
				$cBck = new CoureurBlock($_GET["actionid"]);
				$cBck->setValide(true);
				$cBck->reload();
				if ($cBck->isValide())
				{
					$actionOk = true;
					$actionMsg = "Ajout block ok";
					$actionData = array("id"      =>  $cBck->id,
										"isValide"=>  $cBck->isValide(),
										);
					
				}
				else
					$actionMsg = "Erreur ajout block ok";
			}
			else
			{
				$actionMsg = "Erreur actionid innexistant";
			}
		break;
		default:
			$actionMsg = "Action inconnu";
	}
	
	
	
	
	unset($_GET["action"]);
	if (isset($_GET["actionid"]))
		unset($_GET["actionid"]);
	
}