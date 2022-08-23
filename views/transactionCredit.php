<?php
require_once('../core/transactionCredit.php');

$request['body'] = file_get_contents('php://input'); //Json Request
$request['tenant_id'] = isset($_REQUEST["tenant_id"]) ? $_REQUEST["tenant_id"] : ""; 

if ($request['body']) {
	$response = creditAmount($request);
}
else {
	print_r('/*************NO REQUEST PARAMETERS FOUND************/');
}
?>