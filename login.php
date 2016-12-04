<?php
session_start();

if (!isset($_GET["username"]) || !isset($_GET["password"]))
{
	$ret = new stdClass();
	$ret->success = FALSE;
	$ret->errorMessage = "Missing parameter(s)";
	echo json_encode($ret);
	die();
}

$users = array();

if (!file_exists("users.json"))
{
	$ret = new stdClass();
	$ret->success = false;
	$ret->errorMessage = "User not registered";
	echo json_encode($ret);
	die();
}

$file = fopen("users.json", "r");
$fileContents = fread($file, filesize("users.json"));
$users = json_decode($fileContents);
fclose($file);

$registered = false;
$loginSuccessful = false;
for ($i = 0; $i < count($users); $i++)
{
	if ($users[$i]->name == $_GET["username"])
	{
		$registered = true;
		if ($users[$i]->password == $_GET["password"])
		{
			$loginSuccessful = true;
		}
		break;
	}
}

if ($loginSuccessful)
{
	$_SESSION["user"] = $_GET["username"];
	
	$ret = new stdClass();
	$ret->success = true;
	echo json_encode($ret);
	die();
}
else if ($registered)
{
	$ret = new stdClass();
	$ret->success = false;
	$ret->errorMessage = "Incorrect password";
	echo json_encode($ret);
	die();
}
else
{
	$ret = new stdClass();
	$ret->success = false;
	$ret->errorMessage = "User not registered";
	echo json_encode($ret);
	die();
}

?>