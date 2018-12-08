<?php

/*!
 * ifsoft.co.uk engine v1.0
 *
 * http://ifsoft.com.ua, http://ifsoft.co.uk
 * qascript@ifsoft.co.uk
 *
 * Copyright 2012-2015 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
 */

if (!empty($_POST)) {

    $clientId = isset($_POST['clientId']) ? $_POST['clientId'] : 0;

    $accountId = isset($_POST['accountId']) ? $_POST['accountId'] : 0;
    $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

    $postId = isset($_POST['postId']) ? $_POST['postId'] : 0;

    $postText = isset($_POST['postText']) ? $_POST['postText'] : '';
    $postImg = isset($_POST['postImg']) ? $_POST['postImg'] : '';

    $clientId = helper::clearInt($clientId);
    $accountId = helper::clearInt($accountId);

    $postId = helper::clearInt($postId);

    $postText = preg_replace( "/[\r\n]+/", "<br>", $postText); //replace all new lines to one new line
    $postText  = preg_replace('/\s+/', ' ', $postText);        //replace all white spaces to one space

    $postText = helper::escapeText($postText);

    $postImg = helper::clearText($postImg);
    $postImg = helper::escapeText($postImg);

    $result = array("error" => true,
                    "error_code" => ERROR_UNKNOWN);

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    $posts = new post($dbo);
    $posts->setRequestFrom($accountId);

    $postInfo = $posts->info($postId);

    if ($postInfo['error'] === true) {

        return $result;
    }

    if ($postInfo['fromUserId'] != $accountId) {

        return $result;
    }

    $result = $posts->edit($postId, $postText, $postImg);

    echo json_encode($result);
    exit;
}
