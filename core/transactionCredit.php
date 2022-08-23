<?php
require_once('../utilities/config.php');
require_once('../utilities/commonCodes.php');
require_once('../utilities/response.php');
require_once('../utilities/crypto.php');

// CREDIT TRANSACTION
function creditAmount($req)
{
    // print_r('------------------<br>');
    // print_r('REQUEST : creditAmount<br>');
    // print_r('------------------<br>');

    $date = new DateTime("now");
    $timeStamp = $date->format('Y-m-d\TH:i:s\Z');
    $head = array(
        "api" => "creditAmount",
        "apiVersion" => "V1",
        "timeStamp" => $timeStamp
    );

    try {
        // ASSIGNING TENANT_ID IF THE PLATFORM IS MULTY TENANT
        $TENANT_ID = '';
        if (IS_MULTY_TENANT_PLATFORM === 'YES') {
            $TENANT_ID = $req['tenant_id'];
        }

        /*  */
        /* ASSIGN ENCRYPTION_KEY, API_KEY & API_ID OF ENTITY */
        $ENCRYPTION_KEY = '';
        $AUTH_API_ID = '';
        $AUTH_API_KEY = '';
        /*  */

        // CREDIT_CONFIRM REQUEST URL
        $CREDIT_CONFIRM_URL = BASE_URL . '/transaction/confirmCredit';

        $CREDIT_CONFIRM_HEAD = array(
            "api" => "confirmCredit",
            "apiVersion" => "V1",
            "timeStamp" => $timeStamp,
            "auth" => array
                (
                "API_ID" => $AUTH_API_ID,
                "API_KEY" => $AUTH_API_KEY
            )
        );

        // ASSIGNING DATA RECIVED IN THE REQUEST
        $REQUEST_DATA = json_decode($req['body']);

        // DECRYPTING DATA RECEIVED
        $DECRYPTED = decryptRimitData(
            json_encode($REQUEST_DATA->encrypted_data),
            $ENCRYPTION_KEY
        );

        // ERROR RESPONSE IF DECRYPTION FAILED
        if (!$DECRYPTED) {
            $result = array
                (
                "code" => RESULT_CODE_DECRYPTION_FAILED,
                "status" => STATUS_ERROR,
                "message" => RESULT_MESSAGE_E2008,
            );
            $data = array();
            $head['HTTP_CODE'] = HTTP_CODE_BAD_REQUEST;
            return response_error($result, $head, $data);
        }

        $DECRYPTED_DATA = json_decode($DECRYPTED, JSON_PRETTY_PRINT);

        $USER = $DECRYPTED_DATA["content"]["data"]["beneficiary"];
        $TRANSACTION = $DECRYPTED_DATA["content"]["data"]["transaction"];
        $REFUND_REFERENCE = $DECRYPTED_DATA["content"]["data"]["refund_reference"];

        $USER_MOBILE = $USER["mobile"];
        $USER_COUNTRY_CODE = $USER["country_code"];
        $USER_ACCOUNT_NUMBER = $USER["account_number"];
        $USER_BRANCH_CODE = $USER["branch_code"];

        $TRANSACTION_TYPE = $TRANSACTION["type"];
        $TRANSACTION_ID = $TRANSACTION["txn_id"];
        $TRANSACTION_AMOUNT = $TRANSACTION["amount"];
        $TRANSACTION_DATE = $TRANSACTION["date"];
        $TRANSACTION_TIME = $TRANSACTION["time"];

        $REFUND_ID = "";
        $REFUND_AMOUNT = "";
        $REFUND_DATE = "";
        $REFUND_TIME = "";

        if ($TRANSACTION_TYPE === 'REFUND_CREDIT') {
            $REFUND_ID = $REFUND_REFERENCE["txn_id"];
            $REFUND_AMOUNT = $REFUND_REFERENCE["amount"];
            $REFUND_DATE = $REFUND_REFERENCE["date"];
            $REFUND_TIME = $REFUND_REFERENCE["time"];
        }

        /*  */
        /*  */
        /* VERIFY THE USER */
        /* MANAGE SCOPE FOR FAILED TRANSACTIONS (Refer - https://doc.rimit.co/transaction-credit/confirm-credit#result-code) */
        /* VERIFY THE USER ACCOUNT */
        /* VERIFY THE USER ACCOUNT BALANCE AVAILABILITY */
        /* CREDIT USER ACCOUNT WITH txn_amount */
        /*  */
        /*  */

        /*  */
        /* ASSIGN A UNIQUE TRANSACTION_REF */
        $TRANSACTION_REF = '';
        /*  */

        /*  */
        /* ASSIGN LATEST ACCOUNT_BALANCE AFTER CREDITING THE TRANSACTION_AMOUNT */
        $ACCOUNT_BALANCE = '';
        /*  */

        $CREDIT_CONFIRM_DATA = array(
            "txn_id" => $TRANSACTION_ID,
            "txn_reference" => $TRANSACTION_REF,
            "txn_amount" => $TRANSACTION_AMOUNT,
            "txn_type" => $TRANSACTION_TYPE,
            "account_balance" => $ACCOUNT_BALANCE,
        );

        /*  */
        /* EG FOR FAILED REQUEST : FIND LATEST ACCOUNT BALANCE, IF FOUND INSUFFICIENT, SEND REQUEST AS FAILED */
        $CHECK_ACCOUNT_STATUS = true;
        if (!$CHECK_ACCOUNT_STATUS) {
            $CREDIT_CONFIRM_RESULT = array
                (
                "code" => RESULT_CODE_ACCOUNT_IS_INACTIVE_BLOCKED_CLOSED,
                "status" => STATUS_FAILED,
                "message" => RESULT_MESSAGE_E8897,
            );

            $CREDIT_CONFIRM = confirmTransaction($CREDIT_CONFIRM_HEAD, $CREDIT_CONFIRM_RESULT, $CREDIT_CONFIRM_DATA, $CREDIT_CONFIRM_URL, $ENCRYPTION_KEY);

            if (!$CREDIT_CONFIRM) {
            // print_r('CREDIT_CONFIRM - CHECK_ACCOUNT_STATUS - REQUEST STATUS');
            // print_r($CREDIT_CONFIRM);
            }

            // print_r('CREDIT_CONFIRM - CHECK_ACCOUNT_STATUS - RESPONSE');
            // print_r($CREDIT_CONFIRM);
            return;
        }
        /*  */

        // IF THE CREDIT AMOUNT IS SUCCESSFUL
        $CREDIT_CONFIRM_RESULT = array(
            "code" => RESULT_CODE_SUCCESS,
            "status" => STATUS_SUCCESS,
            "message" => RESULT_MESSAGE_E1001,
        );

        $CREDIT_CONFIRM = confirmTransaction($CREDIT_CONFIRM_HEAD, $CREDIT_CONFIRM_RESULT, $CREDIT_CONFIRM_DATA, $CREDIT_CONFIRM_URL, $ENCRYPTION_KEY);

        if (!$CREDIT_CONFIRM) {
            // print_r('<br>CREDIT_CONFIRM - REQUEST STATUS<br>');
            // print_r($CREDIT_CONFIRM);
            return;
        }

        // print_r('<br>*****************<br>');
        // print_r('CREDIT_CONFIRM - RESPONSE<br>');
        // print_r(json_encode($CREDIT_CONFIRM));
        // print_r('*****************<br>');

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
            "code" => RESULT_CODE_SERVICE_NOT_AVAILABLE,
            "status" => STATUS_ERROR,
            "message" => RESULT_MESSAGE_E2003,
        );
        $data = array();
        $head["HTTP_CODE"] = HTTP_CODE_SERVICE_UNAVAILABLE;
        return response_error($result, $head, $data);
    }
}
?>