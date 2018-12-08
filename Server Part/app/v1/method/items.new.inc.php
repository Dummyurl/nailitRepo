<?php

/*!
 * ifsoft.co.uk engine v1.0
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * qascript@ifsoft.co.uk
 *
 * Copyright 2012-2016 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
 */

if (!empty($_POST)) {

    $clientId = isset($_POST['clientId']) ? $_POST['clientId'] : 0;

    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : 0;
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $groupId = isset($_POST['groupId']) ? $_POST['groupId'] : 0;
    $postMode = isset($_POST['postMode']) ? $_POST['postMode'] : 0;

    $rePostId = isset($_POST['rePostId']) ? $_POST['rePostId'] : 0;

    $postText = isset($_POST['postText']) ? $_POST['postText'] : '';
    $postImg = isset($_POST['postImg']) ? $_POST['postImg'] : '';

    $postArea = isset($_POST['postArea']) ? $_POST['postArea'] : '';
    $postCountry = isset($_POST['postCountry']) ? $_POST['postCountry'] : '';
    $postCity = isset($_POST['postCity']) ? $_POST['postCity'] : '';
    $postLat = isset($_POST['postLat']) ? $_POST['postLat'] : '';
    $postLng = isset($_POST['postLng']) ? $_POST['postLng'] : '';

    $clientId = helper::clearInt($clientId);
    $accountId = helper::clearInt($accountId);

    $groupId = helper::clearInt($groupId);
    $postMode = helper::clearInt($postMode);

    $rePostId = helper::clearInt($rePostId);

    $postText = preg_replace( "/[\r\n]+/", "<br>", $postText); //replace all new lines to one new line
    $postText  = preg_replace('/\s+/', ' ', $postText);        //replace all white spaces to one space

    $postText = helper::escapeText($postText);

    $postImg = helper::clearText($postImg);
    $postImg = helper::escapeText($postImg);

    $postArea = helper::clearText($postArea);
    $postArea = helper::escapeText($postArea);

    $postCountry = helper::clearText($postCountry);
    $postCountry = helper::escapeText($postCountry);

    $postCity = helper::clearText($postCity);
    $postCity = helper::escapeText($postCity);

    $postLat = helper::clearText($postLat);
    $postLat = helper::escapeText($postLat);

    $postLng = helper::clearText($postLng);
    $postLng = helper::escapeText($postLng);

    $result = array("error" => true,
                    "error_code" => ERROR_UNKNOWN);

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    $fromUserId = $accountId;

    if ($groupId != 0) {

        $m = new profile($dbo, $groupId);
        $m->setRequestFrom(auth::getCurrentUserId());

        $mInfo = $m->get();

        if ($mInfo['accountType'] == ACCOUNT_TYPE_GROUP || $mInfo['accountType'] == ACCOUNT_TYPE_PAGE) {

            $groupId = $mInfo['id'];
            $postMode = 0;

            if ($mInfo['accountAuthor'] == $accountId) {

                $fromUserId = $mInfo['id'];

            } else {

                $fromUserId = $accountId;
            }

        } else {

            $groupId = 0;
            $fromUserId = $accountId;
        }
    }

    $posts = new post($dbo);
    $posts->setRequestFrom($fromUserId);

    $result = $posts->add($postMode, $postText, $postImg, $rePostId, $groupId, $postArea, $postCountry, $postCity, $postLat, $postLng);

    echo json_encode($result);
    exit;
}
