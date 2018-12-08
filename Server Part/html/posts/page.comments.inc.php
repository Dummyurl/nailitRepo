<?php

    /*!
     * ifsoft.co.uk v1.0
     *
     * http://ifsoft.com.ua, http://ifsoft.co.uk
     * qascript@ifsoft.co.uk
     *
     * Copyright 2012-2015 Demyanchuk Dmitry (https://vk.com/dmitry.demyanchuk)
     */

    $accountId = auth::getCurrentUserId();
    $postId = helper::clearInt($request[2]);

    if (!empty($_POST)) {

        $accessToken = isset($_POST['accessToken']) ? $_POST['accessToken'] : '';

        $act = isset($_POST['act']) ? $_POST['act'] : '';

        $commentText = isset($_POST['commentText']) ? $_POST['commentText'] : '';
        $commentId = isset($_POST['commentId']) ? $_POST['commentId'] : 0;

        $replyToUserId = isset($_POST['replyToUserId']) ? $_POST['replyToUserId'] : 0;

        $commentId = helper::clearInt($commentId);
        $replyToUserId = helper::clearInt($replyToUserId);

        $commentText = helper::clearText($commentText);
        $commentText = helper::escapeText($commentText);

        $result = array("error" => true,
                        "error_code" => ERROR_UNKNOWN);

        $auth = new auth($dbo);

        $post = new post($dbo);
        $post->setRequestFrom($accountId);

        $postInfo = $post->info($postId);

        if ($postInfo['error'] === false) {

            switch ($act) {

                case 'create': {

                    if (!$auth->authorize($accountId, $accessToken)) {

                        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
                    }

                    if (strlen($commentText) != 0) {

                        $blacklist = new blacklist($dbo);
                        $blacklist->setRequestFrom($postInfo['fromUserId']);

                        if ($blacklist->isExists($accountId)) {

                            exit;
                        }

                        if ($postInfo['allowComments'] == 0) {

                            exit;
                        }

                        $comments = new comments($dbo);
                        $comments->setLanguage($LANG['lang-code']);
                        $comments->setRequestFrom(auth::getCurrentUserId());

                        $notifyId = 0;

                        $data = $comments->create($postId, $commentText, $notifyId, $replyToUserId);

                        ob_start();

                        draw::comment($data['comment'], $postInfo, $LANG);

                        $result['html'] = ob_get_clean();

                        echo json_encode($result);

                        exit;
                    }

                    break;
                }

                case 'more': {

                    $comments = new comments($dbo);
                    $comments->setLanguage($LANG['lang-code']);
                    $comments->setRequestFrom(auth::getCurrentUserId());

                    $data = $comments->get($postId, $commentId);

                    $data['comments'] = array_reverse($data['comments'], false);

                    ob_start();

                    foreach ($data['comments'] as $key => $value) {

                        draw::comment($value, $postInfo, $LANG);
                    }

                    $result['html'] = ob_get_clean();

                    echo json_encode($result);

                    exit;

                    break;
                }

                case 'remove': {

                    if (!$auth->authorize($accountId, $accessToken)) {

                        api::printError(ERROR_ACCESS_TOKEN, "Error authorization.");
                    }

                    $comments = new comments($dbo);
                    $comments->setLanguage($LANG['lang-code']);
                    $comments->setRequestFrom(auth::getCurrentUserId());

                    $commentInfo = $comments->info($commentId);

                    if ($commentInfo['fromUserId'] == auth::getCurrentUserId() || $postInfo['fromUserId'] == auth::getCurrentUserId()) {

                        $notify = new notify($dbo);
                        $notify->remove($commentInfo['notifyId']);
                        unset($notify);

                        $comments->remove($commentId);
                    }

                    break;
                }

                default: {

                    exit;
                }
            }
        }

        echo json_encode($result);
        exit;
    }
