<?php
include_once("lib/conf.php");
$r = array("action"=>$action,
			"actionOk"=>$actionOk,
			"msg"=>$actionMsg,
			"data"=>$actionData);
header("content-type: application/json");
echo json_encode($r);
