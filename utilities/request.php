<?php
require_once('crypto.php');

function confirmTransaction($head, $result, $data, $uri, $key)
{
	$encryptData = '{ "content": { "result": ' . json_encode($result) . ', "data": ' . json_encode($data) . ' } }';

	// print_r('<br>---------------------<br>');
	// print_r('DATA TO BE ENCRYPTED<br>');
	// print_r($encryptData);
	// print_r('<br>---------------------<br>');

	$ecrypted = encryptRimitData($encryptData, $key);
	$details = '{ "head": ' . json_encode($head) . ', "encrypted_data": ' . $ecrypted . '}';
	$response = request($uri, $details);

	if ($response) {
		$res = json_decode($response);

		if (isset($res->head->HTTP_CODE) && $res->head->HTTP_CODE === HTTP_CODE_BAD_REQUEST) {
			return $res->content;
		}
		$decrypted = isset($res->encrypted_data) ? decryptRimitData(json_encode($res->encrypted_data), $key) : false;
		return $decrypted ? json_decode($decrypted)->content : "";
	}
	else {
		return "";
	}
}

function request($url, $param)
{
	$headers = array(
		'Content-Type:application/json',
		'Content-Length: ' . strlen($param)
	);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	// Get URL content
	$lines_string = curl_exec($ch);
	$err = curl_error($ch);
	//print_r($err);
	curl_close($ch);
	return $err ? false : $lines_string;
}

?>
