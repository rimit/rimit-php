<?php
require_once('crypto.php');

function response_success($head, $result, $data, $key)
{
	$encryptData = '{ "content": { "result": ' . json_encode($result) . ', "data": ' . json_encode($data) . ' } }';
	$encrypted = encryptRimitData($encryptData, $key);
	return $data = '{ "head":' . json_encode($head) . ', "encrypted_data":' . $encrypted . '}';
}

function response_error($result, $head, $data)
{
	$details = '{ "result": ' . json_encode($result) . ', "data": ' . json_encode($data) . ' }';
	return $data = '{ "head":' . json_encode($head) . ', "content":' . $details . '}';
}
?>