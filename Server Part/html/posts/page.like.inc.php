<?php

    /*!
     * ifsoft.co.uk v1.1
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@mail.ru
     *
     * Copyright 2012-2017 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    $accountId = auth::getCurrentUserId();
    $postId = helper::clearInt($request[2]);

    if (!empty($_POST)) {

        $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $auth = new auth($dbo);

        if (!$auth->authorize($accountId, $accessToken)) {

            api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
        }

        $post = new post($dbo);
        $post->setRequestFrom($accountId);

        $postInfo = $post->info($postId);

        if ($postInfo['error'] === false) {

            $result = $post->like($postId, $accountId);

            $postInfo['likesCount'] = $result['likesCount'];
            $postInfo['myLike'] = $result['myLike'];
        }

        echo json_encode($result);
        exit;
    }
