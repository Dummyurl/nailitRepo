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

    $accessMode = isset($_POST['accessMode']) ? $_POST['accessMode'] : 0;

    $comment = isset($_POST['comment']) ? $_POST['comment'] : "";
    $videoUrl = isset($_POST['videoUrl']) ? $_POST['videoUrl'] : "";
    $imgUrl = isset($_POST['imgUrl']) ? $_POST['imgUrl'] : "";

    $clientId = helper::clearInt($clientId);
    $accountId = helper::clearInt($accountId);

    $accessMode = helper::clearInt($accessMode);

    $comment = helper::clearText($comment);

    $comment = preg_replace( "/[\r\n]+/", "<br>", $comment); //replace all new lines to one new line
    $comment  = preg_replace('/\s+/', ' ', $comment);        //replace all white spaces to one space

    $comment = helper::escapeText($comment);

    $videoUrl = helper::clearText($videoUrl);
    $videoUrl = helper::escapeText($videoUrl);

    $imgUrl = helper::clearText($imgUrl);
    $imgUrl = helper::escapeText($imgUrl);

    $result = array("error" => true,
                    "error_code" => ERROR_UNKNOWN);

    $auth = new auth($dbo);

    if (!$auth->authorize($accountId, $accessToken)) {

        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
    }

    $video = new video($dbo);
    $video->setRequestFrom($accountId);

    $result = $video->add($accessMode, $comment, $videoUrl, $imgUrl);

    echo json_encode($result);
    exit;
}
