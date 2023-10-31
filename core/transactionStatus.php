<?php
require_once('../utilities/config.php');
require_once('../utilities/commonCodes.php');
require_once('../utilities/request.php');

// TRANSACTION STATUS
function txnStatus($req)
{
    /*  */
    /* REQUEST PAYLOAD, FOR USING IN POSTMAN */
    /*
     {
        "txn_type": '',
        "txn_nature": '',
        "txn_number": '',
        "txn_urn": '',
        "txn_reference": '',
        "txn_amount": ''
     }
    */
    /*  */
    // print_r('------------------<br>');
    // print_r('REQUEST : txnStatus<br>');
    // print_r('------------------<br>');
    // print_r($req['body']);
    // print_r('------------------');

    try {

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
        $TRANSACTION_TYPE = $REQUEST_DATA->txn_type;
        $TRANSACTION_NATURE = $REQUEST_DATA->txn_nature;
        $TRANSACTION_NUMBER = $REQUEST_DATA->txn_number;
        $TRANSACTION_URN = $REQUEST_DATA->txn_urn;
        $TRANSACTION_AMOUNT = $REQUEST_DATA->txn_amount;
        $TRANSACTION_REF = $REQUEST_DATA->txn_reference;
        /*  */

        // DEBIT_CONFIRM REQUEST URL
        $TXN_STATUS_URL = BASE_URL . '/transaction/statusCheck';

        $TXN_STATUS_HEAD = array(
            "api" => 'statusCheck',
            "apiVersion" => 'V1',
            "timeStamp" => date("Y-m-d H:i:s A"),
            "auth" => array(
                "API_ID" => $AUTH_API_ID,
                "API_KEY" => $AUTH_API_KEY,
            ),
        );

        $TXN_STATUS_DATA = array(
            "txn_number" => $TRANSACTION_NUMBER,
            "txn_urn" => $TRANSACTION_URN,
            "txn_reference" => $TRANSACTION_REF,
            "txn_amount" => $TRANSACTION_AMOUNT,
            "txn_type" => $TRANSACTION_TYPE,
            "txn_nature" => $TRANSACTION_NATURE,
        );

        // TXN_STATUS_RESULT MUST BE EMPTY
        $TXN_STATUS_RESULT = array();

        $TXN_STATUS = confirmRequest($TXN_STATUS_HEAD, $TXN_STATUS_RESULT, $TXN_STATUS_DATA, $TXN_STATUS_URL, $ENCRYPTION_KEY);

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
        return $TXN_STATUS;
    } catch (Exception $e) {
        echo 'Message: ' . $e->getMessage();
    }
}


?>