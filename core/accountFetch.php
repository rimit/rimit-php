<?php
require_once('../utilities/config.php');
require_once('../utilities/commonCodes.php');
require_once('../utilities/response.php');
require_once('../utilities/crypto.php');

// FETCH ACCOUNT
function accountFetch($req)
{
    // print_r('------------------<br>');
    // print_r('REQUEST : accountFetch<br>');
    // print_r('------------------<br>');

    $date = new DateTime("now");
    $timeStamp = $date->format('Y-m-d\TH:i:s\Z');
    $head = array(
        'api' => 'accountFetch',
        'apiVersion' => 'V1',
        'timeStamp' => $timeStamp
    );
    try {
        // ASSIGNING TENANT_ID IF THE PLATFORM IS MULTY TENANT
        $TENANT_ID = '';
        if (IS_MULTY_TENANT_PLATFORM === 'YES') {
            $TENANT_ID = $req['tenant_id'];
        }

        $USER_ACCOUNTS = array();

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
            return response_error($result, $head, $data);
        }

        $DECRYPTED_DATA = json_decode($DECRYPTED, JSON_PRETTY_PRINT);

        $USER_MOBILE = $DECRYPTED_DATA["content"]["data"]["mobile"];
        $USER_CC = $DECRYPTED_DATA["content"]["data"]["country_code"];
        $DOB = $DECRYPTED_DATA["content"]["data"]["dob"];

        /*  */
        /*  */
        /* VERIFY THE USER */
        /* MANAGE SCOPE FOR ERRORS (Refer - https://doc.rimit.co/account/account-fetch#response-code) */
        /*  */
        /*  */

        /*  */
        /* EG FOR FAILED RESPONSE : FIND USER, IF NOT FOUND, SEND RESPONSE AS FAILED */
        $FIND_USER = true;
        if (!$FIND_USER) {
            $result = array(
                'code' => RESULT_CODE_MOBILE_NUMBER_NOT_FOUND,
                'status' => STATUS_FAILED,
                'message' => RESULT_MESSAGE_E2014,
            );
            $data = array();
            $head['HTTP_CODE'] = HTTP_CODE_SUCCESS;
            return response_success($head, $result, $data, $ENCRYPTION_KEY);
        }
        /*  */

        /*  */
        /* READ ALL ACCOUNTS OF THE USER IN ACCOUNTS DATA */
        $ACCOUNTS_DATA = array(
                array(
                'account_name' => 'ATHISH BALU',
                'account_number' => '11223333',
                'branch_code' => 'BR001',
                'branch_name' => 'KANNUR',
                'account_type' => 'SAVING_ACCOUNT',
                'account_class' => 'SAVING',
                'txn_amount_limit' => '5000',
                'account_status' => 'ACTIVE',
                'account_opening_date' => '2020-09-01',

                'is_debit_allowed' => true,
                'is_credit_allowed' => true,
                'is_cash_debit_allowed' => true,
                'is_cash_credit_allowed' => true,
            ),
                array(
                'account_name' => 'ATHISH BALU K',
                'account_number' => '613274841345',
                'branch_code' => 'BR002',
                'branch_name' => 'ERNAKULAM',
                'account_type' => 'GOLD_LOAN',
                'account_class' => 'GOLD',
                'txn_amount_limit' => '200000',

                'account_status' => 'ACTIVE',
                'account_opening_date' => '2020-12-10',

                'is_debit_allowed' => true,
                'is_credit_allowed' => true,
                'is_cash_debit_allowed' => false,
                'is_cash_credit_allowed' => false,
            )
        );
        /*  */

        /*  */
        /* ASSIGN DATA RECEIVED FROM ACCOUNTS_DATA ARRAY */
        if (count($ACCOUNTS_DATA) > 0) {
            foreach ($ACCOUNTS_DATA as $account) {
                $details = array(
                    'account_name' => $account['account_name'],
                    'account_number' => $account['account_number'],
                    'branch_code' => $account['branch_code'],
                    'branch_name' => $account['branch_name'],
                    'account_type' => $account['account_type'],
                    'account_class' => $account['account_class'],
                    'txn_amount_limit' => $account['txn_amount_limit'],
                    'account_status' => $account['account_status'],
                    'account_opening_date' => $account['account_opening_date'],

                    'is_debit_allowed' => $account['is_debit_allowed'],
                    'is_credit_allowed' => $account['is_credit_allowed'],
                    'is_cash_debit_allowed' =>
                    $account['is_cash_debit_allowed'],
                    'is_cash_credit_allowed' =>
                    $account['is_cash_credit_allowed'],
                );

                $USER_ACCOUNTS[] = $details;
            }
        }

        $result = array(
            'code' => RESULT_CODE_SUCCESS,
            'status' => STATUS_SUCCESS,
            'message' => RESULT_MESSAGE_E1001,
        );
        $data = array(
            'accounts' => $USER_ACCOUNTS,
        );
        $head['HTTP_CODE'] = HTTP_CODE_SUCCESS;
        return response_success($head, $result, $data, $ENCRYPTION_KEY);

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
;



?>