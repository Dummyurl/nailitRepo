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

    $clientId = isset($_POST['clientId']) ? $_POST['clientId'] : 0;

    $gcm_regId = isset($_POST['gcm_regId']) ? $_POST['gcm_regId'] : '';

    $ios_fcm_regId = isset($_POST['ios_fcm_regId']) ? $_POST['ios_fcm_regId'] : '';

    $android_msg_fcm_regid = isset($_POST['android_msg_fcm_regid']) ? $_POST['android_msg_fcm_regid'] : '';
    $ios_msg_fcm_regid = isset($_POST['ios_msg_fcm_regid']) ? $_POST['ios_msg_fcm_regid'] : '';

    $facebookId = isset($_POST['facebookId']) ? $_POST['facebookId'] : '';

    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $referrer = isset($_POST['referrer']) ? $_POST['referrer'] : 0;

    $language = isset($_POST['language']) ? $_POST['language'] : '';

    $clientId = helper::clearInt($clientId);

    $referrer = helper::clearInt($referrer);

    $facebookId = helper::clearText($facebookId);

    $android_msg_fcm_regid = helper::clearText($android_msg_fcm_regid);
    $android_msg_fcm_regid = helper::escapeText($android_msg_fcm_regid);

    $ios_msg_fcm_regid = helper::clearText($ios_msg_fcm_regid);
    $ios_msg_fcm_regid = helper::escapeText($ios_msg_fcm_regid);

    $gcm_regId = helper::clearText($gcm_regId);
    $ios_fcm_regId = helper::clearText($ios_fcm_regId);
    $username = helper::clearText($username);
    $fullname = helper::clearText($fullname);
    $password = helper::clearText($password);
    $email = helper::clearText($email);
    $language = helper::clearText($language);

    $facebookId = helper::escapeText($facebookId);
    $gcm_regId = helper::escapeText($gcm_regId);
    $ios_fcm_regId = helper::escapeText($ios_fcm_regId);
    $username = helper::escapeText($username);
    $fullname = helper::escapeText($fullname);
    $password = helper::escapeText($password);
    $email = helper::escapeText($email);
    $language = helper::escapeText($language);

    if ($clientId != CLIENT_ID) {

        api::printError(ERROR_UNKNOWN, "Error client Id.");
    }

    $result = array("error" => true);

    $account = new account($dbo);
    $result = $account->signup($username, $fullname, $password, $email, $language);
    unset($account);

    if ($result['error'] === false) {

        $account = new account($dbo);
        $account->setState(ACCOUNT_STATE_ENABLED);
        $account->setLastActive();
        $result = $account->signin($username, $password);
        unset($account);

        if ($result['error'] === false) {

            $auth = new auth($dbo);
            $result = $auth->create($result['accountId'], $clientId);

            if ($result['error'] === false) {

                $account = new account($dbo, $result['accountId']);

                // refsys

                if ($referrer != 0) {

                    $ref = new refsys($dbo);
                    $ref->setRequestFrom($account->getId());
                    $ref->setReferrer($referrer);

                    $ref->setReferralsCount($referrer, $ref->getReferralsCount($referrer));

                    $ref->addSignupBonus($referrer);

                    unset($ref);
                }

                // Facebook

                if (strlen($facebookId) != 0) {

                    $helper = new helper($dbo);

                    if ($helper->getUserIdByFacebook($facebookId) == 0) {

                        $account->setFacebookId($facebookId);
                    }

                } else {

                    $account->setFacebookId("");
                }

                if (strlen($gcm_regId) != 0) {

                    $account->setGCM_regId($gcm_regId);
                }

                if (strlen($ios_fcm_regId) != 0) {

                    $account->set_ios_fcm_regId($ios_fcm_regId);
                }

                if (strlen($ios_msg_fcm_regid) != 0) {

                    $account->set_ios_msg_fcm_regId($ios_msg_fcm_regid);
                }

                if (strlen($android_msg_fcm_regid) != 0) {

                    $account->set_android_msg_fcm_regId($android_msg_fcm_regid);
                }

                $result['account'] = array();

                array_push($result['account'], $account->get());
            }
        }
    }

    echo json_encode($result);
    exit;
}
