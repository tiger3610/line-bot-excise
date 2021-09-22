<?php

$channelAccessToken = 'MbUn0x5Y/r/RpHoWkvoNgQevNDHO1zrlEbdB7bFQfYE2rb2HkD63kaVsPvSd2K8frpFLExJrJOGePFA/2TXq/wcy2wnpFvLSU1OQWKMbQq4IfFkJzshCfzs3xXPyvKUGaUoHWisF0hG4cYjNELknMAdB04t89/1O/w1cDnyilFU='; // Access Token ค่าที่เราสร้างขึ้น

$request = file_get_contents('php://input');   // Get request content

$request_json = json_decode($request, true);   // Decode JSON request

foreach ($request_json['events'] as $event)
{
	if ($event['type'] == 'message') 
	{
		if($event['message']['type'] == 'text')
		{
			$text = $event['message']['text'];
			
			if(($text == "รถแบบผมเข้าพิกัดเสียภาษีหรือไหมครับ?") || ($text == "รถแบบนี้เข้าพิกัดเสียภาษีหรือไหมครับ?") ){
				$number = rand(0,1);
				if($number == 0){
					$reply_message = 'อยู่พิกัดการเสียภาษี (ผลิต/ดัดแปลงสภาพรถยนต์หรือยานพาหนะ)'; 
				}else{
					$reply_message = 'ไม่อยู่พิกัดการเสียภาษี!!!'; 
				}
				
			}else{
				$reply_message = 'ฉันได้รับข้อความ "'. $text.'" ของคุณแล้ว!';
			}			
		} else {
			$reply_message = 'ฉันได้รับ "'.$event['message']['type'].'" ของคุณแล้ว!';
		}
		
	} else {
		$reply_message = 'ฉันได้รับ Event "'.$event['type'].'" ของคุณแล้ว!';
	}
	
	// reply message
	$post_header = array('Content-Type: application/json', 'Authorization: Bearer ' . $channelAccessToken);
	
	$data = ['replyToken' => $event['replyToken'], 'messages' => [['type' => 'text', 'text' => $reply_message]]];
	
	$post_body = json_encode($data);
	
	// reply method type-1 vs type-2
	$send_result = reply_message_1('https://api.line.me/v2/bot/message/reply', $post_header, $post_body); 
	//$send_result = reply_message_2('https://api.line.me/v2/bot/message/reply', $post_header, $post_body);
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

echo "220 OK!!";

?>
