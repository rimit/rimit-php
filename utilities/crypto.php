<?php

require_once('hashing.php');

function encryptRimitData($data, $password)
{
	$method = "AES-256-CBC";
	$key = $password;
	$iv = bin2hex(random_bytes(8));

	// print_r('<br>---------------------<br>');
	// print_r('<br>*** ENCRYPT - KEY *** '. $key);
	// print_r('<br>*** ENCRYPT - IV *** '. $iv);;
	// print_r('<br>*** ENCRYPT - DATA *** '. $data);;
	// print_r('<br>---------------------<br>');;

	$ciphertext = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);
	$encrypted = base64_encode($ciphertext);
	$encriptedData = array("cipher_text" => $encrypted, "iv" => $iv);

	// CREATE SALT FROM cipher_text

	$salt = $iv . $iv;
	$hash = hashData($data, $salt);
	$encriptedData = array("cipher_text" => $encrypted, "iv" => $iv, "hash" => $hash);

	// print_r('<br>*** ENCRYPTED DATA ***<br>');
	// print_r(json_encode($encriptedData));;
	// print_r('---------------------<br>');

	return json_encode($encriptedData);
}

function decryptRimitData($ivHashCiphertext, $password)
{
	$data = json_decode($ivHashCiphertext);
	$method = "AES-256-CBC";
	$key = $password;
	$ciphertext = base64_decode($data->cipher_text);
	$iv = $data->iv;

	// print_r('<br>---------------------<br>');;
	// print_r('<br>*** DECRYPT - KEY *** '. $key);
	// print_r('<br>*** DECRYPT - IV *** '. $iv);
	// print_r('<br>*** DECRYPT - DATA *** '. $data->cipher_text);
	// print_r('<br>---------------------<br>');

	$decrypted = openssl_decrypt($ciphertext, $method, $key, OPENSSL_RAW_DATA, $iv);

	// print_r('<br>*** DECRYPTED DATA ***<br>');
	// print_r($decrypted);
	// print_r('<br>---------------------<br>');

	// CHECK THE cipher_text IS CORRECT 
	$salt = $iv . $iv;
	$validateHash = hashVerify($decrypted, $data->hash, $salt);
	if (!$validateHash) {
		// print_r('Not a Valid Hash');
		// print_r($validateHash);
		return false;
	}

	return $decrypted;
}
?>