<?php

/*!
 * ifsoft.co.uk engine v1.0
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * qascript@ifsoft.co.uk
 *
 * Copyright 2012-2017 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
 */

if (!empty($_POST)) {

    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : 0;
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $allowShowMyPhotos = isset($_POST['allowShowMyPhotos']) ? $_POST['allowShowMyPhotos'] : 0;
    $allowShowMyVideos = isset($_POST['allowShowMyVideos']) ? $_POST['allowShowMyVideos'] : 0;
    $allowShowMyGifts = isset($_POST['allowShowMyGifts']) ? $_POST['allowShowMyGifts'] : 0;
    $allowShowMyFriends = isset($_POST['allowShowMyFriends']) ? $_POST['allowShowMyFriends'] : 0;
    $allowShowMyInfo = isset($_POST['allowShowMyInfo']) ? $_POST['allowShowMyInfo'] : 0;

    $allowShowMyPhotos = helper::clearInt($allowShowMyPhotos);
    $allowShowMyGifts = helper::clearInt($allowShowMyGifts);
    $allowShowMyFriends = helper::clearInt($allowShowMyFriends);
    $allowShowMyVideos = helper::clearInt($allowShowMyVideos);
    $allowShowMyInfo = helper::clearInt($allowShowMyInfo);

    $result = array("error" => true,
                    "error_code" => ERROR_UNKNOWN);

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    $result = array("error" => false,
                    "error_code" => ERROR_SUCCESS);

    $account = new account($dbo, $accountId);

    $account->setPrivacySettings($allowShowMyPhotos, $allowShowMyGifts, $allowShowMyFriends, $allowShowMyVideos, $allowShowMyInfo);

    $result = $account->getPrivacySettings();

    echo json_encode($result);
    exit;
}
