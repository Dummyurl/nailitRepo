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

        $postText = isset($_POST['postText']) ? $_POST['postText'] : '';
        $postImg = isset($_POST['postImg']) ? $_POST['postImg'] : '';
        $rePostId = isset($_POST['rePostId']) ? $_POST['rePostId'] : 0;
        $postMode = isset($_POST['mode_checkbox']) ? $_POST['mode_checkbox'] : '';

        $postText = preg_replace( "/[\r\n]+/", "<br>", $postText); //replace all new lines to one new line
        $postText  = preg_replace('/\s+/', ' ', $postText);        //replace all white spaces to one space

        // $postText = helper::clearText($postText);
        $postText = helper::escapeText($postText);

        $postImg = helper::clearText($postImg);
        $postImg = helper::escapeText($postImg);

        $rePostId = helper::clearInt($rePostId);

        $postMode = helper::clearText($postMode);
        $postMode = helper::escapeText($postMode);

        if ($postMode === "on") {

            $postMode = 1;

        } else {

            $postMode = 0;
        }

        $result = array("error" => true,
                        "error_description" => "token");

        if (auth::getAuthenticityToken() !== $token) {

            echo json_encode($result);
        }

        $mId = $helper->getUserId($request[0]);

        $m = new profile($dbo, $mId);
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

        $post = new post($dbo);
        $post->setRequestFrom($fromUserId);
        $result = $post->add($postMode, $postText, $postImg, $rePostId, $groupId);

        if ($result['error'] === false) {

            ob_start();

            if ($groupId == 0) {

                draw::post($result['post'], $LANG, $helper, false);

            } else {

                draw::post($result['post'], $LANG, $helper, false);
            }

            $result['html'] = ob_get_clean();

            if ($groupId == 0) {

                $profile = new profile($dbo, $fromUserId);

                $result['postsCount'] = $profile->getPostsCount();

            } else {

                $group = new group($dbo, $groupId);
                $group->setRequestFrom($accountId);

                $result['postsCount'] = $group->getPostsCount();

                unset($group);
            }
        }

        echo json_encode($result);
        exit;
    }
