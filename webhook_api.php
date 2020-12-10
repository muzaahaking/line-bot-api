<?php

$channelAccessToken = 'PZ6qlbYABvcIg+sly4KFcjs8rAVOW1+EEEDBgcOn86a9MwA+MNHV8//FPERaqcVuWnKEs4U+6oe0jLA++fQlGKdK9/SCRKlZ0x4otRbscQZBRbe5VDkXvu32iZAA+dpXEwrb47Ncr9kuH1vSp+t3LwdB04t89/1O/w1cDnyilFU='; // Access Token ค่าที่เราสร้างขึ้น

$request = file_get_contents('php://input');   // Get request content

$request_json = json_decode($request, true);   // Decode JSON request

foreach ($request_json['events'] as $event)
{
	if ($event['type'] == 'message') 
	{
		if($event['message']['type'] == 'text')
		{
			$text = $event['message']['text'];
			
			if($text == "@บอท"){
				
				$reply_message = "กรุณาใช้รูปแบบคำสั่งที่ถูกต้องงงงง!!\n";
				
				$reply_message .= "ฉันมีบริการให้คุณสั่งได้ ดังนี้...\n";
				
				$reply_message .= "พิมพ์ว่า \"@บอท ขอรายชื่อนิสิตทั้งหมด\"\n";
				$reply_message .= "พิมพ์ว่า \"@บอท ขอรายชื่อนิสิต รหัส 61160xxx\"\n";
				$reply_message .= "พิมพ์ว่า \"@บอท ขอรหัส FTP ของ s61160xxx\"\r\n";
			}else if($text == "@บอทขอรายชื่อนิสิตทั้งหมด"){
					$reply_message = mySQL_selectAll('http://bot.kantit.com/json_select_users.php');
				}else if($text == "ขอรายชื่อนิสิต รหัส"){
					$reply_message = mySQL_selectAll('http://bot.kantit.com/json_select_users.php?sid='.$txts[3]);
				}else if($text == "ขอรหัส FTP ของ"){
					$reply_message = mySQL_selectFTP('http://bot.kantit.com/json_select_ftp.php?sid='.$txts[4]);					
				}	
			else{ 
			
			}
			
		} else {
			$reply_message = 'ฉันได้รับ "'.$event['message']['type'].'" ของคุณแล้ว!';
		}
		
	} else {
		$reply_message = 'ฉันได้รับ Event "' . $event['type'] . '" ของคุณแล้ว!';
	}
	
	if($reply_message == null || $reply_message == ""){ $reply_message =  'ขออภัยฉันไม่สามารถตอบกลับข้อความ "'. $text . '" ของคุณ!'; }
		
	// reply message
	$post_header = array('Content-Type: application/json', 'Authorization: Bearer ' . $channelAccessToken);	
	
	$data = ['replyToken' => $event['replyToken'], 'messages' => [['type' => 'text', 'text' => $reply_message]]];	
	
	$post_body = json_encode($data);	
	
	// reply method type-1 vs type-2
	$send_result = reply_message_1('https://api.line.me/v2/bot/message/reply', $post_header, $post_body);
	//$send_result = reply_message_2('https://api.line.me/v2/bot/message/reply', $post_header, $post_body);	
}

function mySQL_selectAll($url)
{
	$result = file_get_contents($url);
	
	$result_json = json_decode($result, true); //var_dump($result_json);
	
	$data = "ผลลัพธ์:\r\n";
		
	foreach($result_json as $values) {
		$data .= $values["user_stuid"] . " " . $values["user_firstname"] . " " . $values["user_lastname"] . "\r\n";
	}
	
	return $data;
}

function mySQL_selectFTP($url)
{
	$result = file_get_contents($url);
	
	$result_json = json_decode($result, true); //var_dump($result_json);
	
	$data = "ผลลัพธ์:\r\n";
		
	foreach($result_json as $values) {
		$data .= $values["user_password"] . "\r\n";
	}
	
	return "รหัส FTP ของคุณคือ ".$data;
}


function reply_message_1($url, $post_header, $post_body)
{
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => $post_header,
                'content' => $post_body,
            ],
        ]);
	
	$result = file_get_contents($url, false, $context);
	
	return $result;
}

function reply_message_2($url, $post_header, $post_body)
{
	$ch = curl_init($url);	
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $post_header);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	
	$result = curl_exec($ch);
	
	curl_close($ch);
	
	return $result;
}

?>
