<?php

$url = 'http://bot.kantit.com/select_users.php';  

$result = file_get_contents($url);

$result_json = json_decode($result, true); //var_dump($result_json);

//$data = "";
foreach($result_json as $values) {
	//$data .= $values["user_stuid"] . " " . $values["user_firstname"] . " " . $values["user_lastname"] . "\r\n";
	echo $values["user_stuid"] . " " . $values["user_firstname"] . " " . $values["user_lastname"] . "<br>";	
}
//echo $data;

?>
