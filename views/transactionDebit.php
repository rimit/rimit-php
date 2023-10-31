<?php
require_once('../core/transactionDebit.php');

$request['body'] = file_get_contents('php://input'); //Json Request
$request['tenant_id'] = isset($_REQUEST["tenant_id"]) ? $_REQUEST["tenant_id"] : "";

if ($request['body']) {
    $response = debitAmount($request);

    /**************Decrypt the response ***********/
    // $ENCRYPTION_KEY = "";
    // $REQUEST_DATA = json_decode($response); 
    // $DECRYPTED = decryptRimitData( json_encode($REQUEST_DATA->encrypted_data), $ENCRYPTION_KEY );
    // print_r($DECRYPTED);
} else {
    print_r('*************NO REQUEST PARAMETERS FOUND************');
}
?>