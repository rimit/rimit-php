<?php
require_once('../utilities/config.php');
require_once('../utilities/commonCodes.php');
require_once('../utilities/response.php');
require_once('../utilities/crypto.php');

// TRANSACTION STATUS
function txnStatus($req)
{
    // print_r('------------------<br>');
    // print_r('REQUEST : txnStatus<br>');
    // print_r('------------------<br>');

    try {
        $date = new DateTime("now");
        $timeStamp = $date->format('Y-m-d\TH:i:s\Z');
        $head = array(
            "api" => "txnStatus",
            "apiVersion" => "V1",
            "timeStamp" => $timeStamp
        );

        /*  */
        /* ASSIGN ENCRYPTION_KEY, API_KEY & API_ID OF ENTITY */
        $ENCRYPTION_KEY = '';
        $AUTH_API_ID = '';
        $AUTH_API_KEY = '';
        /*  */

        /*  */
        /* ASSIGNING DATA RECIVED IN THE REQUEST  */
        $REQUEST_DATA = json_decode($req['body']);
        /*  */

        /*  */
        /* ASSIGNING DATA RECIVED IN THE REQUEST  */
        $TRANSACTION_TYPE = $REQUEST_DATA->type;
        $TRANSACTION_ID = $REQUEST_DATA->id;
        $TRANSACTION_AMOUNT = $REQUEST_DATA->amount;
        $TRANSACTION_REF = $REQUEST_DATA->reference;
        /*  */

        // DEBIT_CONFIRM REQUEST URL
        $TXN_STATUS_URL = BASE_URL . '/transaction/status';

        $TXN_STATUS_HEAD = array(
            "api" => 'status',
            "apiVersion" => 'V1',
            "timeStamp" => $timeStamp,
            "auth" => array(
                "API_ID" => $AUTH_API_ID,
                "API_KEY" => $AUTH_API_KEY,
            ),
        );

        $TXN_STATUS_DATA = array(
            "txn_id" => $TRANSACTION_ID,
            "txn_reference" => $TRANSACTION_REF,
            "txn_amount" => $TRANSACTION_AMOUNT,
            "txn_type" => $TRANSACTION_TYPE,
        );

        // TXN_STATUS_RESULT MUST BE EMPTY
        $TXN_STATUS_RESULT = array();

        $TXN_STATUS = confirmTransaction($TXN_STATUS_HEAD, $TXN_STATUS_RESULT, $TXN_STATUS_DATA, $TXN_STATUS_URL, $ENCRYPTION_KEY);

        if (!$TXN_STATUS) {
            // print_r('<br>TXN_STATUS - REQUEST STATUS<br>');
            // print_r($TXN_STATUS);
            return;
        }

        // print_r('<br>*****************<br>');
        // print_r('TXN_STATUS - RESPONSE<br>');
        // print_r(json_encode($TXN_STATUS));
        // print_r('<br>*****************<br>');

        /*  */
        /*  */

        /* MANAGE RECEIVED RESPONSE */
        /*  */

        /*  */
        /*  */
        return;
    }
    catch (Exception $e) {
        $result = array(
            'code' => RESULT_CODE_SERVICE_NOT_AVAILABLE,
            'status' => STATUS_ERROR,
            'message' => RESULT_MESSAGE_E2003,
        );
        $data = array();
        $head['HTTP_CODE'] = HTTP_CODE_SERVICE_UNAVAILABLE;
        return response_error($result, $head, $data);
    }
}


?>