<?php
require_once('crypto.php');

function hashData($data, $salt)
{
    try {
        $iterations = 2048;
        $hash = hash_pbkdf2("sha512", $data, $salt, $iterations, 64);
        return $hash;
    } catch (Exception $e) {
        return false;
    }
}

function hashVerify($data, $hash, $salt)
{
    try {
        $newHash = hashData($data, $salt);

        return $newHash === $hash;
    } catch (Exception $e) {
        return false;
    }
}
;
?>