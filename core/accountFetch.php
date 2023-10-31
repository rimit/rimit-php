<?php
require_once('../utilities/config.php');
require_once('../utilities/commonCodes.php');
require_once('../utilities/response.php');
require_once('../utilities/crypto.php');
require_once('../utilities/request.php');

// FETCH ACCOUNT
function accountFetch($req)
{
    // print_r('------------------<br>');
    // print_r('REQUEST : accountFetch<br>');
    // print_r('------------------<br>');
    // print_r($req['body']);
    // print_r('------------------');

    $head = array(
        'api' => 'accountFetch',
        'apiVersion' => 'V1',
        'timeStamp' => date("Y-m-d H:i:s A")
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


        $USER_DATA = array(
            "mobile" => $USER_MOBILE,
            "country_code" => $USER_CC,
        );


        // IF SUCCESSFUL, CALL addAccount
        addAccount($USER_DATA);

        // SUCCESS RESPONSE
        $result = array(
            "code" => RESULT_CODE_SUCCESS,
            "status" => STATUS_SUCCESS,
            "message" => RESULT_MESSAGE_E1001,
        );
        $data = array();

        $head["HTTP_CODE"] = HTTP_CODE_SUCCESS;

        return response_success($head, $result, $data, $ENCRYPTION_KEY);
    } catch (Exception $e) {
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

//
// ADD ACCOUNT
function addAccount($userData)
{
    // print_r('<br>------------------<br>');
    // print_r('REQUEST : AddAccount');
    // print_r('<br>------------------<br>');

    try {
        /*  */
        /* ASSIGN ENCRYPTION_KEY, API_KEY & API_ID OF ENTITY */
        $ENCRYPTION_KEY = '';
        $AUTH_API_ID = '';
        $AUTH_API_KEY = '';
        /*  */

        // ADD_ACCOUNT REQUEST URL
        $ADD_ACCOUNT_URL = BASE_URL . '/account/add';

        $ADD_ACCOUNT_HEAD = array(
            "api" => 'accountAdd',
            "apiVersion" => 'V1',
            "timeStamp" => date("Y-m-d H:i:s A"),
            "auth" => array(
                "API_ID" => $AUTH_API_ID,
                "API_KEY" => $AUTH_API_KEY,
            ),
        );

        /*  */
        /* ASSIGN USER DATA BASED ON REQUEST DATA ON accountFetch */
        $USER_DATA = array(
            "mobile" => $userData->mobile,
            "country_code" => $userData->country_code,
        );
        /*  */

        /*  */
        /* READ ALL ACCOUNTS OF THE USER IN ACCOUNTS DATA */
        $ACCOUNTS_DATA = array(
            array(
                "account_name" => '',
                "account_number" => '',
                "branch_code" => '',
                "branch_name" => '',

                "account_type" => '',
                "account_class" => '',
                "account_status" => '',
                "account_opening_date" => '',
                "account_currency" => '',
                "account_daily_limit" => '',

                "is_debit_allowed" => true,
                "is_credit_allowed" => true,
                "is_cash_debit_allowed" => true,
                "is_cash_credit_allowed" => true,
                "salt" => '',
            )
        );

        /*  */
        /*  */
        /* ASSIGN DATA RECEIVED FROM ACCOUNTS_DATA ARRAY */
        $USER_ACCOUNTS = array();
        if (count($ACCOUNTS_DATA) > 0) {
            foreach ($ACCOUNTS_DATA as $account) {
                $details = array(
                    'account_name' => $account['account_name'],
                    'account_number' => $account['account_number'],
                    'branch_code' => $account['branch_code'],
                    'branch_name' => $account['branch_name'],

                    'account_type' => $account['account_type'],
                    'account_class' => $account['account_class'],
                    'account_status' => $account['account_status'],
                    'account_opening_date' => $account['account_opening_date'],
                    'account_currency' => $account['account_currency'],
                    'account_daily_limit' => $account['account_daily_limit'],

                    'is_debit_allowed' => $account['is_debit_allowed'],
                    'is_credit_allowed' => $account['is_credit_allowed'],
                    'is_cash_debit_allowed' =>
                    $account['is_cash_debit_allowed'],
                    'is_cash_credit_allowed' =>
                    $account['is_cash_credit_allowed'],
                    'auth_salt' => $account['salt'],
                );

                $USER_ACCOUNTS[] = $details;
            }
        }

        $ADD_ACCOUNTS_DATA = array(
            "user" => $USER_DATA,
            "accounts" => $USER_ACCOUNTS,
        );

        // IF THE ALL ACCOUNTS READ SUCCESSFULLY
        $ADD_ACCOUNT_RESULT = array(
            'code' => RESULT_CODE_SUCCESS,
            'status' => STATUS_SUCCESS,
            'message' => RESULT_MESSAGE_E1001,
        );

        $ADD_ACCOUNT_CONFIRM = confirmRequest($ADD_ACCOUNT_HEAD, $ADD_ACCOUNT_RESULT, $ADD_ACCOUNTS_DATA, $ADD_ACCOUNT_URL, $ENCRYPTION_KEY);

        if (!$ADD_ACCOUNT_CONFIRM) {
            // print_r( 'ADD_ACCOUNT_CONFIRM - REQUEST STATUS<br>');
            // print_r( $ADD_ACCOUNT_CONFIRM);
            return true;
        }

        // print_r('<br>*****************<br>');
        // print_r('ADD_ACCOUNT_CONFIRM - RESPONSE<br>');
        // print_r($ADD_ACCOUNT_CONFIRM);
        // print_r('<br>*****************<br>');

        /*  */
        /*  */

        /* MANAGE RECEIVED RESPONSE */
        /*  */

        /*  */
        /*  */
        return true;

    } catch (Exception $e) {
        echo 'Message: ' . $e->getMessage();
    }
}

?>