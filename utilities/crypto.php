<?php

function encryptRimitData($plaintext, $password)
{
	$method = "AES-256-CBC";
	$key = $password;
	$iv = bin2hex(random_bytes(8));

	// echo '<br>---------------------<br>';
	// echo '<br>*** ENCRYPT - KEY *** '. $key;
	// echo '<br>*** ENCRYPT - IV *** '. $iv;
	// echo '<br>*** ENCRYPT - DATA *** '. $plaintext;
	// echo '<br>---------------------<br>';

	$ciphertext = openssl_encrypt($plaintext, $method, $key, OPENSSL_RAW_DATA, $iv);
	$encrypted = base64_encode($ciphertext);
	$encriptedData = array("cipher_text" => $encrypted, "iv" => $iv);

	// echo '<br>*** ENCRYPTED DATA ***<br>';
	// echo json_encode($encriptedData);
	// echo '---------------------<br>';

	return json_encode($encriptedData);
}

function decryptRimitData($ivHashCiphertext, $password)
{
	$data = json_decode($ivHashCiphertext);
	$method = "AES-256-CBC";
	$key = $password;
	$ciphertext = base64_decode($data->cipher_text);
	$iv = $data->iv;

	// echo '<br>---------------------<br>';
	// echo '<br>*** DECRYPT - KEY *** '. $key;
	// echo '<br>*** DECRYPT - IV *** '. $iv;
	// echo '<br>*** DECRYPT - DATA *** '. $data->cipher_text;
	// echo '<br>---------------------<br>';

	$data = openssl_decrypt($ciphertext, $method, $key, OPENSSL_RAW_DATA, $iv);

	// echo '<br>*** DECRYPTED DATA ***<br>';
	// echo $data;
	// echo '<br>---------------------<br>';

	return $data;
}
?>