<?php
require_once('../utilities/config.php');
require_once('../utilities/commonCodes.php');
require_once('../utilities/response.php');
require_once('../utilities/crypto.php');

// CHECK ACCOUNT BALANCE
function accountStatement($req)
{
    // print_r('------------------<br>');
    // print_r('REQUEST : accountStatement<br>');
    // print_r('------------------<br>');
    // print_r($req['body']);
    // print_r('------------------');

    $head = array(
        'api' => 'accountStatement',
        'apiVersion' => 'V1',
        'timeStamp' => date("Y-m-d H:i:s A")
    );

    try {
        // ASSIGNING TENANT_ID IF THE PLATFORM IS MULTY TENANT
        $TENANT_ID = '';
        if (IS_MULTY_TENANT_PLATFORM === 'YES') {
            $TENANT_ID = $req['tenant_id'];
        }

        $TRANSACTION_DATA = array();

        /*  */
        /* ASSIGN ENCRYPTION_KEY OF ENTITY */
        $ENCRYPTION_KEY = '';
        /*  */

        // ASSIGNING DATA RECIVED IN THE REQUEST
        $REQUEST_DATA = json_decode($req['body']);

        // DECRYPTING DATA RECEIVED
        $DECRYPTED = decryptRimitData(
            json_encode($REQUEST_DATA->encrypted_data),
            $ENCRYPTION_KEY
        );


        // ERROR RESPONSE IF DECRYPTION FAILED
        if (!$DECRYPTED) {
            $result = array(
                'code' => RESULT_CODE_DECRYPTION_FAILED,
                'status' => STATUS_ERROR,
                'message' => RESULT_MESSAGE_E2008,
            );
            $data = array();
            $head['HTTP_CODE'] = HTTP_CODE_BAD_REQUEST;
            return response_error($result, json_encode($head), $data);
        }

        $DECRYPTED_DATA = json_decode($DECRYPTED, JSON_PRETTY_PRINT);

        $USER_MOBILE = $DECRYPTED_DATA["content"]["data"]["mobile"];
        $USER_CC = $DECRYPTED_DATA["content"]["data"]["country_code"];
        $ACC_NO = $DECRYPTED_DATA["content"]["data"]["account_number"];
        $ACC_BRANCH = $DECRYPTED_DATA["content"]["data"]["branch_code"];
        $START_DATE = $DECRYPTED_DATA["content"]["data"]["start_date"];
        $END_DATE = $DECRYPTED_DATA["content"]["data"]["end_date"];

        /*  */
        /*  */
        /* VERIFY THE USER */
        /* MANAGE SCOPE FOR ERRORS (Refer - https://doc.rimit.co/account/account-statement#response-code) */
        /*  */
        /*  */

        /*  */
        /* EG FOR FAILED RESPONSE : FIND USER ACCOUNT, IF NOT FOUND, SEND RESPONSE AS FAILED */
        $FIND_ACCOUNT = true;
        if (!$FIND_ACCOUNT) {
            $result = array(
                "code" => RESULT_CODE_INVALID_ACCOUNT,
                "status" => STATUS_FAILED,
                "message" => RESULT_MESSAGE_E2021,
            );
            $data = array();
            $head['HTTP_CODE'] = HTTP_CODE_SUCCESS;
            return response_success(json_encode($head), $result, $data, $ENCRYPTION_KEY);
        }
        /*  */

        /*  */
        /* FIND THE ACCOUNT BALANCE AND ASSIGN. KEEP 0 IF NO BALANCE FOUND*/
        $ACC_BALANCE = '';
        /*  */

        /*  */
        /* FIND ALL TRANSACTIONS BETWEEN START_DATE & END_DATE IN THE RESPECTIVE ACCOUNT */
        $ACCOUNT_TRANSACTION = array(
            array(
                "txn_id" => '',
                "date" => '',
                "time" => '',
                "debit_amount" => '',
                "credit_amount" => '',
                "balance" => '',
                "description" => ''
            ),
            array
            (
                "txn_id" => '',
                "date" => '',
                "time" => '',
                "debit_amount" => '',
                "credit_amount" => '',
                "balance" => '',
                "description" => ''
            ),
        );
        /*  */

        /*  */
        /* ASSIGN DATA RECEIVED FROM ACCOUNT_TRANSACTION ARRAY */
        if (count($ACCOUNT_TRANSACTION) > 0) {
            foreach ($ACCOUNT_TRANSACTION as $account) {
                $details = array(
                    "txn_id" => $account["txn_id"],
                    "date" => $account["date"],
                    "time" => $account["time"],
                    "debit_amount" => $account["debit_amount"],
                    "credit_amount" => $account["credit_amount"],
                    "balance" => $account["balance"],
                    "description" => $account["description"],
                );
                $TRANSACTION_DATA[] = $details;
            }
        }
        /*  */

        $USER_ACCOUNT_DATA = array(
            "account_number" => $ACC_NO,
            "branch_code" => $ACC_BRANCH,
            "balance_amount" => $ACC_BALANCE,
            "start_date" => $START_DATE,
            "end_date" => $END_DATE,
            "transactions_count" => count($ACCOUNT_TRANSACTION),
        );

        $result = array(
            "code" => RESULT_CODE_SUCCESS,
            "status" => STATUS_SUCCESS,
            "message" => RESULT_MESSAGE_E1001,
        );
        $data = array(
            "account" => $USER_ACCOUNT_DATA,
            "transactions" => $TRANSACTION_DATA,
        );
        $head["HTTP_CODE"] = HTTP_CODE_SUCCESS;
        return response_success($head, $result, $data, $ENCRYPTION_KEY);
    } catch (Exception $e) {
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