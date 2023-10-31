<?php
require_once('../utilities/config.php');
require_once('../utilities/commonCodes.php');
require_once('../utilities/response.php');
require_once('../utilities/request.php');
require_once('../utilities/crypto.php');

// CREDIT TRANSACTION
function creditAmount($req)
{
    // print_r('<br>------------------<br>');
    // print_r('<br>REQUEST : creditAmount<br>');
    // print_r('<br>------------------<br>');
    // print_r($req['body']);
    // print_r('------------------');

    $head = array(
        "api" => "creditAmount",
        "apiVersion" => "V1",
        "timeStamp" => date("Y-m-d H:i:s A")
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
            "timeStamp" => date("Y-m-d H:i:s A"),
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

        $USER = $DECRYPTED_DATA["content"]["data"]["user"];
        $TRANSACTION = $DECRYPTED_DATA["content"]["data"]["transaction"];
        $SETTLEMENT = $DECRYPTED_DATA["content"]["data"]["settlement"];

        $USER_MOBILE = $USER["mobile"];
        $USER_COUNTRY_CODE = $USER["country_code"];
        $USER_ACCOUNT_NUMBER = $USER["account_number"];
        $USER_ACCOUNT_CLASS = $USER["account_class"];
        $USER_ACCOUNT_TYPE = $USER["account_type"];
        $USER_BRANCH_CODE = $USER["branch_code"];

        $TRANSACTION_NO = $TRANSACTION["txn_number"];
        $TRANSACTION_URN = $TRANSACTION["txn_urn"];
        $TRANSACTION_TYPE = $TRANSACTION["txn_type"];
        $TRANSACTION_NATURE = $TRANSACTION["txn_nature"];
        $TRANSACTION_NOTE = $TRANSACTION["txn_note"];
        $TRANSACTION_DATE = $TRANSACTION["txn_date"];
        $TRANSACTION_TIME = $TRANSACTION["txn_time"];
        $TRANSACTION_TIMESTAMP = $TRANSACTION["txn_ts"];
        $TRANSACTION_AMOUNT = $TRANSACTION["txn_amount"];
        $TRANSACTION_SERVICE_CHARGE = $TRANSACTION["txn_service_charge"];
        $TRANSACTION_SP_FEE = $TRANSACTION["txn_sp_fee"];
        $TRANSACTION_FEE = $TRANSACTION["txn_fee"];

        $TRANSACTION_SERVICE_CHARGE = $TRANSACTION["txn_service_charge"];
        $TRANSACTION_SERVICE_PROVIDER_CHARGE = $TRANSACTION["txn_sp_charge"];
        $TRANSACTION_FEE = $TRANSACTION["txn_fee"];

        $SETTLEMENT_ACCOUNT_TYPE = $SETTLEMENT["account_type"];
        $SETTLEMENT_ACCOUNT_NUMBER = $SETTLEMENT["account_number"];

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
            "txn_urn" => $TRANSACTION_URN,
            "txn_number" => $TRANSACTION_NO,
            "txn_reference" => $TRANSACTION_REF,
            "txn_amount" => $TRANSACTION_AMOUNT,
            "txn_type" => $TRANSACTION_TYPE,
            "txn_nature" => $TRANSACTION_NATURE,
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

            $CREDIT_CONFIRM = confirmRequest($CREDIT_CONFIRM_HEAD, $CREDIT_CONFIRM_RESULT, $CREDIT_CONFIRM_DATA, $CREDIT_CONFIRM_URL, $ENCRYPTION_KEY);

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

        $CREDIT_CONFIRM = confirmRequest($CREDIT_CONFIRM_HEAD, $CREDIT_CONFIRM_RESULT, $CREDIT_CONFIRM_DATA, $CREDIT_CONFIRM_URL, $ENCRYPTION_KEY);

        // print_r('<br>*****************<br>');
        // print_r('<br>CREDIT_CONFIRM - RESPONSE<br>');
        // print_r(json_encode($CREDIT_CONFIRM));
        // print_r('<br>*****************<br>');

        /*  */
        /*  */

        /* MANAGE RECEIVED RESPONSE */
        /*  */

        /*  */
        /*  */
        return;
    } catch (Exception $e) {
        echo 'Message: ' . $e->getMessage();
    }
}
?>
