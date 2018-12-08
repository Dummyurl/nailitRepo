<?php

    /*!
     * ifsoft.co.uk v1.1
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2017 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    $toUserId = $helper->getUserId($request[0]);
    $accountId = auth::getCurrentUserId();
    $accessToken = auth::getAccessToken();

    if (!$auth->authorize($accountId, $accessToken)) {

        exit;
    }

    if (!empty($_POST)) {

        $token = isset($_POST['authenticity_token']) ? $_POST['authenticity_token'] : '';

        $itemImg = isset($_POST['itemImg']) ? $_POST['itemImg'] : '';
        $itemPreviewImg = isset($_POST['itemPreviewImg']) ? $_POST['itemPreviewImg'] : '';
        $itemOriginImg = isset($_POST['itemOriginImg']) ? $_POST['itemOriginImg'] : '';

        $itemImg = helper::clearText($itemImg);
        $itemImg = helper::escapeText($itemImg);

        $itemPreviewImg = helper::clearText($itemPreviewImg);
        $itemPreviewImg = helper::escapeText($itemPreviewImg);

        $itemOriginImg = helper::clearText($itemOriginImg);
        $itemOriginImg = helper::escapeText($itemOriginImg);

        $result = array("error" => true,
                        "error_description" => "token");

        if (auth::getAuthenticityToken() !== $token) {

            echo json_encode($result);
        }

        $photos = new photos($dbo);
        $photos->setRequestFrom($accountId);
        $result = $photos->add(0, "", $itemOriginImg, $itemPreviewImg, $itemImg);

        ob_start();

        draw::galleryItem($result['photo'], $LANG, $helper);

        $result['html'] = ob_get_clean();

        echo json_encode($result);
        exit;
    }

