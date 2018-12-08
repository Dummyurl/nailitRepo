<?php

/*!
 * ifsoft.co.uk engine v1.0
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * raccoonsquare@gmail.com
 *
 * Copyright 2012-2018 Demyanchuk Dmitry (raccoonsquare@gmail.com)
 */

if (!empty($_POST)) {

    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : 0;
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $gcm_regId = isset($_POST['gcm_regId']) ? $_POST['gcm_regId'] : '';
    $ios_fcm_regId = isset($_POST['ios_fcm_regId']) ? $_POST['ios_fcm_regId'] : '';

    $android_msg_fcm_regid = isset($_POST['android_msg_fcm_regid']) ? $_POST['android_msg_fcm_regid'] : '';
    $ios_msg_fcm_regid = isset($_POST['ios_msg_fcm_regid']) ? $_POST['ios_msg_fcm_regid'] : '';

    $gcm_regId = helper::clearText($gcm_regId);
    $gcm_regId = helper::escapeText($gcm_regId);

    $ios_fcm_regId = helper::clearText($ios_fcm_regId);
    $ios_fcm_regId = helper::escapeText($ios_fcm_regId);

    $android_msg_fcm_regid = helper::clearText($android_msg_fcm_regid);
    $android_msg_fcm_regid = helper::escapeText($android_msg_fcm_regid);

    $ios_msg_fcm_regid = helper::clearText($ios_msg_fcm_regid);
    $ios_msg_fcm_regid = helper::escapeText($ios_msg_fcm_regid);

    $result = array("error" => true,
                    "error_code" => ERROR_UNKNOWN);

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    $account = new account($dbo, $accountId);

    if (strlen($ios_fcm_regId) != 0) {

        $result = $account->set_ios_fcm_regId($ios_fcm_regId);
    }

    if (strlen($ios_msg_fcm_regid) != 0) {

        $result = $account->set_ios_msg_fcm_regId($ios_msg_fcm_regid);
    }

    if (strlen($android_msg_fcm_regid) != 0) {

        $result = $account->set_android_msg_fcm_regId($android_msg_fcm_regid);
    }

    if (strlen($gcm_regId) != 0) {

        $result = $account->setGCM_regId($gcm_regId);
    }

    echo json_encode($result);
    exit;
}
