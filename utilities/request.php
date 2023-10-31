<?php
require_once('crypto.php');

function confirmRequest($head, $result, $data, $uri, $key)
{
	$encryptData = '{ "content": { "result": ' . json_encode($result) . ', "data": ' . json_encode($data) . ' } }';

	// print_r('<br>---------------------<br>');
	// print_r('<br>DATA TO BE ENCRYPTED<br>');
	// print_r($encryptData);
	// print_r('<br>---------------------<br>');

	$ecrypted = encryptRimitData($encryptData, $key);
	$details = '{ "head": ' . json_encode($head) . ', "encrypted_data": ' . $ecrypted . '}';
	$response = request($uri, $details);

	if ($response) {
		$res = json_decode($response);
		$httpCode = isset($res->head->HTTP_CODE) ? isset($res->head->HTTP_CODE) : 0;
		if ($httpCode === HTTP_CODE_BAD_REQUEST || $httpCode === HTTP_CODE_UNAUTHORIZED || $httpCode === HTTP_CODE_SERVICE_UNAVAILABLE) {

			// print_r('<br>---------------------<br>');
			// print_r('<br>DECRYPTED FAILED<br>');
			// print_r($res);
			// print_r('<br>---------------------<br>');

			return $res;
		}

		// print_r('<br>---------------------<br>');
		// print_r('<br>DATA TO BE DECRYPTED<br>');
		// print_r($res);
		// print_r('<br>---------------------<br>');

		$decrypted = isset($res->encrypted_data) ? decryptRimitData(json_encode($res->encrypted_data), $key) : false;

		$responseData = array("head" => $res->head, "content" => json_decode($decrypted)->content);

		return $responseData;

	} else {
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

	curl_close($ch);
	return $err ? false : $lines_string;
}

?>