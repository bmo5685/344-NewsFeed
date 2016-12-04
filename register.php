<?php

if (!isset($_GET["username"]) || !isset($_GET["password"]))
{
	$ret = new stdClass();
	$ret->success = false;
	$ret->errorMessage = "Missing parameter(s)";
	echo json_encode($ret);
	die();
}

$users = array();

if (file_exists("users.json"))
{
	$file = fopen("users.json", "r");
	$fileContents = fread($file, filesize("users.json"));
	$users = json_decode($fileContents);
	fclose($file);
}

for ($i = 0; $i < count($users); $i++)
{
	if ($users[$i]->name == $_GET["username"])
	{
		$ret = new stdClass();
		$ret->success = false;
		$ret->errorMessage = "An account with that name is already registered";
		echo json_encode($ret);
		die();
	}
}

$newUser = new stdClass();
$newUser->name = $_GET["username"];
$newUser->password = $_GET["password"];
array_push($users, $newUser);

$file = fopen("users.json", "w");
fwrite($file, json_encode($users));
fclose($file);

$ret = new stdClass();
$ret->success = true;
echo json_encode($ret);

?>